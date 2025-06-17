<div class="modal fade" id="editBudgetModal" tabindex="-1" aria-labelledby="editBudgetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBudgetModalLabel">Edit Budget</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editBudgetForm">
                    <input type="hidden" id="edit_budget_id" name="id_budget">

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <div class="form-control-plaintext text-light" id="edit_category_name"></div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_amount" class="form-label">Budget Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="edit_amount" name="amount" placeholder="0"
                                required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Budget</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Edit budget form submission
        document.getElementById('editBudgetForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
            submitBtn.disabled = true;

            // Get form values directly
            const budgetId = document.getElementById('edit_budget_id').value;
            const amount = document.getElementById('edit_amount').value;

            // Validate inputs
            if (!budgetId || !amount) {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                showErrorToast('Invalid input data');
                return;
            }

            // Prepare data object
            const data = {
                id_budget: budgetId,
                amount: amount
            };

            console.log('Sending budget update data:', data);

            // Send request to update budget
            fetch('/api/budgets/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editBudgetModal'));
                        modal.hide();

                        // Show success message
                        showSuccessToast(data.message || 'Budget updated successfully');

                        // Reload page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Restore button state
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        showErrorToast(data.message || 'Failed to update budget');
                    }
                })
                .catch(error => {
                    // Restore button state
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;

                    console.error('Error:', error);
                    showErrorToast('An error occurred during the operation');
                });
        });
    });
</script>