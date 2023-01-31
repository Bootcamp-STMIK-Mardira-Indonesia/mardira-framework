<?php

namespace App\Core;

use Dotenv;

class DotEnvKey
{
    public static function get(string $key): string
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        return $_ENV[$key];
    }
}
