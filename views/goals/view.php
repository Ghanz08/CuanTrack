<?php
require_once 'core/functions.php';
$pageTitle = 'Goal Details';

// Ensure variables are always arrays to avoid warnings
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
                <h1><?= htmlspecialchars($goal['title'] ?? 'Goal Details') ?></h1>
                <div>
                    <a href="/goals" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Goals
                    </a>
                </div>
            </div>

            <!-- Goal Details -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title"><i class="bi bi-flag me-2"></i>Goal Details</div>
                            <div>
                                <a href="/goals/edit/<?= $goal['id_goal'] ?>" class="btn btn-sm btn-outline-light me-2">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteGoalModal">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>

                        <div class="goal-details-card">
                            <div class="goal-status-banner bg-<?= $goal['status_class'] ?? 'primary' ?>">
                                <span class="status-text"><?= ucfirst($goal['status'] ?? 'Active') ?></span>
                            </div>

                            <div class="goal-progress mt-4">
                                <div class="progress-label">Progress</div>
                                <div class="progress-details d-flex justify-content-between mb-1">
                                    <span class="current"><?= format_rupiah($goal['current_amount'] ?? 0) ?></span>
                                    <span class="target text-muted">of
                                        <?= format_rupiah($goal['target_amount'] ?? 0) ?></span>
                                    <span class="percentage"><?= $goal['percentage'] ?? 0 ?>%</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-<?= $goal['status_class'] ?? 'primary' ?>"
                                        role="progressbar" style="width: <?= $goal['percentage'] ?? 0 ?>%"
                                        aria-valuenow="<?= $goal['percentage'] ?? 0 ?>" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>

                            <div class="goal-stats row mt-4">
                                <div class="col-md-4">
                                    <div class="stat-card">
                                        <div class="stat-title">Target Date</div>
                                        <div class="stat-value">
                                            <i class="bi bi-calendar me-1"></i>
                                            <?= date('d M Y', strtotime($goal['target_date'] ?? 'now')) ?>
                                        </div>
                                        <?php if (($goal['days_left'] ?? 0) > 0 && ($goal['percentage'] ?? 0) < 100): ?>
                                            <div class="stat-subtitle"><?= $goal['days_left'] ?? 0 ?> days remaining</div>
                                        <?php elseif (($goal['percentage'] ?? 0) >= 100): ?>
                                            <div class="stat-subtitle text-success">Goal completed!</div>
                                        <?php else: ?>
                                            <div class="stat-subtitle text-danger">Deadline passed</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-card">
                                        <div class="stat-title">Amount Saved</div>
                                        <div class="stat-value">
                                            <?= format_rupiah($goal['current_amount'] ?? 0) ?>
                                        </div>
                                        <div class="stat-subtitle">
                                            <?= format_rupiah(($goal['target_amount'] ?? 0) - ($goal['current_amount'] ?? 0)) ?>
                                            left to reach goal
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-card">
                                        <div class="stat-title">Daily Savings Needed</div>
                                        <?php
                                        $amountLeft = ($goal['target_amount'] ?? 0) - ($goal['current_amount'] ?? 0);
                                        $daysLeft = max(1, $goal['days_left'] ?? 1);
                                        $dailyAmount = $amountLeft > 0 ? $amountLeft / $daysLeft : 0;
                                        ?>
                                        <div class="stat-value">
                                            <?= format_rupiah($dailyAmount) ?>
                                        </div>
                                        <div class="stat-subtitle">per day to reach goal on time</div>
                                    </div>
                                </div>
                            </div>

                            <div class="goal-actions mt-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addProgressModal">
                                    <i class="bi bi-plus-circle me-1"></i> Add Progress
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title"><i class="bi bi-lightbulb me-2"></i>Tips to Reach Your Goal</div>
                        </div>

                        <div class="goal-tips">
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="bi bi-calculator"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Break it down</div>
                                    <div class="tip-text">Save <?= format_rupiah($dailyAmount) ?> per day or
                                        <?= format_rupiah($dailyAmount * 7) ?> per week to reach your goal on time.
                                    </div>
                                </div>
                            </div>

                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="bi bi-piggy-bank"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Automate your savings</div>
                                    <div class="tip-text">Set up automatic transfers to make saving effortless and
                                        consistent.</div>
                                </div>
                            </div>

                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Track your progress</div>
                                    <div class="tip-text">Regularly update your progress to stay motivated and on track.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Progress Modal -->
    <div class="modal fade" id="addProgressModal" tabindex="-1" aria-labelledby="addProgressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProgressModalLabel">Add Progress to Goal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProgressForm">
                        <input type="hidden" id="goal_id" name="id_goal" value="<?= $goal['id_goal'] ?? 0 ?>">

                        <div class="mb-3">
                            <label for="progress_amount" class="form-label">Amount to Add</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="progress_amount" name="amount"
                                    placeholder="0" required>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Add Progress</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Goal Modal -->
    <div class="modal fade" id="deleteGoalModal" tabindex="-1" aria-labelledby="deleteGoalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteGoalModalLabel">Delete Goal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this goal? This action cannot be undone.</p>
                    <p class="fw-bold"><?= htmlspecialchars($goal['title'] ?? '') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteGoalForm" action="/goals/delete" method="POST">
                        <input type="hidden" name="id_goal" value="<?= $goal['id_goal'] ?? 0 ?>">
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
            // Handle add progress form submission
            document.getElementById('addProgressForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const goalId = document.getElementById('goal_id').value;
                const amount = document.getElementById('progress_amount').value;

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
                submitBtn.disabled = true;

                fetch('/api/goals/update-progress', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_goal: goalId,
                        amount: amount
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        // Restore button
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        if (data.success) {
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('addProgressModal'));
                            modal.hide();

                            // Show success message
                            if (window.showToast) {
                                showToast(data.message, true);
                            } else {
                                alert(data.message);
                            }

                            // Reload page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            if (window.showToast) {
                                showToast(data.message, false);
                            } else {
                                alert(data.message);
                            }
                        }
                    })
                    .catch(error => {
                        // Restore button
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        console.error('Error updating progress:', error);
                        if (window.showToast) {
                            showToast('Error updating progress', false);
                        } else {
                            alert('Error updating progress');
                        }
                    });
            });

            // Handle delete goal form submission
            document.getElementById('deleteGoalForm').addEventListener('submit', function (e) {
                e.preventDefault();

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
                submitBtn.disabled = true;

                const formData = new FormData(this);

                fetch('/goals/delete', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            if (window.showToast) {
                                showToast(data.message, true);
                            } else {
                                alert(data.message);
                            }

                            // Redirect after a short delay
                            setTimeout(() => {
                                window.location.href = '/goals';
                            }, 1500);
                        } else {
                            // Restore button
                            submitBtn.innerHTML = originalBtnText;
                            submitBtn.disabled = false;

                            if (window.showToast) {
                                showToast(data.message, false);
                            } else {
                                alert(data.message);
                            }
                        }
                    })
                    .catch(error => {
                        // Restore button
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        console.error('Error deleting goal:', error);
                        if (window.showToast) {
                            showToast('Error deleting goal', false);
                        } else {
                            alert('Error deleting goal');
                        }
                    });
            });
        });
    </script>
</body>

</html>