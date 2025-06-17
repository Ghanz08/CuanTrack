<?php
require_once 'core/functions.php';
$pageTitle = 'Wallet Details';

// Ensure variables are always arrays to avoid warnings
$wallet = $wallet ?? [];
$transactions = $transactions ?? [];
$incomeTransactions = $incomeTransactions ?? [];
$expenseTransactions = $expenseTransactions ?? [];
$totalIncome = $totalIncome ?? 0;
$totalExpenses = $totalExpenses ?? 0;
$walletSubscriptions = $walletSubscriptions ?? [];
$monthlyExpenses = $monthlyExpenses ?? [];
$walletBudgets = $walletBudgets ?? [];
$recentGoals = $recentGoals ?? []; // Ensure recentGoals is defined
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include 'views/layouts/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-wrapper">
            <!-- Page Header -->
            <div class="page-header">
                <h1><?= htmlspecialchars($wallet['name'] ?? 'Wallet Details') ?></h1>
                <div>
                    <a href="/wallets" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <!-- Wallet Details -->
            <div class="wallet-details-card mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="detail-item">
                            <div class="detail-label">Balance</div>
                            <div class="detail-value">
                                <?= isset($wallet['balance']) ? format_rupiah($wallet['balance']) : '0' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <div class="detail-label">Total Income</div>
                            <div class="detail-value text-success">
                                <?= format_rupiah($totalIncome) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <div class="detail-label">Total Expenses</div>
                            <div class="detail-value text-danger">
                                <?= format_rupiah($totalExpenses) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Income/Expense Summary -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title text-success"><i class="bi bi-graph-up-arrow me-2"></i>Income
                            </div>
                            <a href="/transactions?wallet_id=<?= $wallet['id_wallet'] ?>&type=income"
                                class="btn btn-sm btn-outline-success">
                                <i class="bi bi-eye me-1"></i> View Income
                            </a>
                        </div>

                        <?php if (empty($incomeTransactions)): ?>
                            <div class="alert alert-info">
                                No income transactions found.
                            </div>
                        <?php else: ?>
                            <div class="transaction-list">
                                <?php foreach (array_slice($incomeTransactions, 0, 5) as $transaction): ?>
                                    <div class="transaction-item">
                                        <div class="transaction-icon bg-success text-white">
                                            <i class="bi bi-arrow-down"></i>
                                        </div>
                                        <div class="transaction-info">
                                            <div class="transaction-title">
                                                <?= htmlspecialchars($transaction['description'] ?? '') ?>
                                            </div>
                                            <div class="transaction-subtitle">
                                                <?= htmlspecialchars($transaction['category_name'] ?? 'Uncategorized') ?> •
                                                <?= date('d M Y', strtotime($transaction['transaction_date'] ?? 'now')) ?>
                                            </div>
                                        </div>
                                        <div class="transaction-amount amount-positive">
                                            <?= format_rupiah($transaction['amount'] ?? 0) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($incomeTransactions) > 10): ?>
                                <div class="text-center mt-3">
                                    <a href="/transactions?wallet_id=<?= $wallet['id_wallet'] ?>&type=income"
                                        class="btn btn-sm btn-outline-success">
                                        View All Income Transactions (<?= count($incomeTransactions) ?>)
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title text-danger"><i class="bi bi-graph-down-arrow me-2"></i>Expenses
                            </div>
                            <a href="/transactions?wallet_id=<?= $wallet['id_wallet'] ?>&type=expense"
                                class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-eye me-1"></i> View Expenses
                            </a>
                        </div>

                        <?php if (empty($expenseTransactions)): ?>
                            <div class="alert alert-info">
                                No expense transactions found.
                            </div>
                        <?php else: ?>
                            <div class="transaction-list">
                                <?php foreach (array_slice($expenseTransactions, 0, 5) as $transaction): ?>
                                    <div class="transaction-item">
                                        <div class="transaction-icon bg-danger text-white">
                                            <i class="bi bi-arrow-up"></i>
                                        </div>
                                        <div class="transaction-info">
                                            <div class="transaction-title">
                                                <?= htmlspecialchars($transaction['description'] ?? '') ?>
                                            </div>
                                            <div class="transaction-subtitle">
                                                <?= htmlspecialchars($transaction['category_name'] ?? 'Uncategorized') ?> •
                                                <?= date('d M Y', strtotime($transaction['transaction_date'] ?? 'now')) ?>
                                            </div>
                                        </div>
                                        <div class="transaction-amount amount-negative">
                                            <?= format_rupiah($transaction['amount'] ?? 0) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($expenseTransactions) > 10): ?>
                                <div class="text-center mt-3">
                                    <a href="/transactions?wallet_id=<?= $wallet['id_wallet'] ?>&type=expense"
                                        class="btn btn-sm btn-outline-danger">
                                        View All Expense Transactions (<?= count($expenseTransactions) ?>)
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Monthly Expenses by Category -->
            <div class="dashboard-section mb-4">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-pie-chart me-2"></i>Monthly Expenses</div>
                </div>

                <div class="row">
                    <div class="col-md-7">
                        <canvas id="expenseChart" width="100%" height="300"></canvas>
                    </div>
                    <div class="col-md-5">
                        <div class="category-legend">
                            <?php if (empty($monthlyExpenses)): ?>
                                <div class="alert alert-info">No expense data available for this month.</div>
                            <?php else: ?>
                                <?php foreach ($monthlyExpenses as $expense): ?>
                                    <div class="category-item">
                                        <div class="category-color"
                                            style="background-color: <?= $expense['color'] ?? '#' . substr(md5($expense['category']), 0, 6) ?>">
                                        </div>
                                        <div class="category-name"><?= htmlspecialchars($expense['category']) ?></div>
                                        <div class="category-amount"><?= format_rupiah($expense['amount']) ?></div>
                                        <div class="category-percentage"><?= number_format($expense['percentage'], 1) ?>%</div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wallet Budgets Section -->
            <div class="dashboard-section mb-4">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-bar-chart-steps me-2"></i>Budget Management</div>
                    <button class="btn btn-sm btn-outline-light" data-bs-toggle="modal"
                        data-bs-target="#addBudgetModal">
                        <i class="bi bi-plus-circle"></i> Add Budget
                    </button>
                </div>

                <?php if (empty($walletBudgets)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> No budgets set for this wallet.
                        <button class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addBudgetModal">
                            Create your first budget
                        </button>
                    </div>
                <?php else: ?>
                    <div class="budget-list">
                        <?php foreach ($walletBudgets as $budget): ?>
                            <div class="budget-item">
                                <div class="budget-info">
                                    <div class="budget-title"><?= htmlspecialchars($budget['category_name']) ?></div>
                                    <div class="budget-period">
                                        <?= date('d M Y', strtotime($budget['start_date'])) ?> -
                                        <?= date('d M Y', strtotime($budget['end_date'])) ?>
                                    </div>
                                </div>
                                <div class="budget-progress">
                                    <div class="progress-details">
                                        <span class="spent"><?= format_rupiah($budget['spent_amount']) ?></span>
                                        <span class="total">of <?= format_rupiah($budget['amount']) ?></span>
                                        <span
                                            class="percentage badge bg-<?= $budget['status'] ?>"><?= $budget['percentage'] ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-<?= $budget['status'] ?>" role="progressbar"
                                            style="width: <?= $budget['percentage'] ?>%"
                                            aria-valuenow="<?= $budget['percentage'] ?>" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                                <div class="budget-actions">
                                    <button class="btn btn-sm btn-outline-light edit-budget"
                                        data-id="<?= $budget['id_budget'] ?>" data-amount="<?= $budget['amount'] ?>"
                                        data-category="<?= $budget['id_category'] ?>"
                                        data-category-name="<?= htmlspecialchars($budget['category_name']) ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-budget"
                                        data-id="<?= $budget['id_budget'] ?>"
                                        data-category-name="<?= htmlspecialchars($budget['category_name']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Goal Shortcut Section - New addition -->
            <div class="dashboard-section mb-4">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-flag me-2"></i>Savings Goals</div>
                    <a href="/goals" class="btn btn-sm btn-outline-light">View All Goals</a>
                </div>

                <?php if (empty($recentGoals)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> You haven't set any savings goals yet.
                        <a href="/goals/create" class="alert-link">Create your first goal</a> to start tracking your
                        progress.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($recentGoals as $goal): ?>
                            <div class="col-md-4">
                                <div class="goal-card">
                                    <div class="goal-card-header">
                                        <div class="goal-title"><?= htmlspecialchars($goal['title']) ?></div>
                                        <div class="goal-status badge bg-<?= $goal['status_class'] ?>">
                                            <?= ucfirst($goal['status']) ?>
                                        </div>
                                    </div>

                                    <div class="goal-progress mt-3">
                                        <div class="progress-details d-flex justify-content-between mb-1">
                                            <span class="current"><?= format_rupiah($goal['current_amount']) ?></span>
                                            <span class="target text-muted">of
                                                <?= format_rupiah($goal['target_amount']) ?></span>
                                            <span class="percentage"><?= $goal['percentage'] ?>%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-<?= $goal['status_class'] ?>" role="progressbar"
                                                style="width: <?= $goal['percentage'] ?>%"
                                                aria-valuenow="<?= $goal['percentage'] ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="goal-actions mt-3">
                                        <a href="/goals/view/<?= $goal['id_goal'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Wallet Subscriptions -->
            <div class="dashboard-section mb-4">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-calendar-check me-2"></i>Subscriptions</div>
                    <a href="/subscriptions/create?wallet_id=<?= $wallet['id_wallet'] ?? '' ?>"
                        class="btn btn-sm btn-outline-light">Add New</a>
                </div>

                <?php if (empty($walletSubscriptions)): ?>
                    <div class="alert alert-info">
                        No active subscriptions for this wallet.
                    </div>
                <?php else: ?>
                    <div class="subscription-list">
                        <?php foreach ($walletSubscriptions as $subscription): ?>
                            <div class="subscription-item">
                                <div class="subscription-info">
                                    <div class="subscription-icon bg-primary text-white">
                                        <i class="bi bi-credit-card"></i>
                                    </div>
                                    <div>
                                        <div class="subscription-title"><?= htmlspecialchars($subscription['name']) ?></div>
                                        <div class="subscription-subtitle">
                                            Next payment: <?= date('d M Y', strtotime($subscription['next_due_date'])) ?>
                                            <span
                                                class="badge <?= $subscription['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= ucfirst($subscription['status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="subscription-amount">
                                    <?= format_rupiah($subscription['amount']) ?>
                                    <div class="subscription-cycle"><?= ucfirst($subscription['billing_cycle']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- All Transactions List -->
            <div class="dashboard-section">
                <div class="section-header">
                    <div class="section-title">All Transactions</div>
                    <a href="/transactions?wallet_id=<?= $wallet['id_wallet'] ?>"
                        class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> View All
                    </a>
                </div>

                <?php if (empty($transactions)): ?>
                    <div class="alert alert-info">
                        No transactions found for this wallet. Add your first transaction!
                    </div>
                <?php else: ?>
                    <div class="transaction-list">
                        <?php foreach (array_slice($transactions, 0, 10) as $transaction): ?>
                            <div class="transaction-item">
                                <div
                                    class="transaction-icon <?= $transaction['type'] == 'income' ? 'bg-success' : 'bg-danger' ?> text-white">
                                    <i class="bi <?= $transaction['type'] == 'income' ? 'bi-arrow-down' : 'bi-arrow-up' ?>"></i>
                                </div>
                                <div class="transaction-info">
                                    <div class="transaction-title"><?= htmlspecialchars($transaction['description'] ?? '') ?>
                                    </div>
                                    <div class="transaction-subtitle">
                                        <?= htmlspecialchars($transaction['category_name'] ?? 'Uncategorized') ?> •
                                        <?= date('d M Y', strtotime($transaction['transaction_date'] ?? 'now')) ?>
                                    </div>
                                </div>
                                <div
                                    class="transaction-amount <?= $transaction['type'] == 'income' ? 'amount-positive' : 'amount-negative' ?>">
                                    <?= format_rupiah($transaction['amount'] ?? 0) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($transactions) > 10): ?>
                        <div class="text-center mt-3">
                            <a href="/transactions?wallet_id=<?= $wallet['id_wallet'] ?>" class="btn btn-outline-primary">
                                View All Transactions (<?= count($transactions) ?>)
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="dashboard-footer mt-4 mb-5">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="footer-section">
                            <h5>Quick Links</h5>
                            <ul class="list-unstyled">
                                <li><a href="/transactions/create"><i class="bi bi-plus-circle me-2"></i>Add New
                                        Transaction</a></li>
                                <li><a href="/budgets"><i class="bi bi-pie-chart me-2"></i>Manage Budgets</a></li>
                                <li><a href="/reports"><i class="bi bi-bar-chart me-2"></i>View Reports</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="footer-section">
                            <h5>This Month</h5>
                            <div class="d-flex justify-content-between">
                                <span>Income:</span>
                                <span class="text-success">Rp
                                    <?= number_format($monthlyStats['income'], 0, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Expenses:</span>
                                <span class="text-danger">Rp
                                    <?= number_format($monthlyStats['expenses'], 0, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Savings:</span>
                                <span class="text-primary">Rp
                                    <?= number_format($monthlyStats['savings'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="footer-section">
                            <h5>Tips</h5>
                            <p class="small">Track daily expenses to better understand your spending habits and find
                                areas to save.</p>
                            <a href="/tips" class="btn btn-sm btn-outline-secondary">More Tips</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Action Button - Modified to always be visible -->
            <a href="#" class="add-wallet-btn" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                <span class="add-wallet-text">Add Transaction</span>
                <i class="bi bi-plus"></i>
            </a>
        </div>
    </div>

    <!-- Include Components -->
    <?php include 'views/components/add_transaction_modal.php'; ?>
    <?php include 'views/components/edit_transaction_modal.php'; ?>
    <?php include 'views/components/delete_confirmation_modal.php'; ?>
    <?php include 'views/components/toast_notification.php'; ?>
    <?php include 'views/components/add_budget_modal.php'; ?>
    <?php include 'views/components/edit_budget_modal.php'; ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js initialization script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const expenseData = <?= json_encode($monthlyExpenses) ?>;

            if (expenseData.length > 0) {
                const ctx = document.getElementById('expenseChart').getContext('2d');

                // Extract data for chart
                const labels = expenseData.map(item => item.category);
                const data = expenseData.map(item => item.amount);
                const backgroundColors = expenseData.map(item => item.color || '#36A2EB');

                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: backgroundColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.formattedValue;
                                        const dataset = context.dataset;
                                        const total = dataset.data.reduce((acc, data) => acc + data, 0);
                                        const percentage = Math.round((context.raw / total) * 100);
                                        return `${label}: Rp ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <!-- Transaction and Budget handling -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add data-id attributes to all transaction items
            document.querySelectorAll('.transaction-item').forEach((item, index) => {
                const transactionId = item.getAttribute('data-id');
                if (!transactionId) {
                    // If data-id is not set, try to find it from a child element or use index
                    const idFromChild = item.querySelector('[data-id]')?.getAttribute('data-id');
                    item.setAttribute('data-id', idFromChild || 'transaction-' + index);
                }

                // Add edit and delete buttons if not already present
                const transactionInfo = item.querySelector('.transaction-info');
                const transactionSubtitle = transactionInfo?.querySelector('.transaction-subtitle');
                const transactionAmount = item.querySelector('.transaction-amount');

                if (transactionSubtitle && !transactionSubtitle.querySelector('.edit-transaction-btn')) {
                    const type = transactionAmount.classList.contains('amount-positive') ? 'income' : 'expense';

                    // Create edit button
                    const editButton = document.createElement('button');
                    editButton.className = 'btn btn-sm btn-outline-light edit-transaction-btn ms-2';
                    editButton.innerHTML = '<i class="bi bi-pencil"></i>';
                    editButton.setAttribute('data-id', item.getAttribute('data-id'));
                    editButton.setAttribute('data-type', type);

                    // Create delete button
                    const deleteButton = document.createElement('button');
                    deleteButton.className = 'btn btn-sm btn-outline-danger delete-transaction-btn ms-1';
                    deleteButton.innerHTML = '<i class="bi bi-trash"></i>';
                    deleteButton.setAttribute('data-id', item.getAttribute('data-id'));

                    // Add buttons to the transaction item
                    transactionSubtitle.appendChild(document.createTextNode(' '));
                    transactionSubtitle.appendChild(editButton);
                    transactionSubtitle.appendChild(deleteButton);

                    // Add click event to edit button
                    editButton.addEventListener('click', function (e) {
                        e.stopPropagation();
                        const transactionId = this.getAttribute('data-id');
                        handleEditTransaction(transactionId);
                    });

                    // Add click event to delete button
                    deleteButton.addEventListener('click', function (e) {
                        e.stopPropagation();
                        const transactionId = this.getAttribute('data-id');
                        const title = item.querySelector('.transaction-title')?.textContent.trim() || 'this transaction';
                        handleDeleteTransaction(transactionId, title);
                    });
                }
            });

            // Handle edit transaction
            function handleEditTransaction(transactionId) {
                fetch(`/api/transactions/get?id=${transactionId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const transaction = data.transaction;

                            // Populate form
                            document.getElementById('edit_transaction_id').value = transaction.id_transaction;
                            document.getElementById('edit_amount').value = transaction.amount;
                            document.getElementById('edit_description').value = transaction.description || '';
                            document.getElementById('edit_transaction_date').value = transaction.transaction_date.split(' ')[0]; // Get date part only

                            // Set transaction type (hidden field)
                            document.getElementById('edit_type').value = transaction.type;

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
                                });

                            // Show modal
                            const editModal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
                            editModal.show();
                        } else {
                            showToast(data.message, false);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching transaction:', error);
                        showToast('Error fetching transaction details', false);
                    });
            }

            // Handle delete transaction
            function handleDeleteTransaction(transactionId, description) {
                showDeleteConfirmation({
                    type: 'Transaction',
                    name: description || 'this transaction',
                    id: transactionId,
                    idField: 'id_transaction',
                    action: '/transactions/delete'
                });
            }

            // Edit transaction form submission
            if (document.getElementById('editTransactionForm')) {
                document.getElementById('editTransactionForm').addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    fetch('/transactions/update', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then data => {
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
                                showToast(data.message, false);
                            }
                        })
                    .catch(error => {
                        console.error('Error updating transaction:', error);
                        showToast('An error occurred while updating the transaction', false);
                    });
            });
            }

        // Budget handling
        // Add data-id attributes to all budget items
        document.querySelectorAll('.budget-item').forEach((item, index) => {
            const budgetId = item.getAttribute('data-id');
            if (!budgetId) {
                // If data-id is not set, use index
                item.setAttribute('data-id', 'budget-' + index);
            }

            // Add edit and delete buttons if not already present
            const budgetActions = item.querySelector('.budget-actions');

            if (budgetActions && !budgetActions.querySelector('.edit-budget')) {
                // Create edit button
                const editButton = document.createElement('button');
                editButton.className = 'btn btn-sm btn-outline-light edit-budget';
                editButton.innerHTML = '<i class="bi bi-pencil"></i>';
                editButton.setAttribute('data-id', item.getAttribute('data-id'));

                // Create delete button
                const deleteButton = document.createElement('button');
                deleteButton.className = 'btn btn-sm btn-outline-danger delete-budget';
                deleteButton.innerHTML = '<i class="bi bi-trash"></i>';
                deleteButton.setAttribute('data-id', item.getAttribute('data-id'));

                // Add buttons to the budget item
                budgetActions.appendChild(editButton);
                budgetActions.appendChild(deleteButton);

                // Add click event to edit button
                editButton.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const budgetId = this.getAttribute('data-id');
                    handleEditBudget(budgetId);
                });

                // Add click event to delete button
                deleteButton.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const budgetId = this.getAttribute('data-id');
                    const categoryName = item.querySelector('.budget-title')?.textContent.trim() || 'this budget';
                    handleDeleteBudget(budgetId, categoryName);
                });
            }
        });

        // Handle edit budget
        function handleEditBudget(budgetId) {
            fetch(`/api/budgets/get?id=${budgetId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const budget = data.budget;

                        // Populate form
                        document.getElementById('edit_budget_id').value = budget.id_budget;
                        document.getElementById('edit_budget_amount').value = budget.amount;
                        document.getElementById('edit_budget_category').value = budget.id_category;
                        document.getElementById('edit_budget_start_date').value = budget.start_date.split(' ')[0]; // Get date part only
                        document.getElementById('edit_budget_end_date').value = budget.end_date.split(' ')[0]; // Get date part only

                        // Show modal
                        const editModal = new bootstrap.Modal(document.getElementById('editBudgetModal'));
                        editModal.show();
                    } else {
                        showToast(data.message, false);
                    }
                })
                .catch(error => {
                    console.error('Error fetching budget:', error);
                    showToast('Error fetching budget details', false);
                });
        }

        // Handle delete budget
        function handleDeleteBudget(budgetId, categoryName) {
            showDeleteConfirmation({
                type: 'Budget',
                name: categoryName || 'this budget',
                id: budgetId,
                idField: 'id_budget',
                action: '/budgets/delete'
            });
        }

        // Edit budget form submission
        if (document.getElementById('editBudgetForm')) {
            document.getElementById('editBudgetForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch('/budgets/update', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editBudgetModal'));
                            modal.hide();

                            // Show success message
                            showToast(data.message, true);

                            // Reload page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showToast(data.message, false);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating budget:', error);
                        showToast('An error occurred while updating the budget', false);
                    });
            });
        }
        });
    </script>

    <!-- Budget handling script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Edit budget button handler
            document.querySelectorAll('.edit-budget').forEach(button => {
                button.addEventListener('click', function () {
                    const budgetId = this.getAttribute('data-id');
                    const amount = this.getAttribute('data-amount');
                    const categoryName = this.getAttribute('data-category-name');

                    // Populate form
                    document.getElementById('edit_budget_id').value = budgetId;
                    document.getElementById('edit_amount').value = amount;
                    document.getElementById('edit_category_name').textContent = categoryName;

                    // Show modal
                    const editModal = new bootstrap.Modal(document.getElementById('editBudgetModal'));
                    editModal.show();
                });
            });

            // Delete budget button handler
            document.querySelectorAll('.delete-budget').forEach(button => {
                button.addEventListener('click', function () {
                    const budgetId = this.getAttribute('data-id');
                    const categoryName = this.getAttribute('data-category-name');

                    // Show delete confirmation dialog
                    showDeleteConfirmation({
                        type: 'Budget',
                        name: categoryName || 'this budget',
                        id: budgetId,
                        idField: 'id_budget',
                        action: '/api/budgets/delete'
                    });
                });
            });
        });
    </script>
</body>

</html>