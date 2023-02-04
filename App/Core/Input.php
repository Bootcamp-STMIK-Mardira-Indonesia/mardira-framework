<?php

namespace App\Core;

class Input
{
    public static function get(string $key = null, $default = null)
    {
        if ($key) {
            return $_GET[$key] ?? $default;
        }
        return $_GET;
    }

    public static function post(string $key = null, $default = null)
    {
        if ($key) {
            return $_POST[$key] ?? $default;
        }
        return $_POST;
    }

    public static function put(string $key = null, $default = null)
    {
        parse_str(file_get_contents('php://input'), $put);
        if ($key) {
            return $put[$key] ?? $default;
        }
        return $put;
    }

    public static function header(string $key = null, $default = null)
    {
        $headers = getallheaders();
        if ($key) {
            return $headers[$key] ?? $default;
        }
        return $headers;
    }
}