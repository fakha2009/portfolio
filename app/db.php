<?php

declare(strict_types=1);

function cv_db(): ?PDO
{
    static $pdo = null;
    static $attempted = false;

    if ($attempted) {
        return $pdo;
    }

    $attempted = true;

    if (cv_config('app.install_mode', false)) {
        return null;
    }

    $host     = (string) cv_config('db.host');
    $port     = (int)    cv_config('db.port', 5432);
    $database = (string) cv_config('db.database');
    $sslmode  = (string) cv_config('db.sslmode', 'require');

    try {
        $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s;sslmode=%s', $host, $port, $database, $sslmode);
        $pdo = new PDO($dsn, (string) cv_config('db.username'), (string) cv_config('db.password'), [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $exception) {
        $GLOBALS['cv_db_error'] = $exception->getMessage();
        cv_log('db', 'Database connection failed', ['error' => $exception->getMessage()]);
        $pdo = null;
    }

    return $pdo;
}

function cv_database_ready(): bool
{
    return cv_db() instanceof PDO;
}

function cv_query(string $sql, array $params = []): ?PDOStatement
{
    $pdo = cv_db();

    if (!$pdo) {
        return null;
    }

    $statement = $pdo->prepare($sql);
    $statement->execute($params);

    return $statement;
}

function cv_fetch_one(string $sql, array $params = []): ?array
{
    $statement = cv_query($sql, $params);

    return $statement ? ($statement->fetch() ?: null) : null;
}

function cv_fetch_all(string $sql, array $params = []): array
{
    $statement = cv_query($sql, $params);

    return $statement ? $statement->fetchAll() : [];
}

function cv_execute(string $sql, array $params = []): bool
{
    $statement = cv_query($sql, $params);

    return $statement !== null;
}

function cv_last_insert_id(): int
{
    $pdo = cv_db();

    if (!$pdo) {
        return 0;
    }

    try {
        $row = $pdo->query('SELECT lastval()')->fetch(PDO::FETCH_NUM);
        return (int) ($row[0] ?? 0);
    } catch (\Throwable) {
        return 0;
    }
}

function cv_db_error(): ?string
{
    return $GLOBALS['cv_db_error'] ?? null;
}

function cv_register_pg_session_handler(): void
{
    $handler = new class implements SessionHandlerInterface {
        public function open(string $path, string $name): bool { return true; }
        public function close(): bool { return true; }

        public function read(string $id): string|false
        {
            $row = cv_fetch_one(
                'SELECT session_data FROM php_sessions WHERE session_id = :id AND expires_at > NOW() LIMIT 1',
                ['id' => $id]
            );
            return $row ? (string) $row['session_data'] : '';
        }

        public function write(string $id, string $data): bool
        {
            $lifetime = (int) ini_get('session.gc_maxlifetime') ?: 7200;
            return cv_execute(
                'INSERT INTO php_sessions (session_id, session_data, expires_at)
                 VALUES (:id, :data, NOW() + INTERVAL \'' . $lifetime . ' seconds\')
                 ON CONFLICT (session_id) DO UPDATE SET
                     session_data = EXCLUDED.session_data,
                     expires_at   = EXCLUDED.expires_at',
                ['id' => $id, 'data' => $data]
            );
        }

        public function destroy(string $id): bool
        {
            return cv_execute('DELETE FROM php_sessions WHERE session_id = :id', ['id' => $id]);
        }

        public function gc(int $max_lifetime): int|false
        {
            cv_execute('DELETE FROM php_sessions WHERE expires_at < NOW()');
            return 1;
        }
    };

    session_set_save_handler($handler, true);
}
