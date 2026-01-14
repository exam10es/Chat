<?php

declare(strict_types=1);

namespace Core;

class Validator
{
    private array $errors = [];

    public function required(string $field, mixed $value, string $message): void
    {
        if ($value === null || $value === '') {
            $this->errors[$field][] = $message;
        }
    }

    public function email(string $field, mixed $value, string $message): void
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = $message;
        }
    }

    public function minLength(string $field, string $value, int $min, string $message): void
    {
        if (mb_strlen($value) < $min) {
            $this->errors[$field][] = $message;
        }
    }

    public function maxLength(string $field, string $value, int $max, string $message): void
    {
        if (mb_strlen($value) > $max) {
            $this->errors[$field][] = $message;
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }
}
