<?php
$pageTitle = 'Dashboard';

// Ensure variables are always arrays to avoid warnings
$wallets = $wallets ?? [];
$transactions = $transactions ?? [];
$goals = $goals ?? [];
$subscriptions = $subscriptions ?? [];
$monthlyExpenses = $monthlyExpenses ?? [];
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
                <h1>Dashboard</h1>
            </div>

            <!-- Wallet Cards -->
            <div class="wallet-cards">
                <?php foreach ($wallets as $wallet): ?>
                    <a href="/wallets/view/<?= $wallet['id_wallet'] ?>" class="wallet-card text-decoration-none text-dark">
                        <div class="wallet-name"><?= htmlspecialchars($wallet['name']) ?></div>
                        <div class="wallet-balance"><?= format_rupiah($wallet['balance']) ?></div>
                        <div class="wallet-stats">
                            <span class="text-success"><i class="bi bi-arrow-up-circle"></i>
                                <?= format_rupiah($wallet['total_income'] ?? 0) ?></span>
                            <span class="text-danger"><i class="bi bi-arrow-down-circle"></i>
                                <?= format_rupiah($wallet['total_expenses'] ?? 0) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Dashboard Content -->
            <div class="row g-3">
                <!-- Latest Transactions -->
                <div class="col-md-4">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title">Last Transaction</div>
                            <a href="/transactions" class="btn btn-sm btn-outline-light">View All</a>
                        </div>
                        <?php
                        // Sort transactions by transaction_date in descending order
                        usort($transactions, function ($a, $b) {
                            $dateA = $a['transaction_date'] ?? null;
                            $dateB = $b['transaction_date'] ?? null;
                            return strtotime($dateB) - strtotime($dateA);
                        });
                        foreach (array_slice($transactions, 0, 3) as $transaction): ?>
                            <div class="transaction-item">
                                <div
                                    class="transaction-icon <?= $transaction['type'] == 'income' ? 'bg-success' : 'bg-danger' ?> text-white">
                                    <i
                                        class="bi <?= $transaction['type'] == 'income' ? 'bi-arrow-down' : 'bi-arrow-up' ?>"></i>
                                </div>
                                <div class="transaction-info">
                                    <div class="transaction-title"><?= htmlspecialchars($transaction['description']) ?>
                                    </div>
                                    <div class="transaction-subtitle"><?= htmlspecialchars($transaction['category_name']) ?>
                                    </div>
                                </div>
                                <div
                                    class="transaction-amount <?= $transaction['type'] == 'income' ? 'amount-positive' : 'amount-negative' ?>">
                                    <?= format_rupiah($transaction['amount']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Goals -->
                <div class="col-md-4">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title">Goals</div>
                            <a href="/goals" class="btn btn-sm btn-outline-light">View All</a>
                        </div>
                        <?php foreach ($goals as $goal): ?>
                            <div class="goal-item">
                                <div class="goal-info w-100">
                                    <div class="goal-icon bg-primary text-white">
                                        <i class="bi bi-bullseye"></i>
                                    </div>
                                    <div class="goal-details">
                                        <div class="goal-title mb-1"><?= htmlspecialchars($goal['title']) ?></div>
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                style="width: <?= $goal['percentage'] ?>%"
                                                aria-valuenow="<?= $goal['percentage'] ?>" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <div class="goal-stats d-flex justify-content-between">
                                            <div class="goal-currency">Rp.
                                                <?= number_format($goal['current_amount'], 0, ',', '.') ?> / Rp.
                                                <?= number_format($goal['target_amount'], 0, ',', '.') ?>
                                            </div>
                                            <span class="badge bg-primary"><?= $goal['percentage'] ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Subscriptions -->
                <div class="col-md-4">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title">All Subscription</div>
                        </div>
                        <?php foreach ($subscriptions as $subscription): ?>
                            <div class="subscription-item">
                                <div class="subscription-info">
                                    <div class="subscription-icon">
                                        <i class="bi bi-credit-card"></i>
                                    </div>
                                    <div>
                                        <div class="subscription-title"><?= htmlspecialchars($subscription['name']) ?></div>
                                    </div>
                                </div>
                                <div class="subscription-amount">
                                    Rp. <?= number_format($subscription['amount'], 0, ',', '.') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Income vs Expense Chart -->
            <div class="dashboard-section mb-4">
                <div class="section-header">
                    <div class="section-title">Income vs Expense</div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <canvas id="incomeExpenseChart" height="250"></canvas>
                    </div>
                    <div class="col-md-4">
                        <div class="financial-stats">
                            <div class="stat-item">
                                <div class="stat-label">Income</div>
                                <div class="stat-value text-success"><?= format_rupiah($monthlyStats['income'] ?? 0) ?>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Expenses</div>
                                <div class="stat-value text-danger"><?= format_rupiah($monthlyStats['expenses'] ?? 0) ?>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Savings</div>
                                <div class="stat-value text-primary"><?= format_rupiah($monthlyStats['savings'] ?? 0) ?>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Savings Rate</div>
                                <div class="stat-value">
                                    <?= $monthlyStats['income'] > 0
                                        ? number_format(($monthlyStats['savings'] / $monthlyStats['income']) * 100, 1) . '%'
                                        : '0%' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Expense Chart -->
            <div class="dashboard-section mb-4">
                <div class="section-header">
                    <div class="section-title">Monthly Expense Breakdown</div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <canvas id="expenseChart" height="250"></canvas>
                    </div>
                    <div class="col-md-4">
                        <div class="static-chart">
                            <ul class="list-unstyled">
                                <?php if (empty($monthlyExpenses)): ?>
                                    <li>No expense data available for this month.</li>
                                <?php else: ?>
                                    <?php foreach ($monthlyExpenses as $expense): ?>
                                        <li>
                                            <span style="color: <?= $expense['color'] ?? '#36A2EB' ?>;">●</span>
                                            <?= htmlspecialchars($expense['category']) ?>: Rp
                                            <?= number_format($expense['amount'], 0, ',', '.') ?>
                                            (<?= number_format($expense['percentage'], 2) ?>%)
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Footer -->
            <div class="dashboard-footer mt-4 mb-5">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="footer-section">
                            <h5>Quick Links</h5>
                            <ul class="list-unstyled">
                                <li><a href="/transactions"><i class="bi bi-list-ul me-2"></i>View All Transactions</a>
                                </li>
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

            <!-- Floating Add Button - Modified to always be visible -->
            <a href="/wallets/create" class="add-wallet-btn">
                <span class="add-wallet-text">Add Wallet</span>
                <i class="bi bi-plus"></i>
            </a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Income vs Expense Chart
            const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
            const monthlyIncome = <?= $monthlyStats['income'] ?? 0 ?>;
            const monthlyExpenses = <?= $monthlyStats['expenses'] ?? 0 ?>;

            new Chart(incomeExpenseCtx, {
                type: 'bar',
                data: {
                    labels: ['Income', 'Expenses'],
                    datasets: [{
                        data: [monthlyIncome, monthlyExpenses],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Expense breakdown chart
            const ctx = document.getElementById('expenseChart').getContext('2d');
            const expenseData = <?= json_encode(array_column($monthlyExpenses, 'amount')) ?>;
            const expenseLabels = <?= json_encode(array_column($monthlyExpenses, 'category')) ?>;
            const expenseColors = <?= json_encode(array_map(function ($item) {
                return $item['color'] ?? '#36A2EB';
            }, $monthlyExpenses)) ?>;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: expenseLabels,
                    datasets: [{
                        data: expenseData,
                        backgroundColor: expenseColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right'
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>