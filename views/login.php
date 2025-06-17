<?php
require_once __DIR__ . '/../core/functions.php';

if (isLoggedIn()) {
    redirect('/dashboard');
}

// Get error message based on error code
$errorMessage = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'missing_fields':
            $errorMessage = 'Please fill in all fields.';
            break;
        case 'invalid_credentials':
            $errorMessage = 'Invalid email or password. Please try again.';
            break;
        case 'auth_required':
            $errorMessage = 'You must be logged in to access that page.';
            break;
        default:
            $errorMessage = 'An error occurred. Please try again.';
    }
}

// Get success message
$successMessage = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'registration_success':
            $successMessage = 'Registration successful! You can now log in.';
            break;
        case 'logout_success':
            $successMessage = 'You have been successfully logged out.';
            break;
        default:
            $successMessage = 'Operation completed successfully.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuanTrack - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        body {
            background-color: #0b0a16;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #ffffff;
        }

        .auth-card {
            background-color: #181f30;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }

        .form-control,
        .form-select {
            background-color: #242d40;
            color: #ffffff;
            border-color: #2c3850;
            border-radius: 8px;
            padding: 12px;
            height: auto;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #2a3548;
            color: #ffffff;
            border-color: #3a4969;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-label {
            color: #ffffff;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
        }

        .text-muted,
        .text-muted a {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        .text-muted a:hover {
            color: #5b9cff !important;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <h3 class="text-center mb-4">Login to CuanTrack</h3>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $errorMessage ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $successMessage ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Email</label>
                <input type="email" name="username" id="username" class="form-control" placeholder="Enter your email"
                    required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control"
                    placeholder="Enter your password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <small class="text-muted">Don't have an account? <a href="/register">Register here</a></small>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>