<div class="modal fade" id="deleteBudgetModal" tabindex="-1" aria-labelledby="deleteBudgetModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBudgetModalLabel">Delete Budget</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteBudgetId">

                <p>Are you sure you want to delete the budget for <strong id="deleteBudgetCategory"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBudget" class="btn btn-danger">Delete Budget</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Delete Budget confirmation handler
        document.getElementById('confirmDeleteBudget').addEventListener('click', function () {
            const budgetId = document.getElementById('deleteBudgetId').value;

            // Show loading state
            const originalBtnText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
            this.disabled = true;

            console.log('Sending delete request for budget ID:', budgetId);

            // Send AJAX request - using POST instead of DELETE for better compatibility
            fetch('/api/budgets/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_budget: budgetId
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
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteBudgetModal'));
                        modal.hide();

                        // Show success message
                        showSuccessToast(data.message || 'Budget deleted successfully');

                        // Reload page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Restore button
                        this.innerHTML = originalBtnText;
                        this.disabled = false;

                        showErrorToast(data.message || 'Failed to delete budget');
                    }
                })
                .catch(error => {
                    // Restore button
                    this.innerHTML = originalBtnText;
                    this.disabled = false;

                    console.error('Error deleting budget:', error);
                    showErrorToast('An error occurred while deleting the budget');
                });
        });
    });
</script>