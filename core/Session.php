<?php

declare(strict_types=1);

namespace Core;

class Session
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function enforceTimeout(int $timeout): void
    {
        $lastActivity = (int) self::get('last_activity', time());
        if (time() - $lastActivity > $timeout) {
            self::destroy();
        } else {
            self::set('last_activity', time());
        }
    }

    public static function bindClient(string $ip, string $userAgent): void
    {
        if (!self::get('client_ip')) {
            self::set('client_ip', $ip);
            self::set('client_ua', $userAgent);
            return;
        }

        if (self::get('client_ip') !== $ip || self::get('client_ua') !== $userAgent) {
            self::destroy();
        }
    }
}
