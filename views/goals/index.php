<?php
require_once 'core/functions.php';
$pageTitle = 'Savings Goals';

// Ensure variables are always arrays to avoid warnings
$goals = $goals ?? [];
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
                    <a href="/goals/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Create New Goal
                    </a>
                </div>
            </div>

            <!-- Goals Summary -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="dashboard-section h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-piggy-bank fs-1 text-primary"></i>
                            </div>
                            <div>
                                <div class="section-title mb-1 text-white">Total Saved</div>
                                <div class="summary-value fs-4 fw-bold text-white">
                                    <?= format_rupiah($totalSaved) ?>
                                </div>
                                <div class="summary-subtitle small text-white-50">
                                    of <?= format_rupiah($totalTarget) ?> target
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-section h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-trophy fs-1 text-success"></i>
                            </div>
                            <div>
                                <div class="section-title mb-1 text-white">Completed Goals</div>
                                <div class="summary-value fs-4 fw-bold text-success">
                                    <?= $completedGoals ?> / <?= $totalGoals ?>
                                </div>
                                <div class="summary-subtitle small text-white-50">
                                    <?= $completionRate ?>% completion rate
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-section h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-bar-chart-line fs-1 text-info"></i>
                            </div>
                            <div>
                                <div class="section-title mb-1 text-white">Overall Progress</div>
                                <div class="progress mb-2" style="height: 8px; width: 100%;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?= $overallProgress ?>%" aria-valuenow="<?= $overallProgress ?>"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="summary-subtitle small text-white-50">
                                    <?= $overallProgress ?>% to all goals
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Goals List Section -->
            <div class="dashboard-section">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-flag me-2"></i>Your Savings Goals</div>
                </div>

                <?php if (empty($goals)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> You haven't set any savings goals yet.
                        <a href="/goals/create" class="alert-link">Create your first goal</a> to start tracking your
                        progress.
                    </div>
                <?php else: ?>
                    <div class="goals-grid">
                        <?php foreach ($goals as $goal): ?>
                            <div class="goal-card-wrapper">
                                <div class="goal-card" data-id="<?= $goal['id_goal'] ?>">
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

                                    <div class="goal-meta mt-3 d-flex justify-content-between">
                                        <div class="goal-deadline">
                                            <i class="bi bi-calendar me-1"></i>
                                            <?= date('d M Y', strtotime($goal['target_date'])) ?>
                                            <?php if ($goal['days_left'] > 0 && $goal['percentage'] < 100): ?>
                                                <small class="text-muted">(<?= $goal['days_left'] ?> days left)</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="goal-actions mt-3">
                                        <a href="/goals/view/<?= $goal['id_goal'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Details
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-success add-progress-btn"
                                            data-id="<?= $goal['id_goal'] ?>" data-bs-toggle="modal"
                                            data-bs-target="#addProgressModal">
                                            <i class="bi bi-plus-circle"></i> Add Progress
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tips Section -->
            <div class="dashboard-section mt-4">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-lightbulb me-2"></i>Savings Tips</div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="tip-card">
                            <div class="tip-icon bg-primary">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="tip-title">Set up automatic transfers</div>
                            <div class="tip-text">Automate your savings by scheduling regular transfers to your savings
                                account.</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="tip-card">
                            <div class="tip-icon bg-success">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="tip-title">Follow the 50/30/20 rule</div>
                            <div class="tip-text">Allocate 50% of income to needs, 30% to wants, and 20% to savings.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="tip-card">
                            <div class="tip-icon bg-info">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <div class="tip-title">Track your progress</div>
                            <div class="tip-text">Regularly monitor your savings goals to stay motivated and adjust as
                                needed.</div>
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
                        <input type="hidden" id="goal_id" name="id_goal">

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

    <!-- Include Components -->
    <?php include 'views/components/toast_notification.php'; ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle add progress button click
            document.querySelectorAll('.add-progress-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const goalId = this.getAttribute('data-id');
                    document.getElementById('goal_id').value = goalId;
                });
            });

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
        });
    </script>
</body>

</html>