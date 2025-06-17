<?php
require_once 'core/functions.php';
$pageTitle = 'Transactions';

// Ensure variables are always arrays to avoid warnings
$transactions = $transactions ?? [];
$wallets = $wallets ?? [];
$categories = $categories ?? [];
$expenseCategories = $expenseCategories ?? [];
$incomeCategories = $incomeCategories ?? [];
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
                    <a href="/wallets" class="btn btn-primary">
                        <i class="bi bi-wallet2 me-1"></i> Go to Wallets
                    </a>
                </div>
            </div>

            <!-- Transaction Summary Section -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="dashboard-section h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-arrow-down-up fs-1 text-primary"></i>
                            </div>
                            <div>
                                <div class="section-title mb-1 text-white">Total Transactions</div>
                                <div class="summary-value fs-4 fw-bold text-white">
                                    <?= $totalTransactions ?? count($transactions) ?>
                                </div>
                                <?php if (isset($totalTransactions) && isset($transactions) && count($transactions) < $totalTransactions): ?>
                                    <div class="summary-subtitle small text-white-50">
                                        Showing <?= count($transactions) ?> of <?= $totalTransactions ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-section h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-arrow-down fs-1 text-success"></i>
                            </div>
                            <div>
                                <div class="section-title mb-1 text-white">Total Income</div>
                                <div class="summary-value fs-4 fw-bold text-success">
                                    <?= format_rupiah(array_reduce($transactions, function ($sum, $transaction) {
                                        return $sum + ($transaction['type'] == 'income' ? $transaction['amount'] : 0);
                                    }, 0)) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-section h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-arrow-up fs-1 text-danger"></i>
                            </div>
                            <div>
                                <div class="section-title mb-1 text-white">Total Expenses</div>
                                <div class="summary-value fs-4 fw-bold text-danger">
                                    <?= format_rupiah(array_reduce($transactions, function ($sum, $transaction) {
                                        return $sum + ($transaction['type'] == 'expense' ? $transaction['amount'] : 0);
                                    }, 0)) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Filter Section -->
            <div class="dashboard-section mb-4">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-funnel me-2"></i>Filter Transactions</div>
                    <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse"
                        data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                        <i class="bi bi-sliders"></i> Toggle Filters
                    </button>
                </div>

                <div class="collapse show" id="filterCollapse">
                    <form id="filterForm" method="GET" class="row g-3 align-items-end mt-2">
                        <div class="col-md-3">
                            <label class="form-label">Wallet</label>
                            <select class="form-select" name="wallet_id">
                                <option value="">All Wallets</option>
                                <?php foreach ($wallets as $wallet): ?>
                                    <option value="<?= $wallet['id_wallet'] ?>" <?= isset($_GET['wallet_id']) && $_GET['wallet_id'] == $wallet['id_wallet'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($wallet['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id_category'] ?>" <?= isset($_GET['category_id']) && $_GET['category_id'] == $category['id_category'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type">
                                <option value="">All Types</option>
                                <option value="income" <?= isset($_GET['type']) && $_GET['type'] == 'income' ? 'selected' : '' ?>>Income</option>
                                <option value="expense" <?= isset($_GET['type']) && $_GET['type'] == 'expense' ? 'selected' : '' ?>>Expense</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date From</label>
                            <input type="date" class="form-control" name="date_from"
                                value="<?= $_GET['date_from'] ?? '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date To</label>
                            <input type="date" class="form-control" name="date_to"
                                value="<?= $_GET['date_to'] ?? '' ?>">
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Apply
                                Filters</button>
                            <a href="/transactions" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i>
                                Clear
                                Filters</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transaction List -->
            <div class="dashboard-section">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-list-ul me-2"></i>All Transactions</div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-file-earmark-excel"></i> Export to
                                    Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-file-earmark-pdf"></i> Export to
                                    PDF</a></li>
                        </ul>
                    </div>
                </div>

                <?php if (empty($transactions)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> No transactions found.
                        <a href="/wallets" class="alert-link">Go to a wallet</a> to add your first transaction!
                    </div>
                <?php else: ?>
                    <div class="transaction-table-container">
                        <table class="table table-dark table-hover transactions-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Wallet</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($transaction['transaction_date'])) ?></td>
                                        <td><?= htmlspecialchars($transaction['description'] ?? 'No description') ?></td>
                                        <td>
                                            <span class="category-tag">
                                                <?= htmlspecialchars($transaction['category_name'] ?? 'Uncategorized') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/wallets/view/<?= $transaction['id_wallet'] ?>" class="wallet-link">
                                                <i class="bi bi-wallet2 me-1"></i>
                                                <?= htmlspecialchars($transaction['wallet_name'] ?? 'Unknown') ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span
                                                class="badge <?= $transaction['type'] == 'income' ? 'bg-success' : 'bg-danger' ?>">
                                                <?= ucfirst($transaction['type']) ?>
                                            </span>
                                        </td>
                                        <td
                                            class="<?= $transaction['type'] == 'income' ? 'text-success' : 'text-danger' ?> fw-bold">
                                            <?= format_rupiah($transaction['amount']) ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary edit-transaction-btn"
                                                    data-id="<?= $transaction['id_transaction'] ?>" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-transaction-btn"
                                                    data-id="<?= $transaction['id_transaction'] ?>"
                                                    data-desc="<?= htmlspecialchars($transaction['description'] ?? 'this transaction') ?>"
                                                    title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Transaction pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php
                            // Previous page link
                            $prevPage = max(1, ($page ?? 1) - 1);
                            $disabledPrev = ($page ?? 1) <= 1 ? 'disabled' : '';

                            // Next page link
                            $nextPage = min(($totalPages ?? 1), ($page ?? 1) + 1);
                            $disabledNext = ($page ?? 1) >= ($totalPages ?? 1) ? 'disabled' : '';

                            // Create query string for pagination links
                            $queryParams = $_GET;
                            unset($queryParams['page']); // Remove existing page param
                            $queryString = http_build_query($queryParams);
                            $queryPrefix = empty($queryString) ? '?' : "?$queryString&";
                            ?>

                            <li class="page-item <?= $disabledPrev ?>">
                                <a class="page-link"
                                    href="<?= ($page ?? 1) > 1 ? $queryPrefix . 'page=' . $prevPage : '#' ?>"
                                    <?= $disabledPrev ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>

                            <?php
                            if (isset($totalPages) && $totalPages > 0):
                                // Show page numbers
                                $startPage = max(1, ($page ?? 1) - 2);
                                $endPage = min($totalPages, ($page ?? 1) + 2);

                                // Always show page 1
                                if ($startPage > 1): ?>
                                    <li class="page-item"><a class="page-link" href="<?= $queryPrefix ?>page=1">1</a></li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif;
                                endif;

                                // Page links
                                for ($i = $startPage; $i <= $endPage; $i++):
                                    $active = $i == ($page ?? 1) ? 'active' : ''; ?>
                                    <li class="page-item <?= $active ?>"><a class="page-link"
                                            href="<?= $queryPrefix ?>page=<?= $i ?>"><?= $i ?></a></li>
                                <?php endfor;

                                // Always show last page
                                if ($endPage < $totalPages):
                                    if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item"><a class="page-link"
                                            href="<?= $queryPrefix ?>page=<?= $totalPages ?>"><?= $totalPages ?></a></li>
                                <?php endif;
                            else: ?>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <?php endif; ?>

                            <li class="page-item <?= $disabledNext ?>">
                                <a class="page-link"
                                    href="<?= (($page ?? 1) < ($totalPages ?? 1)) ? $queryPrefix . 'page=' . $nextPage : '#' ?>"
                                    <?= $disabledNext ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>

            <!-- Note about adding transactions -->
            <div class="alert alert-light mt-4 mb-5">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-3 fs-4 text-primary"></i>
                    <div>
                        <strong>Adding Transactions:</strong> To add new transactions, please navigate to a specific
                        wallet.
                        Transactions must be associated with a wallet to be properly tracked.
                        <div class="mt-2">
                            <a href="/wallets" class="btn btn-sm btn-primary">
                                <i class="bi bi-wallet2 me-1"></i> Go to My Wallets
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Components -->
    <?php include 'views/components/edit_transaction_modal.php'; ?>
    <?php include 'views/components/delete_confirmation_modal.php'; ?>
    <?php include 'views/components/toast_notification.php'; ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript for Transaction Page -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle edit transaction button click
            document.querySelectorAll('.edit-transaction-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const transactionId = this.getAttribute('data-id');

                    // Show loading in button
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    this.disabled = true;

                    // Fetch transaction details
                    fetch(`/api/transactions/get?id=${transactionId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Restore button
                            this.innerHTML = originalHTML;
                            this.disabled = false;

                            if (data.success) {
                                const transaction = data.transaction;

                                // Populate form
                                document.getElementById('edit_transaction_id').value = transaction.id_transaction;
                                document.getElementById('edit_amount').value = transaction.amount;
                                document.getElementById('edit_description').value = transaction.description || '';
                                document.getElementById('edit_transaction_date').value = transaction.transaction_date.split(' ')[0]; // Get date part only
                                document.getElementById('edit_type').value = transaction.type;

                                // Set wallet ID if available
                                const walletIdField = document.getElementById('edit_wallet_id');
                                if (walletIdField) {
                                    walletIdField.value = transaction.id_wallet;
                                }

                                // Load categories based on transaction type
                                fetch(`/api/categories?type=${transaction.type}`)
                                    .then(response => response.json())
                                    .then(categories => {
                                        // Clear and populate category dropdown
                                        const editCategorySelect = document.getElementById('edit_category');
                                        editCategorySelect.innerHTML = '<option value="" selected disabled>Select category</option>';

                                        categories.forEach(category => {
                                            const option = document.createElement('option');
                                            option.value = category.id_category;
                                            option.textContent = category.name;
                                            editCategorySelect.appendChild(option);
                                        });

                                        // Set selected category
                                        editCategorySelect.value = transaction.id_category;
                                    })
                                    .catch(error => {
                                        console.error('Error fetching categories:', error);
                                        showToast('Error loading categories', false);
                                    });

                                // Show modal
                                const editModal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
                                editModal.show();
                            } else {
                                showToast(data.message, false);
                            }
                        })
                        .catch(error => {
                            // Restore button on error
                            this.innerHTML = originalHTML;
                            this.disabled = false;

                            console.error('Error fetching transaction:', error);
                            showToast('Error fetching transaction details', false);
                        });
                });
            });

            // Edit transaction form submission
            document.getElementById('editTransactionForm').addEventListener('submit', function (e) {
                e.preventDefault();

                // Show loading state on submit button
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
                submitBtn.disabled = true;

                const formData = new FormData(this);

                fetch('/transactions/update', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal 
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editTransactionModal'));
                            modal.hide();

                            // Show success message 
                            showToast(data.message, true);

                            // Reload page after a short delay 
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            // Restore button
                            submitBtn.innerHTML = originalBtnText;
                            submitBtn.disabled = false;

                            showToast(data.message, false);
                        }
                    })
                    .catch(error => {
                        // Restore button on error
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        console.error('Error updating transaction:', error);
                        showToast('An error occurred while updating the transaction', false);
                    });
            });

            // Delete transaction button handler 
            document.querySelectorAll('.delete-transaction-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const transactionId = this.getAttribute('data-id');
                    const description = this.getAttribute('data-desc');

                    // Use the reusable delete confirmation 
                    showDeleteConfirmation({
                        type: 'Transaction',
                        name: description || 'this transaction',
                        id: transactionId,
                        idField: 'id_transaction',
                        action: '/transactions/delete'
                    });
                });
            });

            // Add hover effect to transaction rows
            document.querySelectorAll('.transactions-table tbody tr').forEach(row => {
                row.addEventListener('mouseenter', function () {
                    this.querySelector('.action-buttons').style.opacity = '1';
                });

                row.addEventListener('mouseleave', function () {
                    this.querySelector('.action-buttons').style.opacity = '0.2';
                });
            });
        });
    </script>
</body>

</html>