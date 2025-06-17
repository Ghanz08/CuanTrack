<?php
require_once 'core/functions.php';
$pageTitle = 'Budget Management';

// Ensure variables are always arrays to avoid warnings
$budgets = $budgets ?? [];
$budgetPerformance = $budgetPerformance ?? [];
$expenseCategories = $expenseCategories ?? [];
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
                <h1><?= $pageTitle ?></h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBudgetModal">
                    <i class="bi bi-plus-circle me-2"></i>Create Budget
                </button>
            </div>

            <!-- Budget Overview Section -->
            <div class="dashboard-section mb-4">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-pie-chart me-2"></i>Budget Overview</div>
                </div>

                <?php if (empty($budgetPerformance)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> You haven't set up any budgets yet. Create your first budget
                        to start tracking your spending.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-7">
                            <canvas id="budgetChart" height="250"></canvas>
                        </div>
                        <div class="col-md-5">
                            <div class="budget-summary">
                                <h5>This Month's Summary</h5>
                                <div class="d-flex justify-content-between text-muted mb-2">
                                    <span>Total Budgeted:</span>
                                    <span>
                                        <?= format_rupiah(array_sum(array_column($budgetPerformance, 'budget_amount'))) ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between text-muted mb-3">
                                    <span>Total Spent:</span>
                                    <span>
                                        <?= format_rupiah(array_sum(array_column($budgetPerformance, 'actual_amount'))) ?>
                                    </span>
                                </div>

                                <div class="overall-progress mb-4">
                                    <?php
                                    $totalBudget = array_sum(array_column($budgetPerformance, 'budget_amount'));
                                    $totalSpent = array_sum(array_column($budgetPerformance, 'actual_amount'));
                                    $overallPercentage = $totalBudget > 0 ? min(100, round(($totalSpent / $totalBudget) * 100)) : 0;
                                    $progressClass = $overallPercentage < 70 ? 'bg-success' : ($overallPercentage < 90 ? 'bg-warning' : 'bg-danger');
                                    ?>
                                    <label class="form-label d-flex justify-content-between">
                                        <span>Overall Progress</span>
                                        <span><?= $overallPercentage ?>%</span>
                                    </label>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar <?= $progressClass ?>" role="progressbar"
                                            style="width: <?= $overallPercentage ?>%"
                                            aria-valuenow="<?= $overallPercentage ?>" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Budget List Section -->
            <div class="dashboard-section">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-list-ul me-2"></i>Your Budgets</div>
                </div>

                <?php if (empty($budgets)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> No budgets found. Click the "Create Budget" button to add
                        your first budget.
                    </div>
                <?php else: ?>
                    <div class="budget-list">
                        <?php foreach ($budgetPerformance as $budget): ?>
                            <div class="budget-item">
                                <div class="budget-info">
                                    <div class="budget-title"><?= htmlspecialchars($budget['category']) ?></div>
                                    <div class="budget-period">
                                        <?= date('d M Y', strtotime($budget['start_date'])) ?> -
                                        <?= date('d M Y', strtotime($budget['end_date'])) ?>
                                    </div>
                                </div>
                                <div class="budget-progress">
                                    <div class="progress-details">
                                        <span class="spent"><?= format_rupiah($budget['actual_amount']) ?></span>
                                        <span class="total">of <?= format_rupiah($budget['budget_amount']) ?></span>
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
                                        data-id="<?= $budget['id_budget'] ?>" data-amount="<?= $budget['budget_amount'] ?>"
                                        data-category="<?= htmlspecialchars($budget['category']) ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-budget"
                                        data-id="<?= $budget['id_budget'] ?>"
                                        data-category="<?= htmlspecialchars($budget['category']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Floating Action Button -->
            <a href="#" class="add-wallet-btn" data-bs-toggle="modal" data-bs-target="#addBudgetModal">
                <span class="add-wallet-text">Add Budget</span>
                <i class="bi bi-plus"></i>
            </a>
        </div>
    </div>

    <!-- Include Budget Modal -->
    <?php include 'views/components/add_budget_modal.php'; ?>
    <?php include 'views/components/edit_budget_modal.php'; ?>
    <?php include 'views/components/delete_budget_modal.php'; ?>
    <?php include 'views/components/toast_notification.php'; ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Budget JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart initialization for budget visualization
            const budgetData = <?= json_encode($budgetPerformance) ?>;

            if (budgetData.length > 0) {
                const ctx = document.getElementById('budgetChart').getContext('2d');

                // Prepare data for chart
                const labels = budgetData.map(item => item.category);
                const budgetAmounts = budgetData.map(item => item.budget_amount);
                const actualAmounts = budgetData.map(item => item.actual_amount);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Budget',
                                data: budgetAmounts,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Actual',
                                data: actualAmounts,
                                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#eef0f2'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#eef0f2'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#eef0f2'
                                }
                            }
                        }
                    }
                });
            }

            // Edit budget button handler
            document.querySelectorAll('.edit-budget').forEach(button => {
                button.addEventListener('click', function () {
                    const budgetId = this.getAttribute('data-id');
                    const amount = this.getAttribute('data-amount');
                    const category = this.getAttribute('data-category');

                    document.getElementById('edit_budget_id').value = budgetId;
                    document.getElementById('edit_amount').value = amount;
                    document.getElementById('edit_category_name').textContent = category;

                    const editModal = new bootstrap.Modal(document.getElementById('editBudgetModal'));
                    editModal.show();
                });
            });

            // Delete budget button handler
            document.querySelectorAll('.delete-budget').forEach(button => {
                button.addEventListener('click', function () {
                    const budgetId = this.getAttribute('data-id');
                    const category = this.getAttribute('data-category');

                    document.getElementById('deleteBudgetId').value = budgetId;
                    document.getElementById('deleteBudgetCategory').textContent = category;

                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteBudgetModal'));
                    deleteModal.show();
                });
            });
        });
    </script>
</body>

</html>