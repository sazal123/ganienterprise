<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;

class GuestTokenService
{
    public const COOKIE_NAME = 'chat_guest_token';
    public const COOKIE_LIFETIME_MINUTES = 525600; // 1 year (in minutes)

    /**
     * Generate a cryptographically secure random guest token.
     * Uses PHP 8 random_bytes / Laravel Str::random without exposing IDs, IPs, or timestamps.
     *
     * @return string
     */
    public function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Hash the guest token using SHA-256 for secure DB storage.
     *
     * @param string $token
     * @return string
     */
    public function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Retrieve the unhashed guest token from HTTP Request cookie or fallback header.
     *
     * @param Request $request
     * @return string|null
     */
    public function getTokenFromRequest(Request $request): ?string
    {
        // 1. Try reading from cookie
        $token = $request->cookie(self::COOKIE_NAME);

        // 2. Try raw cookie parameter if encrypted cookie middleware stripped unencrypted testing cookie
        if (!$token && $request->cookies->has(self::COOKIE_NAME)) {
            $token = $request->cookies->get(self::COOKIE_NAME);
        }

        // 3. Fallback to header if header matches 64-char alphanumeric pattern
        if (!$token) {
            $headerToken = $request->header('X-Guest-Token');
            if ($headerToken && preg_match('/^[a-zA-Z0-9]{64}$/', $headerToken)) {
                $token = $headerToken;
            }
        }

        return (is_string($token) && strlen($token) === 64 && preg_match('/^[a-zA-Z0-9]{64}$/', $token)) ? $token : null;
    }

    /**
     * Create a secure HTTP cookie containing the unhashed guest token.
     *
     * @param string $token
     * @return Cookie
     */
    public function makeCookie(string $token): Cookie
    {
        $isSecure = app()->environment('production') || request()->isSecure();

        return cookie(
            self::COOKIE_NAME,
            $token,
            self::COOKIE_LIFETIME_MINUTES,
            '/',
            null,
            $isSecure,
            true, // HttpOnly
            false,
            'lax' // SameSite Lax
        );
    }
}
