<?php
require_once 'core/functions.php';
$pageTitle = 'Add Subscription';

// Ensure variables are always arrays to avoid warnings
$wallets = $wallets ?? [];
$selectedWalletId = $selectedWalletId ?? null;
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
                    <a href="/subscription" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Subscriptions
                    </a>
                </div>
            </div>

            <!-- Create Subscription Form -->
            <div class="dashboard-section">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-calendar-check me-2"></i>New Subscription</div>
                </div>

                <form action="/subscription/store" method="POST" id="createSubscriptionForm">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Subscription Name*</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="e.g., Netflix, Spotify, Gym Membership" required>
                        </div>
                        <div class="col-md-6">
                            <label for="wallet_id" class="form-label">Wallet*</label>
                            <select class="form-select" id="wallet_id" name="wallet_id" required>
                                <option value="" selected disabled>Select wallet</option>
                                <?php foreach ($wallets as $wallet): ?>
                                    <option value="<?= $wallet['id_wallet'] ?>" <?= $selectedWalletId == $wallet['id_wallet'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($wallet['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount*</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="amount" name="amount" placeholder="0"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="billing_cycle" class="form-label">Billing Cycle*</label>
                            <select class="form-select" id="billing_cycle" name="billing_cycle" required>
                                <option value="monthly" selected>Monthly</option>
                                <option value="yearly">Yearly</option>
                                <option value="weekly">Weekly</option>
                                <option value="daily">Daily</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="next_due_date" class="form-label">Next Due Date*</label>
                            <input type="date" class="form-control" id="next_due_date" name="next_due_date"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status*</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Subscription Preview Card -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="goal-preview">
                                <h5>Subscription Preview</h5>
                                <div class="subscription-item mb-0">
                                    <div class="subscription-info">
                                        <div class="subscription-icon bg-primary text-white">
                                            <i class="bi bi-credit-card"></i>
                                        </div>
                                        <div>
                                            <div class="subscription-title" id="preview_name">New Subscription</div>
                                            <div class="subscription-subtitle">
                                                Next payment: <span id="preview_date"><?= date('d M Y') ?></span>
                                                <span class="badge bg-success" id="preview_status">Active</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="subscription-amount">
                                        <div id="preview_amount">Rp 0</div>
                                        <div class="subscription-cycle" id="preview_cycle">Monthly</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Create Subscription
                            </button>
                            <a href="/subscription" class="btn btn-outline-secondary ms-2">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Components -->
    <?php include 'views/components/toast_notification.php'; ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get form elements
            const nameInput = document.getElementById('name');
            const amountInput = document.getElementById('amount');
            const billingCycleSelect = document.getElementById('billing_cycle');
            const nextDueDateInput = document.getElementById('next_due_date');
            const statusSelect = document.getElementById('status');

            // Get preview elements
            const previewName = document.getElementById('preview_name');
            const previewAmount = document.getElementById('preview_amount');
            const previewCycle = document.getElementById('preview_cycle');
            const previewDate = document.getElementById('preview_date');
            const previewStatus = document.getElementById('preview_status');

            // Format currency
            function formatCurrency(amount) {
                return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
            }

            // Format date
            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            }

            // Update preview
            function updatePreview() {
                const name = nameInput.value || 'New Subscription';
                const amount = parseFloat(amountInput.value) || 0;
                const billingCycle = billingCycleSelect.options[billingCycleSelect.selectedIndex].text;
                const nextDueDate = nextDueDateInput.value;
                const status = statusSelect.value;

                // Update preview elements
                previewName.textContent = name;
                previewAmount.textContent = formatCurrency(amount);
                previewCycle.textContent = billingCycle;
                previewDate.textContent = formatDate(nextDueDate);

                previewStatus.textContent = status === 'active' ? 'Active' : 'Inactive';
                previewStatus.className = `badge ${status === 'active' ? 'bg-success' : 'bg-secondary'}`;
            }

            // Add event listeners
            nameInput.addEventListener('input', updatePreview);
            amountInput.addEventListener('input', updatePreview);
            billingCycleSelect.addEventListener('change', updatePreview);
            nextDueDateInput.addEventListener('change', updatePreview);
            statusSelect.addEventListener('change', updatePreview);

            // Initial update
            updatePreview();
        });
    </script>
</body>

</html>