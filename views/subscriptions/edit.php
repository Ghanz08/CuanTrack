<?php
require_once 'core/functions.php';
$pageTitle = 'Edit Subscription';

// Ensure variables are always arrays to avoid warnings
$subscription = $subscription ?? [];
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
                <h1><?= $pageTitle ?></h1>
                <div>
                    <a href="/subscription" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Subscriptions
                    </a>
                </div>
            </div>

            <!-- Edit Subscription Form -->
            <div class="dashboard-section">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-calendar-check me-2"></i>Edit Subscription</div>
                </div>

                <form action="/subscription/update" method="POST" id="editSubscriptionForm">
                    <input type="hidden" name="id_subscription" value="<?= $subscription['id_subscription'] ?? '' ?>">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Subscription Name*</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="e.g., Netflix, Spotify, Gym Membership"
                                value="<?= htmlspecialchars($subscription['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="wallet_id" class="form-label">Wallet*</label>
                            <select class="form-select" id="wallet_id" name="wallet_id" required>
                                <option value="" disabled>Select wallet</option>
                                <?php foreach ($wallets as $wallet): ?>
                                    <option value="<?= $wallet['id_wallet'] ?>" <?= ($subscription['id_wallet'] ?? '') == $wallet['id_wallet'] ? 'selected' : '' ?>>
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
                                    value="<?= $subscription['amount'] ?? 0 ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="billing_cycle" class="form-label">Billing Cycle*</label>
                            <select class="form-select" id="billing_cycle" name="billing_cycle" required>
                                <option value="monthly" <?= ($subscription['billing_cycle'] ?? '') == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                <option value="yearly" <?= ($subscription['billing_cycle'] ?? '') == 'yearly' ? 'selected' : '' ?>>Yearly</option>
                                <option value="weekly" <?= ($subscription['billing_cycle'] ?? '') == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                                <option value="daily" <?= ($subscription['billing_cycle'] ?? '') == 'daily' ? 'selected' : '' ?>>Daily</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="next_due_date" class="form-label">Next Due Date*</label>
                            <input type="date" class="form-control" id="next_due_date" name="next_due_date"
                                value="<?= date('Y-m-d', strtotime($subscription['next_due_date'] ?? 'now')) ?>"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status*</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" <?= ($subscription['status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($subscription['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
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
                                            <div class="subscription-title" id="preview_name">
                                                <?= htmlspecialchars($subscription['name'] ?? 'Subscription') ?>
                                            </div>
                                            <div class="subscription-subtitle">
                                                Next payment: <span id="preview_date">
                                                    <?= date('d M Y', strtotime($subscription['next_due_date'] ?? 'now')) ?>
                                                </span>
                                                <span
                                                    class="badge <?= ($subscription['status'] ?? '') == 'active' ? 'bg-success' : 'bg-secondary' ?>"
                                                    id="preview_status">
                                                    <?= ucfirst($subscription['status'] ?? 'Active') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="subscription-amount">
                                        <div id="preview_amount"><?= format_rupiah($subscription['amount'] ?? 0) ?>
                                        </div>
                                        <div class="subscription-cycle" id="preview_cycle">
                                            <?= ucfirst($subscription['billing_cycle'] ?? 'Monthly') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Update Subscription
                            </button>
                            <a href="/subscription" class="btn btn-outline-secondary ms-2">Cancel</a>
                            <button type="button" class="btn btn-danger float-end delete-subscription"
                                data-id="<?= $subscription['id_subscription'] ?? '' ?>"
                                data-name="<?= htmlspecialchars($subscription['name'] ?? '') ?>">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Components -->
    <?php include 'views/components/toast_notification.php'; ?>
    <?php include 'views/components/delete_confirmation_modal.php'; ?>

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
                const name = nameInput.value || 'Subscription';
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

            // Delete subscription button handler
            document.querySelector('.delete-subscription')?.addEventListener('click', function (e) {
                e.preventDefault();
                const subscriptionId = this.getAttribute('data-id');
                const subscriptionName = this.getAttribute('data-name');

                // Show delete confirmation dialog
                showDeleteConfirmation({
                    type: 'Subscription',
                    name: subscriptionName,
                    id: subscriptionId,
                    idField: 'id_subscription',
                    action: '/subscription/delete'
                });
            });
        });
    </script>
</body>

</html>