<?php
require_once 'core/functions.php';
$pageTitle = 'User Profile';

// Default avatar if user has no image
$defaultAvatar = '/public/images/default-avatar.png';
$profileImage = !empty($user['image']) && file_exists($user['image']) ? '/' . $user['image'] : $defaultAvatar;

// Get success message if it exists
$successMessage = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'profile_updated':
            $successMessage = 'Your profile has been updated successfully.';
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
    <title>CuanTrack - <?= $pageTitle ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include 'views/layouts/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-wrapper">
            <!-- Page Header -->
            <div class="page-header">
                <h1><?= $pageTitle ?></h1>
                <div>
                    <a href="/users/edit" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                </div>
            </div>

            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= $successMessage ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Profile Image and Basic Info -->
                <div class="col-md-4 mb-4">
                    <div class="dashboard-section">
                        <div class="profile-image-container mb-4">
                            <img src="<?= $profileImage ?>" alt="Profile Image" class="profile-image">
                        </div>
                        <h3 class="text-center mb-3"><?= htmlspecialchars($user['username'] ?? 'User') ?></h3>
                        <p class="text-center text-muted"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                        <div class="d-grid gap-2 mt-4">
                            <a href="/users/edit" class="btn btn-outline-primary">
                                <i class="bi bi-pencil"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Details -->
                <div class="col-md-8">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title"><i class="bi bi-person-badge me-2"></i>Account Information</div>
                        </div>
                        <div class="profile-details">
                            <div class="detail-item">
                                <div class="detail-label">Username:</div>
                                <div class="detail-value"><?= htmlspecialchars($user['username'] ?? 'Not set') ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Email Address:</div>
                                <div class="detail-value"><?= htmlspecialchars($user['email'] ?? 'Not set') ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Account Created:</div>
                                <div class="detail-value">
                                    <?= isset($user['created_at']) ? date('F j, Y', strtotime($user['created_at'])) : 'Not available' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Security Section -->
                    <div class="dashboard-section mt-4">
                        <div class="section-header">
                            <div class="section-title"><i class="bi bi-shield-lock me-2"></i>Account Security</div>
                        </div>
                        <div class="security-options">
                            <div class="detail-item">
                                <div class="detail-label">Password</div>
                                <div class="detail-value">
                                    <span class="text-white">••••••••</span>
                                    <a href="/users/edit" class="btn btn-sm btn-outline-primary ms-3">
                                        <i class="bi bi-pencil"></i> Change
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>