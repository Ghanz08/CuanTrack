<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryModalLabel">Delete Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteCategoryId">

                <p>Are you sure you want to delete the category <strong id="deleteCategoryName"></strong>?</p>
                <p class="text-danger">This action cannot be undone. If this category is used in any transactions, you
                    won't be able to delete it.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteCategory" class="btn btn-danger">Delete Category</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Delete Category confirmation handler
        document.getElementById('confirmDeleteCategory').addEventListener('click', function () {
            const categoryId = document.getElementById('deleteCategoryId').value;

            // Show loading state
            const originalBtnText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
            this.disabled = true;

            console.log('Sending delete request for category ID:', categoryId);

            // Send AJAX request
            fetch('/api/categories/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_category: categoryId
                })
            })
                .then(response => {
                    console.log('Delete response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Delete response data:', data);
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal'));
                        modal.hide();

                        // Show success message
                        showSuccessToast(data.message || 'Category deleted successfully');

                        // Remove the category from the DOM or reload the page
                        const categoryElement = document.querySelector(`.category-item[data-id="${categoryId}"]`);
                        if (categoryElement) {
                            categoryElement.remove();
                        } else {
                            // If we can't find the element, reload the page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    } else {
                        // Restore button
                        this.innerHTML = originalBtnText;
                        this.disabled = false;

                        showErrorToast(data.message || 'Failed to delete category');
                    }
                })
                .catch(error => {
                    // Restore button
                    this.innerHTML = originalBtnText;
                    this.disabled = false;

                    console.error('Error deleting category:', error);
                    showErrorToast('An error occurred while deleting the category');
                });
        });
    });
</script>