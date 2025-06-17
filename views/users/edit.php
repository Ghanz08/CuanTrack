<?php
require_once 'core/functions.php';
$pageTitle = 'Edit Profile';

// Default avatar if user has no image
$defaultAvatar = '/public/images/default-avatar.png';
$profileImage = !empty($user['image']) && file_exists($user['image']) ? '/' . $user['image'] : $defaultAvatar;

// Get error message if it exists
$error = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'missing_fields':
            $error = 'Please fill in all required fields.';
            break;
        case 'email_exists':
            $error = 'Email already exists. Please use a different email.';
            break;
        case 'invalid_password':
            $error = 'Current password is incorrect.';
            break;
        case 'password_mismatch':
            $error = 'New passwords do not match.';
            break;
        case 'update_failed':
            $error = 'Failed to update profile. Please try again.';
            break;
        case 'invalid_file_type':
            $error = 'Invalid file type. Only JPEG, PNG, and GIF are allowed.';
            break;
        case 'file_too_large':
            $error = 'File is too large. Maximum size is 2MB.';
            break;
        case 'upload_failed':
            $error = 'Failed to upload profile image. Please try again.';
            break;
        default:
            $error = 'An error occurred. Please try again.';
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
                    <a href="/users/profile" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Profile
                    </a>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Profile Image Section -->
                <div class="col-md-4 mb-4">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title"><i class="bi bi-person-circle me-2"></i>Profile Image</div>
                        </div>

                        <div class="profile-image-container mb-3">
                            <img src="<?= $profileImage ?>" alt="Profile Image" class="profile-image" id="profile-image-preview">
                            <div class="image-upload-controls mt-2">
                                <input type="file" id="profile-image-input" name="profile_image" accept="image/*" class="d-none">
                                <button type="button" class="btn btn-sm btn-primary" id="choose-image-btn">
                                    <i class="bi bi-camera-fill me-1"></i> Choose File
                                </button>
                            </div>
                        </div>

                        <form action="/users/update" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="profile_image" class="form-label">Profile Photo</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image"
                                    accept="image/jpeg,image/png,image/gif">
                                <small class="form-text text-white">Maximum file size: 2MB. Accepted formats: JPG, PNG,
                                    GIF</small>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Upload Photo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Profile Information Section -->
                <div class="col-md-8">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title"><i class="bi bi-person me-2"></i>Profile Information</div>
                        </div>

                        <form action="/users/update" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Update Profile
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Password Change Section -->
                    <div class="dashboard-section mt-4">
                        <div class="section-header">
                            <div class="section-title"><i class="bi bi-shield-lock me-2"></i>Change Password</div>
                        </div>

                        <form action="/users/update" method="POST">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password"
                                    name="current_password">
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password"
                                    name="confirm_password">
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-shield-lock me-1"></i> Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Photo Confirmation Modal -->
    <div class="modal fade" id="deletePhotoModal" tabindex="-1" aria-labelledby="deletePhotoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePhotoModalLabel">Delete Profile Photo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete your profile photo? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeletePhoto">Delete Photo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Delete photo button
            const deletePhotoBtn = document.getElementById('deletePhotoBtn');
            const confirmDeleteBtn = document.getElementById('confirmDeletePhoto');
            const deleteModal = new bootstrap.Modal(document.getElementById('deletePhotoModal'));

            deletePhotoBtn?.addEventListener('click', function () {
                deleteModal.show();
            });

            confirmDeleteBtn?.addEventListener('click', function () {
                // Send AJAX request to delete photo
                fetch('/users/delete-photo', {
                    method: 'POST'
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page to show updated profile
                            window.location.reload();
                        } else {
                            alert('Failed to delete photo: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the photo.');
                    });

                deleteModal.hide();
            });

            // Image preview functionality
            const profileImageInput = document.getElementById('profile-image-input');
            const profileImageFormInput = document.getElementById('profile_image');
            const profileImagePreview = document.getElementById('profile-image-preview');
            const chooseImageBtn = document.getElementById('choose-image-btn');

            // Store original image URL
            const originalImageUrl = profileImagePreview.src;

            // Handle choose image button click
            chooseImageBtn?.addEventListener('click', function() {
                profileImageInput.click();
            });

            // Function to handle file preview
            function handleFilePreview(file) {
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        profileImagePreview.src = e.target.result;
                    }
                    
                    reader.readAsDataURL(file);
                }
            }

            // Handle file selection from hidden input
            profileImageInput?.addEventListener('change', function(e) {
                const file = this.files[0];
                handleFilePreview(file);
                
                // Sync with the form input
                if (profileImageFormInput && file) {
                    // Create a new FileList and assign it to the form input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    profileImageFormInput.files = dataTransfer.files;
                }
            });

            // Handle file selection from form input
            profileImageFormInput?.addEventListener('change', function(e) {
                const file = this.files[0];
                handleFilePreview(file);
                
                // Sync with the hidden input
                if (profileImageInput && file) {
                    // Create a new FileList and assign it to the hidden input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    profileImageInput.files = dataTransfer.files;
                }
            });
        });
    </script>
</body>

</html>