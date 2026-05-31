<?php

declare(strict_types=1);

// Entry point for Vercel serverless PHP runtime.
// Routes all requests to the project's front controller.
chdir(dirname(__DIR__));
require dirname(__DIR__) . '/index.php';
