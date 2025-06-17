<?php

class Middleware
{
    public static function auth()
    {
        auth_required();
    }

    public static function guest()
    {
        if (is_logged_in()) {
            header('Location: /dashboard');
            exit;
        }
    }

    public static function csrf()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

            if (!verify_csrf_token($token)) {
                http_response_code(403);
                die('CSRF token mismatch');
            }
        }
    }

    public static function admin()
    {
        auth_required();

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            http_response_code(403);
            die('Access denied');
        }
    }

    public static function rateLimit($max_attempts = 5, $window = 300)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "rate_limit_{$ip}";

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'last_attempt' => time()];
        }

        $rate_data = $_SESSION[$key];

        if (time() - $rate_data['last_attempt'] > $window) {
            $_SESSION[$key] = ['count' => 1, 'last_attempt' => time()];
        } else {
            $_SESSION[$key]['count']++;
            $_SESSION[$key]['last_attempt'] = time();

            if ($_SESSION[$key]['count'] > $max_attempts) {
                http_response_code(429);
                die('Too many requests. Please try again later.');
            }
        }
    }
}

function guest_required()
{
    if (is_logged_in()) {
        header('Location: /dashboard');
        exit;
    }
}
?>