<?php
/**
 * SMARTRESTA Core CSRF Protection Middleware
 * Token Generation, Verification, Header & Request Payload Defense
 */

class CSRF {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                ini_set('session.cookie_httponly', 1);
                ini_set('session.use_only_cookies', 1);
            }
            @session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function getToken(): string {
        self::init();
        return $_SESSION['csrf_token'];
    }

    public static function validateToken(?string $token): bool {
        self::init();
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], trim($token));
    }

    public static function getRequestToken(): ?string {
        // 1. HTTP Headers
        $headers = [
            $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null,
            $_SERVER['HTTP_X_XSRF_TOKEN'] ?? null
        ];
        foreach ($headers as $h) {
            if (!empty($h)) return trim($h);
        }

        // 2. Post / JSON payload
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input['csrf_token'])) return trim($input['csrf_token']);
        if (!empty($_POST['csrf_token'])) return trim($_POST['csrf_token']);

        return null;
    }

    public static function verifyRequest(): bool {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true; // Read-only requests bypass CSRF
        }

        $token = self::getRequestToken();
        return self::validateToken($token);
    }
}
