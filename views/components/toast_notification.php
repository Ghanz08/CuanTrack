<!-- Toast Notification Component -->
<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="successToastBody">
                <!-- Success message will be inserted here -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>

    <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="errorToastBody">
                <!-- Error message will be inserted here -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Toast Notification JavaScript -->
<script>
    /**
     * Shows a toast notification with the given message
     * @param {string} message - The message to display
     * @param {boolean} isSuccess - Whether this is a success or error message
     */
    function showToast(message, isSuccess = true) {
        console.log('Showing toast:', message, isSuccess); // Debug log

        // Select the appropriate toast element
        const toastElement = isSuccess ? document.getElementById('successToast') : document.getElementById('errorToast');
        const toastBody = isSuccess ? document.getElementById('successToastBody') : document.getElementById('errorToastBody');

        if (!toastElement || !toastBody) {
            console.error('Toast elements not found:', toastElement, toastBody);
            alert(message); // Fallback to alert if toast elements aren't found
            return;
        }

        // Set the message
        toastBody.textContent = message;

        // Create a Bootstrap Toast instance and show it
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });

        toast.show();
    }

    // Helper functions for showing success and error toasts
    function showSuccessToast(message) {
        showToast(message, true);
    }

    function showErrorToast(message) {
        showToast(message, false);
    }

    // Check URL for success or error query parameters
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('success')) {
            const successMsg = urlParams.get('success');
            let message = 'Operation completed successfully';

            // Map success codes to messages
            switch (successMsg) {
                case 'registration_success':
                    message = 'Registration successful! You can now log in.';
                    break;
                case 'logout_success':
                    message = 'You have been successfully logged out.';
                    break;
                case 'wallet_created':
                    message = 'Wallet created successfully.';
                    break;
                case 'wallet_updated':
                    message = 'Wallet updated successfully.';
                    break;
                case 'transaction_added':
                    message = 'Transaction added successfully.';
                    break;
                case 'goal_created':
                    message = 'Goal created successfully.';
                    break;
                // Add more success message mappings as needed
            }

            showSuccessToast(message);
        }

        if (urlParams.has('error')) {
            const errorMsg = urlParams.get('error');
            let message = 'An error occurred';

            // Map error codes to messages
            switch (errorMsg) {
                case 'missing_fields':
                    message = 'Please fill in all required fields.';
                    break;
                case 'invalid_credentials':
                    message = 'Invalid email or password.';
                    break;
                case 'email_exists':
                    message = 'Email already exists. Please use a different email.';
                    break;
                case 'registration_failed':
                    message = 'Registration failed. Please try again.';
                    break;
                case 'unauthorized':
                    message = 'You are not authorized to perform this action.';
                    break;
                // Add more error message mappings as needed
            }

            showErrorToast(message);
        }
    });
</script>

<style>
    .toast-container {
        z-index: 9999;
    }

    .toast {
        opacity: 1 !important;
    }
</style>