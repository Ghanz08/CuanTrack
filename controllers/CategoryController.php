<?php
require_once 'core/Middleware.php';
require_once 'models/CategoriesModel.php';

class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoriesModel();
    }

    public function index()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $expenseCategories = $this->categoryModel->getByType('expense', $userId);
        $incomeCategories = $this->categoryModel->getByType('income', $userId);

        require_once 'views/categories/index.php';
    }

    public function store()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        // Check if the request has JSON content type
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
            // If not JSON, try to get data from POST
            $data = $_POST;
        }

        if (!$data || empty($data['name']) || empty($data['type'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        $name = trim($data['name']);
        $type = $data['type'];

        if (!in_array($type, ['income', 'expense'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid category type']);
            return;
        }

        $success = $this->categoryModel->create([
            'name' => $name,
            'type' => $type,
            'id_user' => $userId
        ]);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Category created successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create category'
            ]);
        }
    }

    public function update()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        // Check if the request has JSON content type
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
            // If not JSON, try to get data from POST
            $data = $_POST;
        }

        if (!$data || empty($data['id_category']) || empty($data['name'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        $categoryId = $data['id_category'];
        $name = trim($data['name']);

        // Verify ownership
        $category = $this->categoryModel->getById($categoryId);
        if (!$category || $category['id_user'] != $userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Category not found or unauthorized']);
            return;
        }

        $success = $this->categoryModel->update($categoryId, [
            'name' => $name
        ]);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Category updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update category'
            ]);
        }
    }

    public function delete()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        // Check if the request has JSON content type
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
            // If not JSON, try to get data from POST
            $data = $_POST;
        }

        // Debug logging
        error_log("Attempting to delete category with data: " . json_encode($data));

        if (!$data || empty($data['id_category'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing category ID']);
            return;
        }

        $categoryId = intval($data['id_category']);

        // Verify ownership
        $category = $this->categoryModel->getById($categoryId);
        if (!$category || $category['id_user'] != $userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Category not found or unauthorized']);
            return;
        }

        // Check if category is in use
        if ($this->categoryModel->isInUse($categoryId)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Cannot delete category that is in use by transactions'
            ]);
            return;
        }

        try {
            $success = $this->categoryModel->delete($categoryId);

            header('Content-Type: application/json');
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Category deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to delete category'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error deleting category: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error deleting category: ' . $e->getMessage()
            ]);
        }
    }
}
?>