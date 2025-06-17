<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTransactionModalLabel">Edit Transaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTransactionForm" action="/transactions/update" method="POST">
                    <input type="hidden" name="id_transaction" id="edit_transaction_id">
                    <input type="hidden" name="wallet_id" value="<?= $wallet['id_wallet'] ?? '' ?>">
                    <input type="hidden" name="type" id="edit_type">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="edit_amount" class="form-label">Amount *</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="edit_amount" name="amount" placeholder="0"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="edit_description" name="description"
                                placeholder="What's this for?">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="edit_category" class="form-label">Category *</label>
                        <select class="form-select" id="edit_category" name="category_id" required>
                            <option value="" selected disabled>Select category</option>
                        </select>
                        <div class="mt-2">
                            <small class="text-white">Don't see a category you need? <a href="/categories"
                                    class="text-primary">
                                    <i class="bi bi-plus-circle-fill"></i> Manage categories</a>
                            </small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="edit_transaction_date" class="form-label">Date *</label>
                        <input type="date" class="form-control" id="edit_transaction_date" name="transaction_date"
                            required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>