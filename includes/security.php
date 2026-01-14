<?php

declare(strict_types=1);

function apply_security_headers(): void
{
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'");
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

function enforce_rate_limit(string $key, int $limit, int $windowSeconds): bool
{
    $now = time();
    $bucket = $_SESSION['rate_limit'][$key] ?? ['count' => 0, 'reset' => $now + $windowSeconds];

    if ($now > $bucket['reset']) {
        $bucket = ['count' => 0, 'reset' => $now + $windowSeconds];
    }

    $bucket['count']++;
    $_SESSION['rate_limit'][$key] = $bucket;

    return $bucket['count'] <= $limit;
}
