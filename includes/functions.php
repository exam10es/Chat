<?php

declare(strict_types=1);

use Core\Session;

function clean_input(string $data): string
{
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
}

function generate_token(int $length = 32): string
{
    return bin2hex(random_bytes((int) ($length / 2)));
}

function format_date_ar(string $date): string
{
    return date('Y-m-d H:i', strtotime($date));
}

function time_elapsed(string $datetime): string
{
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    $minutes = (int) floor($diff / 60);
    if ($minutes < 60) {
        return $minutes . ' دقيقة';
    }
    $hours = (int) floor($minutes / 60);
    return $hours . ' ساعة';
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash(string $type, string $message): void
{
    Session::set('flash', ['type' => $type, 'message' => $message]);
}

function is_admin(): bool
{
    return (bool) Session::get('admin_id');
}

function csrf_token(): string
{
    $token = Session::get('csrf_token');
    if (!$token) {
        $token = generate_token(32);
        Session::set('csrf_token', $token);
    }
    return $token;
}

function verify_csrf(string $token): bool
{
    return hash_equals((string) Session::get('csrf_token'), $token);
}

function log_error(string $message): void
{
    error_log($message . PHP_EOL, 3, BASE_PATH . '/storage/error.log');
}
