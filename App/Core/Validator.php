<?php

namespace App\Core;

class Validator
{
    public static array $data = [];
    public static array $errors = [];
    public static array $fields;

    public static function validate(array $fields, array $input): self
    {
        self::$fields = $fields;
        self::$data = $input;
        foreach ($fields as $field => $rules) {
            foreach ($rules as $rule => $value) {
                switch ($rule) {
                    case 'required':
                        self::required($field);
                        break;
                    case 'min':
                        self::min($field, $value);
                        break;
                    case 'max':
                        self::max($field, $value);
                        break;
                    case 'email':
                        self::email($field);
                        break;
                    case 'file':
                        self::file($field);
                        break;
                }
            }
        }
        return new self();
    }

    private static function required(string $field): void
    {
        if (!isset(self::$data[$field]) || empty(self::$data[$field])) {
            self::addError($field, 'The ' . $field . ' field is required');
        }
    }

    private static function min(string $field, int $min): void
    {
        if (strlen(self::$data[$field]) < $min) {
            self::addError($field, 'The ' . $field . ' field must be at least ' . $min . ' characters');
        }
    }

    private static function max(string $field, int $max): void
    {
        if (strlen(self::$data[$field]) > $max) {
            self::addError($field, 'The ' . $field . ' field must be at most ' . $max . ' characters');
        }
    }

    private static function email(string $field): void
    {
        if (!filter_var(self::$data[$field], FILTER_VALIDATE_EMAIL)) {
            self::addError($field, 'The ' . $field . ' field must be a valid email');
        }
    }

    private static function file(string $field): void
    {
        if (!isset($_FILES[$field]) || !is_uploaded_file($_FILES[$field]['tmp_name'])) {
            self::addError($field, 'The ' . $field . ' field must be a valid file');
            return;
        }
    }

    private static function addError(string $field, string $message): void
    {
        self::$errors[$field] = $message;
    }

    public static function fails(): bool
    {
        return count(self::$errors) > 0;
    }

    public function errors(): array
    {
        return self::$errors;
    }
}
