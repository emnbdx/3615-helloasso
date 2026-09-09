<?php

namespace App;

final class CheckoutLink
{
    private const DIR = __DIR__ . '/../var/checkouts';

    public static function store(string $checkoutUrl): string
    {
        self::ensureDir();
        $token = bin2hex(random_bytes(4));
        file_put_contents(self::path($token), $checkoutUrl);
        return $token;
    }

    public static function url(string $baseUrl, string $token): string
    {
        return rtrim($baseUrl, '/') . '/go.php?t=' . rawurlencode($token);
    }

    public static function resolve(string $token): ?string
    {
        $token = preg_replace('/[^a-f0-9]/', '', strtolower($token)) ?? '';
        if (strlen($token) !== 8) {
            return null;
        }
        $file = self::path($token);
        if (!is_file($file)) {
            return null;
        }
        if (filemtime($file) < time() - 86400) {
            @unlink($file);
            return null;
        }
        $url = trim((string) file_get_contents($file));
        if (!preg_match('#^https://([\w.-]+\.)?helloasso(?:-sandbox)?\.com/#i', $url)) {
            return null;
        }
        return $url;
    }

    private static function ensureDir(): void
    {
        if (!is_dir(self::DIR)) {
            mkdir(self::DIR, 0775, true);
        }
    }

    private static function path(string $token): string
    {
        return self::DIR . '/' . $token . '.url';
    }
}
