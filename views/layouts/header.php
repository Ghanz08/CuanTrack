<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuanTrack - Kelola Keuangan Anda</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body>
    <?php
    require_once 'core/functions.php'; // Pastikan fungsi tersedia
    
    if (!isset($_SESSION['user_id'])): ?>
        <div class="alert alert-info text-center m-0 rounded-0">
            <strong>Demo Mode:</strong> You are viewing the application in demo mode. No data will be saved.
            <a href="/login" class="alert-link">Login</a> or <a href="/register" class="alert-link">Register</a> to use all
            features.
        </div>
    <?php endif; ?>

    <header>
    </header>

    <div class="container main-content">
        <?php
        // Tampilkan pesan flash jika ada
        $flashMessage = getFlashMessage();
        if ($flashMessage): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($flashMessage) ?>
            </div>
        <?php endif; ?>