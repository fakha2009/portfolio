<?php

declare(strict_types=1);
?>
<?php if ($flashes !== []): ?>
    <div class="toast-stack">
        <?php foreach ($flashes as $flash): ?>
            <div class="toast toast-<?= cv_e($flash['type']) ?>"><?= cv_e($flash['message']) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
