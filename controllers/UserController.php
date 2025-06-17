<?php
require_once 'core/Middleware.php';
require_once 'models/UserModel.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function profile()
    {
        auth_required();
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        require_once 'views/users/profile.php';
    }

    public function edit()
    {
        auth_required();
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        require_once 'views/users/edit.php';
    }

    public function update()
    {
        auth_required();
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);

        // Debug the submitted form data
        error_log("UserController::update - POST data: " . json_encode($_POST));
        error_log("UserController::update - FILES data: " . json_encode($_FILES));

        // Check if any profile data is being submitted
        $hasProfileData = isset($_POST['username']) && isset($_POST['email']);
        $hasPasswordData = isset($_POST['current_password']) && !empty($_POST['current_password']);

        // If neither profile nor password data is submitted, check if we have a file upload only
        if (!$hasProfileData && !$hasPasswordData) {
            $hasFileUpload = isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0;

            if ($hasFileUpload) {
                // Process file upload only
                error_log("UserController::update - Processing file upload only");

                $uploadDir = 'public/images/profiles/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = $_FILES['profile_image']['name'];
                $fileSize = $_FILES['profile_image']['size'];
                $fileTmpName = $_FILES['profile_image']['tmp_name'];
                $fileType = $_FILES['profile_image']['type'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

                if (!in_array($fileType, $allowedTypes)) {
                    header('Location: /users/edit?error=invalid_file_type');
                    exit;
                }

                if ($fileSize > 2097152) {
                    header('Location: /users/edit?error=file_too_large');
                    exit;
                }

                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = 'user_' . $userId . '_' . time() . '.' . $fileExtension;
                $targetFilePath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                    if (!empty($user['image']) && file_exists($user['image'])) {
                        unlink($user['image']);
                    }

                    $data = ['image' => $targetFilePath];

                    if ($this->userModel->update($userId, $data)) {
                        header('Location: /users/profile?success=profile_updated');
                    } else {
                        header('Location: /users/edit?error=update_failed');
                    }
                    exit;
                } else {
                    error_log("UserController::update - Failed to move uploaded file from $fileTmpName to $targetFilePath");
                    header('Location: /users/edit?error=upload_failed');
                    exit;
                }
            } else {
                // No valid data submitted
                error_log("UserController::update - No valid data submitted");
                header('Location: /users/edit?error=invalid_request');
                exit;
            }
        }

        // Handle password update
        if ($hasPasswordData) {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            if (!password_verify($currentPassword, $user['password'])) {
                header('Location: /users/edit?error=invalid_password');
                exit;
            }
            if (empty($newPassword) || $newPassword !== $confirmPassword) {
                header('Location: /users/edit?error=password_mismatch');
                exit;
            }
            $data = [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT)
            ];
            if ($this->userModel->update($userId, $data)) {
                header('Location: /users/profile?success=password_updated');
            } else {
                header('Location: /users/edit?error=update_failed');
            }
            exit;
        }
        // Handle profile update
        else if ($hasProfileData) {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            if (empty($username) || empty($email)) {
                header('Location: /users/edit?error=missing_fields');
                exit;
            }
            if ($email !== $user['email'] && $this->userModel->emailExists($email)) {
                header('Location: /users/edit?error=email_exists');
                exit;
            }
            $data = [
                'username' => $username,
                'email' => $email,
            ];

            // Enhanced file upload debugging
            error_log("UserController::update - Processing file upload. FILES array: " . json_encode($_FILES));

            // Check if a file was uploaded
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Log the file information
                error_log("UserController::update - File info: " . json_encode($_FILES['profile_image']));

                $uploadDir = 'public/images/profiles/';

                // Ensure the upload directory exists with proper permissions
                if (!file_exists($uploadDir)) {
                    error_log("UserController::update - Creating upload directory: $uploadDir");
                    if (!mkdir($uploadDir, 0777, true)) {
                        error_log("UserController::update - Failed to create directory: $uploadDir");
                        header('Location: /users/edit?error=directory_creation_failed');
                        exit;
                    }
                    chmod($uploadDir, 0777); // Ensure the directory is writable
                }

                $fileName = $_FILES['profile_image']['name'];
                $fileSize = $_FILES['profile_image']['size'];
                $fileTmpName = $_FILES['profile_image']['tmp_name'];
                $fileType = $_FILES['profile_image']['type'];
                $fileError = $_FILES['profile_image']['error'];

                // Handle file upload errors
                if ($fileError !== UPLOAD_ERR_OK) {
                    $errorMessages = [
                        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
                        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
                        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
                    ];

                    $errorMessage = isset($errorMessages[$fileError]) ? $errorMessages[$fileError] : 'Unknown upload error';
                    error_log("UserController::update - File upload error: $errorMessage (code: $fileError)");
                    header('Location: /users/edit?error=upload_error&code=' . $fileError);
                    exit;
                }

                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($fileType, $allowedTypes)) {
                    error_log("UserController::update - Invalid file type: $fileType");
                    header('Location: /users/edit?error=invalid_file_type');
                    exit;
                }

                if ($fileSize > 2097152) { // 2MB limit
                    error_log("UserController::update - File too large: $fileSize bytes");
                    header('Location: /users/edit?error=file_too_large');
                    exit;
                }

                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = 'user_' . $userId . '_' . time() . '.' . $fileExtension;
                $targetFilePath = $uploadDir . $newFileName;

                error_log("UserController::update - Attempting to move uploaded file from $fileTmpName to $targetFilePath");

                // Try to move the uploaded file
                if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                    error_log("UserController::update - File moved successfully to $targetFilePath");

                    // Delete previous profile image if it exists
                    if (!empty($user['image']) && file_exists($user['image'])) {
                        error_log("UserController::update - Deleting previous image: " . $user['image']);
                        if (unlink($user['image'])) {
                            error_log("UserController::update - Previous image deleted successfully");
                        } else {
                            error_log("UserController::update - Failed to delete previous image");
                        }
                    }

                    $data['image'] = $targetFilePath;
                } else {
                    // Get the last PHP error
                    $phpError = error_get_last();
                    error_log("UserController::update - Failed to move uploaded file. PHP error: " . json_encode($phpError));

                    // Check file permissions
                    error_log("UserController::update - Upload directory permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4));

                    header('Location: /users/edit?error=upload_failed');
                    exit;
                }
            } else {
                error_log("UserController::update - No file uploaded or file upload error: " .
                    (isset($_FILES['profile_image']) ? $_FILES['profile_image']['error'] : 'FILES array not set for profile_image'));
            }

            // Update user data
            error_log("UserController::update - Updating user data: " . json_encode($data));
            if ($this->userModel->update($userId, $data)) {
                if ($username !== $user['username']) {
                    $_SESSION['user_name'] = $username;
                }
                error_log("UserController::update - User data updated successfully");
                header('Location: /users/profile?success=profile_updated');
            } else {
                error_log("UserController::update - Failed to update user data");
                header('Location: /users/edit?error=update_failed');
            }
            exit;
        } else {
            error_log("UserController::update - Invalid request type");
            header('Location: /users/edit?error=invalid_request');
            exit;
        }
    }

    public function deletePhoto()
    {
        auth_required();
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        if (empty($user['image']) || !file_exists($user['image'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No profile photo to delete']);
            return;
        }
        if (unlink($user['image'])) {
            $this->userModel->update($userId, ['image' => null]);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Profile photo deleted successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to delete profile photo']);
        }
    }
}
?>