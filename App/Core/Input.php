<?php

namespace App\Core;

class Input
{
    public static function get(string $key = null, $default = null)
    {
        if (count($_GET) === 0) {
            parse_str(file_get_contents('php://input'), $_GET);
        }

        if ($key) {
            return $_GET[$key] ?? $default;
        }
        return $_GET;
    }

    public static function post(string $key = null, $default = null)
    {

        if (count($_POST) === 0) {
            parse_str(file_get_contents('php://input'), $_POST);
        }

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

    public static function file(string $key = null, $default = null)
    {
        if (count($_FILES) === 0) {
            $input = file_get_contents('php://input');
            $boundary = substr($input, 0, strpos($input, "\r\n"));
            $parts = array_slice(explode($boundary, $input), 1);
            $parts = array_map(function ($part) {
                return ltrim($part, "\r\n");
            }, $parts);
            $parts = array_filter($parts, function ($part) {
                return !empty($part);
            });
            $parts = array_map(function ($part) {
                return array_reduce(explode("\r\n", $part), function ($data, $line) {
                    if (empty($data)) {
                        $data = [
                            'headers' => [],
                            'body' => ''
                        ];
                    }
                    if (strpos($line, ': ') !== false) {
                        [$header, $value] = explode(': ', $line);
                        $data['headers'][$header] = $value;
                    } else {
                        $data['body'] .= $line;
                    }
                    return $data;
                });
            }, $parts);
            $parts = array_map(function ($part) {
                $data = [];
                if (isset($part['headers']['Content-Disposition'])) {
                    $filename = null;
                    $tmp_name = null;
                    preg_match('/name="([^"]+)"/', $part['headers']['Content-Disposition'], $match);
                    $data['name'] = $match[1];
                    if (isset($part['headers']['Content-Type'])) {
                        $data['type'] = $part['headers']['Content-Type'];
                    }
                    if (isset($part['headers']['Content-Length'])) {
                        $data['size'] = $part['headers']['Content-Length'];
                    }
                    if (isset($part['headers']['Content-Transfer-Encoding'])) {
                        $data['error'] = $part['headers']['Content-Transfer-Encoding'];
                    }
                    if (preg_match('/filename="([^"]+)"/', $part['headers']['Content-Disposition'], $match)) {
                        $filename = $match[1];
                        $tmp_name = tempnam(ini_get('upload_tmp_dir'), 'php');
                        file_put_contents($tmp_name, $part['body']);
                    }
                    $data['parameter'] = $data['name'];
                    $data['tmp_name'] = $tmp_name;
                    $data['error'] = $filename ? UPLOAD_ERR_OK : UPLOAD_ERR_NO_FILE;
                    $data['name'] = $filename;
                    $data['size'] = filesize($tmp_name);
                }
                return $data;
            }, $parts);
            $parts = array_filter($parts);

            // rebase array with name as index

            $parts = array_reduce($parts, function ($data, $part) {
                if (isset($part['parameter'])) {
                    $data[$part['parameter']] = $part;
                }
                return $data;
            }, []);

            $parts = array_map(function ($part) {
                unset($part['parameter']);
                return $part;
            }, $parts);

            $_FILES = $parts;
        }

        if ($key) {
            return $_FILES[$key] ?? $default;
        }

        return $_FILES;
    }
}
