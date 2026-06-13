<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (!isset($_SERVER['APP_ENV'])) {
    $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] ?? null;
}

if ($_SERVER['APP_ENV'] !== 'prod') {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}
