<div class="modal fade" id="addBudgetModal" tabindex="-1" aria-labelledby="addBudgetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBudgetModalLabel">Create New Budget</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addBudgetForm">
                    <div class="mb-3">
                        <label for="budget_wallet" class="form-label">Wallet*</label>
                        <select class="form-select" id="budget_wallet" name="id_wallet" required>
                            <option value="" disabled selected>Select wallet</option>
                            <?php if (isset($wallets) && !empty($wallets)): ?>
                                <?php foreach ($wallets as $wallet): ?>
                                    <option value="<?= $wallet['id_wallet'] ?>"><?= htmlspecialchars($wallet['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="budget_category" class="form-label">Category*</label>
                        <select class="form-select" id="budget_category" name="id_category" required>
                            <option value="" disabled selected>Select category</option>
                            <?php if (isset($expenseCategories) && !empty($expenseCategories)): ?>
                                <?php foreach ($expenseCategories as $category): ?>
                                    <option value="<?= $category['id_category'] ?>"><?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="budget_amount" class="form-label">Budget Amount*</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="budget_amount" name="amount" placeholder="0"
                                required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="budget_start_date" class="form-label">Start Date*</label>
                            <input type="date" class="form-control" id="budget_start_date" name="start_date"
                                value="<?= date('Y-m-01') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="budget_end_date" class="form-label">End Date*</label>
                            <input type="date" class="form-control" id="budget_end_date" name="end_date"
                                value="<?= date('Y-m-t') ?>" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Budget</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add budget form submission
        document.getElementById('addBudgetForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';
            submitBtn.disabled = true;

            // Get form data
            const formData = new FormData(this);

            // Convert FormData to JSON
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            // Validate required fields
            if (!data.id_wallet || !data.id_category || !data.amount || !data.start_date || !data.end_date) {
                // Restore button
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;

                showErrorToast('Please fill in all required fields');
                return;
            }

            console.log('Sending budget creation data:', data);

            // Send AJAX request to create budget - correcting the endpoint
            fetch('/api/budgets/add', {
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

                    // Restore button
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;

                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addBudgetModal'));
                        modal.hide();

                        // Reset form
                        document.getElementById('addBudgetForm').reset();

                        // Show success message
                        showSuccessToast(data.message || 'Budget created successfully');

                        // Reload page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showErrorToast(data.message || 'Failed to create budget');
                    }
                })
                .catch(error => {
                    // Restore button
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;

                    console.error('Error:', error);
                    showErrorToast('An error occurred during the operation');
                });
        });
    });
</script>