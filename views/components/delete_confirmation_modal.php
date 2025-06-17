<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Confirmation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteConfirmationMessage">Are you sure you want to delete this item?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteItemForm" method="POST">
                    <input type="hidden" id="deleteItemId" name="id_item">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showDeleteConfirmation(options) {
        const { type, name, id, idField, action } = options;

        // Set up the confirmation modal
        document.getElementById('deleteConfirmationModalLabel').textContent = `Delete ${type}`;
        document.getElementById('deleteConfirmationMessage').textContent =
            `Are you sure you want to delete ${type.toLowerCase()} "${name}"? This action cannot be undone.`;

        // Set up the form
        const form = document.getElementById('deleteItemForm');
        form.action = action;

        // Set the ID field name and value
        const idInput = document.getElementById('deleteItemId');
        idInput.name = idField;
        idInput.value = id;

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        modal.show();

        // Handle form submission
        form.onsubmit = function (e) {
            e.preventDefault();

            // Show loading state on the delete button
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
            submitBtn.disabled = true;

            // Create FormData object from the form
            const formData = new FormData(this);

            // Send request
            fetch(action, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    // Hide the modal
                    modal.hide();

                    if (data.success) {
                        // Show success message
                        if (window.showToast) {
                            showToast(data.message, true);
                        } else {
                            alert(data.message);
                        }

                        // Redirect if provided
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1500);
                        } else {
                            // Reload the page
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    } else {
                        // Restore button
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        // Show error message
                        if (window.showToast) {
                            showToast(data.message, false);
                        } else {
                            alert(data.message);
                        }
                    }
                })
                .catch(error => {
                    // Restore button
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;

                    console.error('Error:', error);

                    // Show error message
                    if (window.showToast) {
                        showToast('An error occurred during the operation', false);
                    } else {
                        alert('An error occurred during the operation');
                    }
                });
        };
    }
</script>