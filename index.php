<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session at the very beginning
session_start();

spl_autoload_register(function ($class) {
    if (file_exists("controllers/$class.php")) {
        require_once "controllers/$class.php";
    } elseif (file_exists("core/$class.php")) {
        require_once "core/$class.php";
    } elseif (file_exists("models/$class.php")) {
        require_once "models/$class.php";
    }
});

require_once 'core/functions.php';
$routes = require 'routes/web.php';

$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseDir = ($scriptName === '/' || $scriptName === '\\') ? '' : $scriptName;

$requestUri = $_SERVER['REQUEST_URI'];
if (!empty($baseDir) && strpos($requestUri, $baseDir) === 0) {
    $requestUri = substr($requestUri, strlen($baseDir));
}
$path = parse_url($requestUri, PHP_URL_PATH);

if ($path != '/' && substr($path, -1) == '/') {
    $path = rtrim($path, '/');
}

$method = $_SERVER['REQUEST_METHOD'];

// Handle root path - redirect to appropriate page
if ($path === '/') {
    if (is_logged_in()) {
        $path = '/dashboard';
    } else {
        $path = '/login';
    }
}

if (isset($routes[$method])) {
    $routeFound = false;

    foreach ($routes[$method] as $routePattern => $handler) {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePattern);
        $pattern = "#^$pattern$#";

        if (preg_match($pattern, $path, $matches)) {
            $routeFound = true;

            list($controller, $action) = explode('@', $handler);

            if (strpos($controller, '\\') !== false) {
                $controllerPath = str_replace('\\', '/', $controller);
                require_once "controllers/$controllerPath.php";
                $controllerClass = $controller;
            } else {
                require_once "controllers/$controller.php";
                $controllerClass = $controller;
            }

            $controllerInstance = new $controllerClass();

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            unset($params[0]);

            call_user_func_array([$controllerInstance, $action], array_values($params));
            break;
        }
    }

    if (!$routeFound) {
        http_response_code(404);
        echo "404 - Page Not Found: " . htmlspecialchars($path);
    }
} else {
    http_response_code(405);
    echo "405 - Method Not Allowed";
}
?>