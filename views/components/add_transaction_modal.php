<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTransactionModalLabel">Add Transaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transactionForm" action="/transactions/store" method="POST">
                    <input type="hidden" name="wallet_id" value="<?= $wallet['id_wallet'] ?? '' ?>">

                    <div class="transaction-type-selector mb-4">
                        <div class="type-toggle-container">
                            <input type="radio" class="btn-check" name="type" id="expense" value="expense" checked>
                            <label class="type-toggle-btn" for="expense">Expense</label>

                            <input type="radio" class="btn-check" name="type" id="income" value="income">
                            <label class="type-toggle-btn" for="income">Income</label>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount *</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="amount" name="amount" placeholder="0"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description"
                                placeholder="What's this for?">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="category" class="form-label">Category *</label>
                        <select class="form-select" id="category" name="category_id" required>
                            <option value="" selected disabled>Select category</option>

                            <?php foreach ($expenseCategories ?? [] as $category): ?>
                                <option value="<?= $category['id_category'] ?>" data-type="expense">
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>

                            <?php foreach ($incomeCategories ?? [] as $category): ?>
                                <option value="<?= $category['id_category'] ?>" data-type="income" style="display:none;">
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="mt-2">
                            <small class="text-white">Don't see a category you need? <a href="/categories"
                                    class="text-primary">
                                    <i class="bi bi-plus-circle-fill"></i> Manage categories</a>
                            </small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="transactionDate" class="form-label">Date *</label>
                        <input type="date" class="form-control" id="transactionDate" name="transaction_date"
                            value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const expenseRadio = document.getElementById('expense');
        const incomeRadio = document.getElementById('income');
        const categorySelect = document.getElementById('category');
        const allOptions = categorySelect.querySelectorAll('option');

        function showCategoriesByType(type) {
            allOptions.forEach(option => {
                if (option.getAttribute('data-type') === type || !option.getAttribute('data-type')) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
            categorySelect.value = '';
        }

        showCategoriesByType('expense');

        expenseRadio.addEventListener('change', function () {
            if (this.checked) showCategoriesByType('expense');
        });

        incomeRadio.addEventListener('change', function () {
            if (this.checked) showCategoriesByType('income');
        });
    });
</script>