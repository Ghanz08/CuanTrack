<?php
namespace Auth;

require_once __DIR__ . '/../../models/AuthModel.php';
require_once __DIR__ . '/../../config/database.php';

class AuthController
{
    private $authModel;

    public function __construct()
    {
        $database = new \Database();
        $dbConnection = $database->getConnection();
        $this->authModel = new AuthModel($dbConnection);
    }

    public function loginPage()
    {
        include __DIR__ . '/../../views/login.php';
    }

    public function registerPage()
    {
        include __DIR__ . '/../../views/register.php';
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$username || !$password) {
            header("Location: /login?error=missing_fields");
            exit;
        }

        if ($this->authModel->validateUser($username, $password)) {
            $_SESSION['user_id'] = $this->authModel->getUserId($username);
            $_SESSION['user_name'] = $this->authModel->getUserName($username);
            header("Location: /dashboard");
        } else {
            header("Location: /login?error=invalid_credentials");
        }
    }

    public function register()
    {
        $username = $_POST['username'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (empty($username) || empty($email) || empty($password)) {
            header("Location: /register?error=missing_fields");
            exit;
        }

        // Check if email already exists
        if ($this->authModel->emailExists($email)) {
            header("Location: /register?error=email_exists");
            exit;
        }

        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($this->authModel->registerUser($username, $email, $hashedPassword)) {
            // Redirect to login page with success message
            header("Location: /login?success=registration_success");
        } else {
            header("Location: /register?error=registration_failed");
        }
        exit;
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header("Location: /login?success=logout_success");
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'login') {
    $controller = new AuthController();
    $controller->login();
} elseif (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $controller = new AuthController();
    $controller->logout();
} elseif (isset($_GET['action']) && $_GET['action'] === 'register') {
    $controller = new AuthController();
    $controller->register();
}