<?php
require_once 'core/functions.php';
$pageTitle = 'Create New Wallet';

// Get error message if it exists
$error = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'name_required':
            $error = 'Wallet name is required.';
            break;
        case 'creation_failed':
            $error = 'Failed to create wallet. Please try again.';
            break;
        default:
            $error = 'An error occurred. Please try again.';
    }
}
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
                    <a href="/wallets" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Wallets
                    </a>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-section">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-wallet2 me-2"></i>Wallet Details</div>
                </div>

                <form action="/wallets" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Wallet Name *</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="e.g., Cash, Bank BCA, Savings" required>
                        <div class="form-text text-muted">Give your wallet a clear and descriptive name.</div>
                    </div>

                    <div class="mb-3">
                        <label for="balance" class="form-label">Initial Balance</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="balance" name="balance" placeholder="0"
                                min="0" step="1000">
                        </div>
                        <div class="form-text text-muted">Enter the current amount in this wallet.</div>
                    </div>

                    <div class="mb-3">
                        <label for="currency" class="form-label">Currency</label>
                        <select class="form-select" id="currency" name="currency">
                            <option value="IDR" selected>Indonesian Rupiah (IDR)</option>
                            <option value="USD">US Dollar (USD)</option>
                            <option value="EUR">Euro (EUR)</option>
                            <option value="SGD">Singapore Dollar (SGD)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i> All fields marked with * are required.
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Create Wallet
                        </button>
                        <a href="/wallets" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>