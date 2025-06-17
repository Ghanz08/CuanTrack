<?php
require_once 'core/functions.php';
$pageTitle = 'Subscriptions';

// Ensure variables are always arrays to avoid warnings
$subscriptions = $subscriptions ?? [];
$wallets = $wallets ?? [];
$totalMonthlyCost = $totalMonthlyCost ?? 0;
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
                    <a href="/subscription/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Subscription
                    </a>
                </div>
            </div>

            <!-- Subscription Summary -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="dashboard-section h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-calendar-check fs-1 text-primary"></i>
                            </div>
                            <div>
                                <div class="section-title mb-1 text-white">Total Monthly Cost</div>
                                <div class="summary-value fs-4 fw-bold text-white">
                                    <?= format_rupiah($totalMonthlyCost) ?>
                                </div>
                                <div class="summary-subtitle small text-white-50">
                                    <?= count($subscriptions) ?> active subscriptions
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-section h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-wallet fs-1 text-success"></i>
                            </div>
                            <div>
                                <div class="section-title mb-1 text-white">Next Payment</div>
                                <?php
                                $nextPayment = null;
                                $nextPaymentDate = null;
                                foreach ($subscriptions as $subscription) {
                                    if ($subscription['status'] == 'active') {
                                        $dueDate = strtotime($subscription['next_due_date']);
                                        if (!$nextPaymentDate || $dueDate < $nextPaymentDate) {
                                            $nextPayment = $subscription;
                                            $nextPaymentDate = $dueDate;
                                        }
                                    }
                                }
                                ?>
                                <?php if ($nextPayment): ?>
                                    <div class="summary-value fs-4 fw-bold text-white">
                                        <?= format_rupiah($nextPayment['amount']) ?>
                                    </div>
                                    <div class="summary-subtitle small text-white-50">
                                        <?= $nextPayment['name'] ?> on
                                        <?= date('d M Y', strtotime($nextPayment['next_due_date'])) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="summary-value fs-4 fw-bold text-white">
                                        No upcoming payments
                                    </div>
                                    <div class="summary-subtitle small text-white-50">
                                        No active subscriptions
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
                                <i class="bi bi-bar-chart fs-1 text-info"></i>
                            </div>
                            <div>
                                <div class="section-title mb-1 text-white">Annual Cost</div>
                                <div class="summary-value fs-4 fw-bold text-white">
                                    <?= format_rupiah($totalMonthlyCost * 12) ?>
                                </div>
                                <div class="summary-subtitle small text-white-50">
                                    <?= format_rupiah($totalMonthlyCost) ?> per month
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscriptions List -->
            <div class="dashboard-section">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-calendar-check me-2"></i>Your Subscriptions</div>
                </div>

                <?php if (empty($subscriptions)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> You don't have any subscriptions yet.
                        <a href="/subscription/create" class="alert-link">Add your first subscription</a> to track recurring
                        payments.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover transactions-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Billing Cycle</th>
                                    <th>Next Due Date</th>
                                    <th>Wallet</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subscriptions as $subscription): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="subscription-icon bg-primary text-white me-2">
                                                    <i class="bi bi-credit-card"></i>
                                                </div>
                                                <span><?= htmlspecialchars($subscription['name']) ?></span>
                                            </div>
                                        </td>
                                        <td><?= format_rupiah($subscription['amount']) ?></td>
                                        <td><?= ucfirst($subscription['billing_cycle']) ?></td>
                                        <td><?= date('d M Y', strtotime($subscription['next_due_date'])) ?></td>
                                        <td><?= htmlspecialchars($subscription['wallet_name']) ?></td>
                                        <td>
                                            <span
                                                class="badge bg-<?= $subscription['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst($subscription['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="/subscription/edit/<?= $subscription['id_subscription'] ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-subscription"
                                                    data-id="<?= $subscription['id_subscription'] ?>"
                                                    data-name="<?= htmlspecialchars($subscription['name']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tips Section -->
            <div class="dashboard-section mt-4">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-lightbulb me-2"></i>Subscription Management Tips</div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="tip-card">
                            <div class="tip-icon bg-primary">
                                <i class="bi bi-calendar"></i>
                            </div>
                            <div class="tip-title">Review Regularly</div>
                            <div class="tip-text">Review your subscriptions quarterly to identify any services you no
                                longer use.</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="tip-card">
                            <div class="tip-icon bg-success">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="tip-title">Annual vs Monthly</div>
                            <div class="tip-text">Consider annual plans for services you use regularly. Many offer
                                discounts for yearly commitments.</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="tip-card">
                            <div class="tip-icon bg-info">
                                <i class="bi bi-share"></i>
                            </div>
                            <div class="tip-title">Share Accounts</div>
                            <div class="tip-text">Consider family plans or sharing subscriptions with trusted friends to
                                reduce individual costs.</div>
                        </div>
                    </div>
                </div>
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
            // Delete subscription button click handler
            document.querySelectorAll('.delete-subscription').forEach(button => {
                button.addEventListener('click', function () {
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

            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

            // Hovering on table rows shows action buttons
            document.querySelectorAll('.transactions-table tbody tr').forEach(row => {
                row.addEventListener('mouseenter', function () {
                    this.querySelector('.action-buttons').style.opacity = 1;
                });

                row.addEventListener('mouseleave', function () {
                    this.querySelector('.action-buttons').style.opacity = 0.2;
                });
            });
        });
    </script>
</body>

</html>