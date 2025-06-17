<?php
require_once 'core/functions.php';
$pageTitle = 'Edit Goal';

// Ensure goal data is available
$goal = $goal ?? [];
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
                    <a href="/goals" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Goals
                    </a>
                </div>
            </div>

            <!-- Edit Goal Form -->
            <div class="dashboard-section">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-flag me-2"></i>Update Goal</div>
                </div>

                <form action="/goals/update" method="POST" id="editGoalForm">
                    <input type="hidden" name="id_goal" value="<?= $goal['id_goal'] ?? '' ?>">

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="title" class="form-label">Goal Name*</label>
                            <input type="text" class="form-control" id="title" name="title"
                                placeholder="e.g., New Laptop, Vacation, Emergency Fund"
                                value="<?= htmlspecialchars($goal['title'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="target_amount" class="form-label">Target Amount*</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="target_amount" name="target_amount"
                                    placeholder="0" value="<?= $goal['target_amount'] ?? 0 ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="current_amount" class="form-label">Current Savings</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="current_amount" name="current_amount"
                                    placeholder="0" value="<?= $goal['current_amount'] ?? 0 ?>">
                            </div>
                            <div class="form-text text-light">Amount you've already saved towards this goal</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="target_date" class="form-label">Target Date*</label>
                            <input type="date" class="form-control" id="target_date" name="target_date"
                                value="<?= date('Y-m-d', strtotime($goal['target_date'] ?? 'now +3 months')) ?>"
                                required>
                        </div>
                    </div>

                    <!-- Goal Preview Card -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="goal-preview">
                                <h5>Goal Preview</h5>
                                <div class="goal-card">
                                    <div class="goal-card-header">
                                        <div class="goal-title" id="preview_title">
                                            <?= htmlspecialchars($goal['title'] ?? 'Your Goal') ?></div>
                                        <div class="goal-status badge bg-<?= $goal['status_class'] ?? 'info' ?>"
                                            id="preview_status">
                                            <?= ucfirst($goal['status'] ?? 'Active') ?>
                                        </div>
                                    </div>

                                    <div class="goal-progress mt-3">
                                        <div class="progress-details d-flex justify-content-between mb-1">
                                            <span class="current"
                                                id="preview_current"><?= format_rupiah($goal['current_amount'] ?? 0) ?></span>
                                            <span class="target text-muted" id="preview_target">of
                                                <?= format_rupiah($goal['target_amount'] ?? 0) ?></span>
                                            <span class="percentage"
                                                id="preview_percentage"><?= $goal['percentage'] ?? 0 ?>%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-<?= $goal['status_class'] ?? 'info' ?>"
                                                role="progressbar" id="preview_progress_bar"
                                                style="width: <?= $goal['percentage'] ?? 0 ?>%"
                                                aria-valuenow="<?= $goal['percentage'] ?? 0 ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="goal-meta mt-3 d-flex justify-content-between">
                                        <div class="goal-deadline">
                                            <i class="bi bi-calendar me-1"></i>
                                            <span
                                                id="preview_date"><?= date('d M Y', strtotime($goal['target_date'] ?? 'now +3 months')) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Update Goal
                            </button>
                            <a href="/goals" class="btn btn-outline-secondary ms-2">Cancel</a>
                            <button type="button" class="btn btn-danger float-end delete-goal"
                                data-id="<?= $goal['id_goal'] ?? '' ?>"
                                data-name="<?= htmlspecialchars($goal['title'] ?? '') ?>">
                                <i class="bi bi-trash me-1"></i> Delete Goal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Goal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this goal? This action cannot be undone.</p>
                    <p class="fw-bold" id="delete-goal-name"><?= htmlspecialchars($goal['title'] ?? '') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="/goals/delete" method="POST">
                        <input type="hidden" name="id_goal" value="<?= $goal['id_goal'] ?? '' ?>">
                        <button type="submit" class="btn btn-danger">Delete Goal</button>
                    </form>
                </div>
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
            const titleInput = document.getElementById('title');
            const targetAmountInput = document.getElementById('target_amount');
            const currentAmountInput = document.getElementById('current_amount');
            const targetDateInput = document.getElementById('target_date');

            // Get preview elements
            const previewTitle = document.getElementById('preview_title');
            const previewCurrent = document.getElementById('preview_current');
            const previewTarget = document.getElementById('preview_target');
            const previewPercentage = document.getElementById('preview_percentage');
            const previewProgressBar = document.getElementById('preview_progress_bar');
            const previewDate = document.getElementById('preview_date');

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

            // Calculate completion percentage
            function calculatePercentage(current, target) {
                if (target <= 0) return 0;
                return Math.min(100, Math.round((current / target) * 100));
            }

            // Update preview
            function updatePreview() {
                const title = titleInput.value || 'Your Goal';
                const targetAmount = parseFloat(targetAmountInput.value) || 0;
                const currentAmount = parseFloat(currentAmountInput.value) || 0;
                const targetDate = targetDateInput.value;

                // Calculate percentage
                const percentage = calculatePercentage(currentAmount, targetAmount);

                // Update preview elements
                previewTitle.textContent = title;
                previewCurrent.textContent = formatCurrency(currentAmount);
                previewTarget.textContent = 'of ' + formatCurrency(targetAmount);
                previewPercentage.textContent = percentage + '%';
                previewProgressBar.style.width = percentage + '%';
                previewProgressBar.setAttribute('aria-valuenow', percentage);
                previewDate.textContent = formatDate(targetDate);

                // Update status based on percentage
                const statusClass = percentage >= 100 ? 'success' : 'info';
                previewProgressBar.className = `progress-bar bg-${statusClass}`;
            }

            // Add event listeners
            titleInput.addEventListener('input', updatePreview);
            targetAmountInput.addEventListener('input', updatePreview);
            currentAmountInput.addEventListener('input', updatePreview);
            targetDateInput.addEventListener('change', updatePreview);

            // Initial update
            updatePreview();

            // Delete goal button handler
            document.querySelector('.delete-goal')?.addEventListener('click', function () {
                const goalId = this.getAttribute('data-id');
                const goalName = this.getAttribute('data-name');

                // Update modal content
                document.getElementById('delete-goal-name').textContent = goalName;

                // Show the delete confirmation modal
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                deleteModal.show();
            });
        });
    </script>
</body>

</html>