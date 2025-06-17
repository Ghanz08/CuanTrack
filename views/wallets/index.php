<?php
$pageTitle = 'All Wallets';
require_once 'core/functions.php';

// Ensure wallets is always an array to avoid warnings
$wallets = $wallets ?? [];
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
                <h1>All Wallets</h1>
                <div>
                    <a href="/transactions" class="btn btn-outline-primary me-2">
                        <i class="bi bi-list-ul"></i> View All Transactions
                    </a>
                </div>
            </div>

            <!-- Info Alert -->
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="alert alert-warning mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i> This is a demo view. To save wallet data, please
                    <a href="/login" class="alert-link">login</a> first.
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i> Select a wallet to view details and add transactions.
                    You can also <a href="/transactions" class="alert-link">view all transactions</a> across all wallets.
                </div>
            <?php endif; ?>

            <!-- Wallet Cards -->
            <div class="wallet-cards">
                <?php foreach ($wallets as $wallet): ?>
                    <a href="/wallets/view/<?= $wallet['id_wallet'] ?>" class="wallet-card text-decoration-none text-dark">
                        <div class="wallet-name"><?= htmlspecialchars($wallet['name'] ?? '') ?></div>
                        <div class="wallet-balance"><?= format_rupiah($wallet['balance'] ?? 0) ?></div>
                        <div class="mt-2 text-center">
                            <span class="badge bg-primary">Click to view & add transactions</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Dashboard Footer -->
            <div class="dashboard-footer mt-4 mb-5">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="footer-section">
                            <h5>Manage Your Wallets</h5>
                            <p class="small">Create different wallets to categorize your funds and better track your
                                finances.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="footer-section">
                            <h5>Quick Links</h5>
                            <ul class="list-unstyled">
                                <li>
                                    <a href="/transactions"><i class="bi bi-list-ul me-2"></i>View All Transactions</a>
                                </li>
                                <li>
                                    <a href="/budget"><i class="bi bi-pie-chart me-2"></i>Manage Budgets</a>
                                </li>
                            </ul>
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
</body>

</html>