<?php
require_once 'core/Middleware.php';
require_once 'models/BudgetModel.php';
require_once 'models/CategoriesModel.php';
require_once 'models/WalletsModel.php';

class BudgetController
{
    private $budgetModel;
    private $categoryModel;
    private $walletModel;

    public function __construct()
    {
        $this->budgetModel = new BudgetModel();
        $this->categoryModel = new CategoriesModel();
        $this->walletModel = new WalletsModel();
    }

    public function index()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $budgets = $this->budgetModel->getByUserId($userId);
        $budgetPerformance = $this->budgetModel->calculatePerformance($userId);
        $expenseCategories = $this->categoryModel->getByType('expense', $userId);
        $wallets = $this->walletModel->getAllByUserId($userId);

        require_once 'views/budgets/index.php';
    }

    public function store()
    {
        auth_required();

        // Check if the request has JSON content type
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
            // If not JSON, try to get data from POST
            $data = $_POST;
        }

        // Add debug logging
        error_log("Budget creation data received: " . json_encode($data));

        if (!$data) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            return;
        }

        if (
            empty($data['id_category']) || empty($data['amount']) ||
            empty($data['start_date']) || empty($data['end_date']) ||
            empty($data['id_wallet'])
        ) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        $data['id_user'] = $_SESSION['user_id'];

        // Convert numeric values to proper format
        $data['amount'] = floatval($data['amount']);
        $data['id_category'] = intval($data['id_category']);
        $data['id_wallet'] = intval($data['id_wallet']);

        // Add extra validation
        if ($data['amount'] <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Amount must be greater than zero']);
            return;
        }

        try {
            $success = $this->budgetModel->create($data);

            header('Content-Type: application/json');
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Budget created successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create budget'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error creating budget: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while creating the budget: ' . $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        auth_required();

        // Check if the request has JSON content type
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
            // If not JSON, try to get data from POST
            $data = $_POST;
        }

        // Debug logging
        error_log("Budget update data received: " . json_encode($data));

        if (!$data || empty($data['id_budget']) || !isset($data['amount'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input data. Missing required fields.']);
            return;
        }

        // Convert id_budget to integer
        $budgetId = intval($data['id_budget']);

        // Ensure amount is a valid number
        $amount = floatval($data['amount']);
        if ($amount <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Amount must be greater than zero.']);
            return;
        }

        // Create update data array
        $updateData = [
            'amount' => $amount
        ];

        // Attempt to update the budget
        $success = $this->budgetModel->update($budgetId, $updateData);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Budget updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update budget'
            ]);
        }
    }

    public function delete()
    {
        auth_required();

        // Check if the request has JSON content type
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
            // If not JSON, try to get data from POST
            $data = $_POST;
        }

        if (!$data || empty($data['id_budget'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            return;
        }

        $success = $this->budgetModel->delete($data['id_budget']);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Budget deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete budget'
            ]);
        }
    }
}
?>