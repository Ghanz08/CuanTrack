<?php
/**
 * Global functions for the application
 */

/**
 * Format number to Rupiah currency
 * @param float $amount Amount to format
 * @return string Formatted amount
 */
function format_rupiah($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Sanitize input
 * @param string $input Input to sanitize
 * @return string Sanitized input
 */
function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if current page matches the given path
 * @param string $path
 * @return bool
 */

function isLoggedIn()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

function isCurrentPage($path)
{
    $currentUri = $_SERVER['REQUEST_URI'];
    // Remove query string
    if (($pos = strpos($currentUri, '?')) !== false) {
        $currentUri = substr($currentUri, 0, $pos);
    }
    // Remove trailing slash
    $currentUri = rtrim($currentUri, '/');

    return $currentUri === $path || strpos($currentUri, $path) === 0;
}

/**
 * Get mock username for demo purposes
 * @return string
 */
function getMockUsername()
{
    return 'Demo User';
}

/**
 * Redirect to a given URL
 * @param string $url URL to redirect to
 */
function redirect($url)
{
    header("Location: $url");
    exit;
}

function getFlashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']); // Hapus setelah diambil
        return $message;
    }
    return null;
}

function is_logged_in()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function auth_required()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!is_logged_in()) {
        header('Location: /login');
        exit;
    }
}

function get_user_id()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user_id'] ?? null;
}

function get_user_name()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user_name'] ?? null;
}

function sanitize_input($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generate_csrf_token()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function flash_message($key, $message = null)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($message === null) {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    } else {
        $_SESSION['flash'][$key] = $message;
    }
}

function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function debug_log($message, $data = null)
{
    $log_message = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    if ($data !== null) {
        $log_message .= ' Data: ' . json_encode($data);
    }
    error_log($log_message);
}

function get_current_url()
{
    return $_SERVER['REQUEST_URI'];
}

function is_current_page($path)
{
    return get_current_url() === $path;
}

function asset($path)
{
    return '/' . ltrim($path, '/');
}

function config($key, $default = null)
{
    static $config = null;

    if ($config === null) {
        $config = [
            'app_name' => 'CuanTrack',
            'app_version' => '1.0.0',
            'timezone' => 'Asia/Jakarta',
            'currency' => 'IDR',
            'date_format' => 'd/m/Y',
            'datetime_format' => 'd/m/Y H:i:s'
        ];
    }

    return $config[$key] ?? $default;
}

function format_date($date, $format = null)
{
    if ($format === null) {
        $format = config('date_format', 'd/m/Y');
    }

    if (is_string($date)) {
        $date = new DateTime($date);
    }

    return $date->format($format);
}

function format_datetime($datetime, $format = null)
{
    if ($format === null) {
        $format = config('datetime_format', 'd/m/Y H:i:s');
    }

    if (is_string($datetime)) {
        $datetime = new DateTime($datetime);
    }

    return $datetime->format($format);
}

function truncate_text($text, $length = 100, $suffix = '...')
{
    if (strlen($text) <= $length) {
        return $text;
    }

    return substr($text, 0, $length) . $suffix;
}

function percentage($part, $total, $decimals = 2)
{
    if ($total == 0) {
        return 0;
    }

    return round(($part / $total) * 100, $decimals);
}

function array_get($array, $key, $default = null)
{
    if (isset($array[$key])) {
        return $array[$key];
    }

    if (strpos($key, '.') === false) {
        return $default;
    }

    $keys = explode('.', $key);
    $current = $array;

    foreach ($keys as $k) {
        if (!is_array($current) || !isset($current[$k])) {
            return $default;
        }
        $current = $current[$k];
    }

    return $current;
}

function old($key, $default = '')
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return $_SESSION['old'][$key] ?? $default;
}

function set_old_input()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['old'] = $_POST;
}

function clear_old_input()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    unset($_SESSION['old']);
}
?>