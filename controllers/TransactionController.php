<?php
require_once 'core/Middleware.php';
require_once 'models/TransactionsModel.php';
require_once 'models/WalletsModel.php';
require_once 'models/CategoriesModel.php';

class TransactionController
{
    private $transactionModel;
    private $walletModel;
    private $categoryModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionsModel();
        $this->walletModel = new WalletsModel();
        $this->categoryModel = new CategoriesModel();
    }

    public function index()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $itemsPerPage = 10;
        $offset = ($page - 1) * $itemsPerPage;

        $result = $this->getFilteredTransactions($userId, $itemsPerPage, $offset);
        $transactions = $result['transactions'];
        $totalTransactions = $result['total'];
        $totalPages = ceil($totalTransactions / $itemsPerPage);

        $wallets = $this->walletModel->getAllByUserId($userId);

        $expenseCategories = $this->categoryModel->getByType('expense', $userId);
        $incomeCategories = $this->categoryModel->getByType('income', $userId);
        $categories = array_merge($expenseCategories, $incomeCategories);

        require 'views/transactions/index.php';
    }

    private function getFilteredTransactions($userId, $limit = null, $offset = null)
    {
        $filters = [
            'user_id' => $userId
        ];

        if (!empty($_GET['wallet_id'])) {
            $filters['wallet_id'] = (int) $_GET['wallet_id'];
        }

        if (!empty($_GET['category_id'])) {
            $filters['category_id'] = (int) $_GET['category_id'];
        }

        if (!empty($_GET['type'])) {
            $filters['type'] = $_GET['type'];
        }

        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }

        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }

        $total = $this->transactionModel->countByFilters($filters);
        $transactions = $this->transactionModel->getByFilters($filters, $limit, $offset);

        return [
            'transactions' => $transactions,
            'total' => $total
        ];
    }

    public function create()
    {
        header("Location: /wallets?message=select_wallet_for_transaction");
        exit;
    }

    public function store()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $walletId = isset($_POST['wallet_id']) ? (int) $_POST['wallet_id'] : null;
        $categoryId = isset($_POST['category_id']) ? (int) $_POST['category_id'] : null;
        $amount = isset($_POST['amount']) ? (float) $_POST['amount'] : 0;
        $type = isset($_POST['type']) ? $_POST['type'] : 'expense';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $transactionDate = isset($_POST['transaction_date']) ? $_POST['transaction_date'] : date('Y-m-d');

        if (!$walletId) {
            header("Location: /wallets?error=no_wallet_selected");
            exit;
        }

        if (!$categoryId || $amount <= 0) {
            header("Location: /wallets/view/{$walletId}?error=invalid_input");
            exit;
        }

        $data = [
            'id_user' => $userId,
            'id_wallet' => $walletId,
            'id_category' => $categoryId,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'transaction_date' => $transactionDate
        ];

        if ($this->transactionModel->create($data)) {
            header("Location: /wallets/view/{$walletId}?success=transaction_added");
        } else {
            header("Location: /wallets/view/{$walletId}?error=save_failed");
        }
        exit;
    }

    public function getCategoriesByType()
    {
        auth_required();
        $userId = $_SESSION['user_id'];
        $type = isset($_GET['type']) ? $_GET['type'] : 'expense';

        $categories = $this->categoryModel->getByType($type, $userId);

        header('Content-Type: application/json');
        echo json_encode($categories);
    }

    public function addCategory()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name']) || !isset($data['type'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $newCategory = $this->categoryModel->add($data['name'], $data['type'], $userId);

        if ($newCategory) {
            echo json_encode([
                'success' => true,
                'category' => $newCategory
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add category']);
        }
    }

    public function getTransaction()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Transaction ID is required']);
            return;
        }

        if (!$this->transactionModel->verifyOwnership($id, $userId)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }

        $transaction = $this->transactionModel->getById($id);

        header('Content-Type: application/json');
        if ($transaction) {
            echo json_encode(['success' => true, 'transaction' => $transaction]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Transaction not found']);
        }
    }

    public function update()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $transactionId = isset($_POST['id_transaction']) ? (int) $_POST['id_transaction'] : null;
        $walletId = isset($_POST['wallet_id']) ? (int) $_POST['wallet_id'] : null;
        $categoryId = isset($_POST['category_id']) ? (int) $_POST['category_id'] : null;
        $amount = isset($_POST['amount']) ? (float) $_POST['amount'] : 0;
        $type = isset($_POST['type']) ? $_POST['type'] : 'expense';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $transactionDate = isset($_POST['transaction_date']) ? $_POST['transaction_date'] : date('Y-m-d');

        if (!$transactionId || !$walletId || !$categoryId || $amount <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            return;
        }

        if (!$this->transactionModel->verifyOwnership($transactionId, $userId)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }

        $data = [
            'id_transaction' => $transactionId,
            'id_wallet' => $walletId,
            'id_category' => $categoryId,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'transaction_date' => $transactionDate
        ];

        $success = $this->transactionModel->update($data);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Transaction updated successfully',
                'redirect' => '/transactions'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update transaction']);
        }
    }

    public function delete()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $transactionId = $_POST['id_transaction'] ?? null;

        if (!$transactionId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Transaction ID is required']);
            return;
        }

        if (!$this->transactionModel->verifyOwnership($transactionId, $userId)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }

        $success = $this->transactionModel->delete($transactionId);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Transaction deleted successfully',
                'redirect' => '/transactions'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete transaction']);
        }
    }
}
?>
