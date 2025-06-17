<?php
require_once 'core/functions.php';
$pageTitle = 'Categories Management';

// Ensure variables are always arrays to avoid warnings
$expenseCategories = $expenseCategories ?? [];
$incomeCategories = $incomeCategories ?? [];
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus-circle me-2"></i>Create Category
                </button>
            </div>

            <!-- Categories Dashboard -->
            <div class="row g-4">
                <!-- Income Categories -->
                <div class="col-md-6">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title text-success"><i class="bi bi-graph-up-arrow me-2"></i>Income
                                Categories</div>
                            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                data-bs-target="#addCategoryModal" data-type="income">
                                <i class="bi bi-plus-circle"></i> Add
                            </button>
                        </div>

                        <?php if (empty($incomeCategories)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i> No income categories found. Create your first
                                category to organize your income.
                            </div>
                        <?php else: ?>
                            <div class="category-list">
                                <?php foreach ($incomeCategories as $category): ?>
                                    <div class="category-item" data-id="<?= $category['id_category'] ?>" data-type="income">
                                        <div class="category-info">
                                            <div class="category-name"><?= htmlspecialchars($category['name']) ?></div>
                                        </div>
                                        <div class="category-actions">
                                            <button class="btn btn-sm btn-outline-light edit-category">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-category">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Expense Categories -->
                <div class="col-md-6">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <div class="section-title text-danger"><i class="bi bi-graph-down-arrow me-2"></i>Expense
                                Categories</div>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                data-bs-target="#addCategoryModal" data-type="expense">
                                <i class="bi bi-plus-circle"></i> Add
                            </button>
                        </div>

                        <?php if (empty($expenseCategories)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i> No expense categories found. Create your first
                                category to organize your expenses.
                            </div>
                        <?php else: ?>
                            <div class="category-list">
                                <?php foreach ($expenseCategories as $category): ?>
                                    <div class="category-item" data-id="<?= $category['id_category'] ?>" data-type="expense">
                                        <div class="category-info">
                                            <div class="category-name"><?= htmlspecialchars($category['name']) ?></div>
                                        </div>
                                        <div class="category-actions">
                                            <button class="btn btn-sm btn-outline-light edit-category">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-category">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tips & Guidelines -->
            <div class="dashboard-section mt-4">
                <div class="section-header">
                    <div class="section-title"><i class="bi bi-lightbulb me-2"></i>Tips for Categories</div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="tip-card">
                            <div class="tip-icon bg-primary">
                                <i class="bi bi-book"></i>
                            </div>
                            <div class="tip-title">Organize by Purpose</div>
                            <div class="tip-text">Create categories that reflect your financial goals and spending
                                habits for better insights.</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="tip-card">
                            <div class="tip-icon bg-success">
                                <i class="bi bi-bar-chart"></i>
                            </div>
                            <div class="tip-title">Not Too Many</div>
                            <div class="tip-text">Keep your categories simple. Too many categories can make tracking
                                difficult.</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="tip-card">
                            <div class="tip-icon bg-info">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <div class="tip-title">Review Regularly</div>
                            <div class="tip-text">Periodically review your categories to ensure they still align with
                                your financial situation.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Create New Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category Type</label>
                            <div class="d-flex">
                                <div class="form-check me-4">
                                    <input class="form-check-input" type="radio" name="type" id="type_expense"
                                        value="expense" checked>
                                    <label class="form-check-label" for="type_expense">
                                        Expense
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_income"
                                        value="income">
                                    <label class="form-check-label" for="type_income">
                                        Income
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" id="edit_category_id" name="id_category">

                        <div class="mb-3">
                            <label for="edit_category_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="edit_category_name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category Type</label>
                            <div class="form-control-plaintext" id="edit_category_type"></div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Components -->
    <?php include 'views/components/toast_notification.php'; ?>
    <?php include 'views/components/delete_category_modal.php'; ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle add category modal type selection
            const addCategoryModal = document.getElementById('addCategoryModal');
            const typeExpenseRadio = document.getElementById('type_expense');
            const typeIncomeRadio = document.getElementById('type_income');

            addCategoryModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const type = button.getAttribute('data-type');

                // Set the appropriate radio button based on which "Add" button was clicked
                if (type === 'income') {
                    typeIncomeRadio.checked = true;
                } else {
                    typeExpenseRadio.checked = true;
                }
            });

            // Add category form submission
            document.getElementById('addCategoryForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const categoryName = document.getElementById('category_name').value;
                const categoryType = document.querySelector('input[name="type"]:checked').value;

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';
                submitBtn.disabled = true;

                fetch('/api/categories/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: categoryName,
                        type: categoryType
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        // Restore button
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        if (data.success) {
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                            modal.hide();

                            // Reset form
                            document.getElementById('category_name').value = '';

                            // Show success message
                            showSuccessToast(data.message || 'Category created successfully');

                            // Reload page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showErrorToast(data.message || 'Failed to create category');
                        }
                    })
                    .catch(error => {
                        // Restore button
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        console.error('Error:', error);
                        showErrorToast('An error occurred during the operation');
                    });
            });

            // Handle edit category button clicks
            document.querySelectorAll('.edit-category').forEach(button => {
                button.addEventListener('click', function () {
                    const categoryItem = this.closest('.category-item');
                    const categoryId = categoryItem.getAttribute('data-id');
                    const categoryName = categoryItem.querySelector('.category-name').textContent;
                    const categoryType = categoryItem.getAttribute('data-type');

                    // Set edit form values
                    document.getElementById('edit_category_id').value = categoryId;
                    document.getElementById('edit_category_name').value = categoryName;
                    document.getElementById('edit_category_type').textContent = categoryType.charAt(0).toUpperCase() + categoryType.slice(1);

                    // Show the modal
                    const editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                    editModal.show();
                });
            });

            // Edit category form submission
            document.getElementById('editCategoryForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const categoryId = document.getElementById('edit_category_id').value;
                const categoryName = document.getElementById('edit_category_name').value;

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
                submitBtn.disabled = true;

                fetch('/api/categories/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_category: categoryId,
                        name: categoryName
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        // Restore button
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        if (data.success) {
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                            modal.hide();

                            // Show success message
                            showSuccessToast(data.message || 'Category updated successfully');

                            // Reload page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showErrorToast(data.message || 'Failed to update category');
                        }
                    })
                    .catch(error => {
                        // Restore button
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        console.error('Error:', error);
                        showErrorToast('An error occurred during the operation');
                    });
            });

            // Handle delete category button clicks
            document.querySelectorAll('.delete-category').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const categoryItem = this.closest('.category-item');
                    const categoryId = categoryItem.getAttribute('data-id');
                    const categoryName = categoryItem.querySelector('.category-name').textContent;

                    // Set modal values
                    document.getElementById('deleteCategoryId').value = categoryId;
                    document.getElementById('deleteCategoryName').textContent = categoryName;

                    // Show the delete confirmation modal
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
                    deleteModal.show();
                });
            });
        });
    </script>
</body>

</html>