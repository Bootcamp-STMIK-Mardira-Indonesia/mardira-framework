<?php

namespace App\Core;

class Request
{
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function uri(): string
    {
        return trim($_SERVER['REQUEST_URI'], '/');
    }

    public function body(): array
    {
        $body = [];

        if ($this->method() === 'GET') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->method() === 'POST') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }

    public function file(): array
    {
        $file = [];

        if ($this->method() === 'POST') {
            foreach ($_FILES as $key => $value) {
                $file[$key] = $value;
            }
        }

        return $file;
    }

    public static function bearerToken(): string
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        if (preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            return $matches[1];
        }

        return '';
    }

    // request make dynamic property when request post by key and value
    public function __get($key)
    {
        return $this->body()[$key] ?? '';
    }

}
