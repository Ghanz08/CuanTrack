<?php
require_once 'core/Middleware.php';
require_once 'models/WalletsModel.php';
require_once 'models/TransactionsModel.php';
require_once 'models/SubscriptionModel.php';
require_once 'models/MonthlyExpense.php';
require_once 'models/MonthlyStatistic.php';
require_once 'models/CategoriesModel.php';
require_once 'models/BudgetModel.php';
require_once 'models/GoalsModel.php';

class WalletController
{
    private $walletModel;
    private $transactionsModel;
    private $subscriptionModel;
    private $monthlyExpenseModel;
    private $monthlyStatisticModel;
    private $categoriesModel;
    private $budgetModel;

    public function __construct()
    {
        // Catatan: Kelas ini menggunakan WalletsModel.php
        // Ada juga file WalletModel.php dengan fungsi serupa
        // Sebaiknya konsolidasikan kedua model ini untuk menghindari redundansi
        $this->walletModel = new WalletsModel();
        $this->transactionsModel = new TransactionsModel();
        $this->subscriptionModel = new SubscriptionModel();
        $this->monthlyExpenseModel = new MonthlyExpense();
        $this->monthlyStatisticModel = new MonthlyStatistic();
        $this->categoriesModel = new CategoriesModel();
        $this->budgetModel = new BudgetModel();
    }

    public function index()
    {
        auth_required();
        $userId = $_SESSION['user_id'] ?? 1;
        $wallets = $this->walletModel->getAllByUserId($userId);
        require 'views/wallets/index.php';
    }

    public function create()
    {
        auth_required();
        require 'views/wallets/create.php';
    }

    public function store()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $name = trim($_POST['name'] ?? '');
        $initialBalance = floatval($_POST['balance'] ?? 0);
        $currency = trim($_POST['currency'] ?? 'IDR');

        if (empty($name)) {
            header('Location: /wallets/create?error=name_required');
            exit;
        }

        $data = [
            'id_user' => $userId,
            'name' => $name,
            'balance' => $initialBalance,
            'currency' => $currency
        ];

        if ($this->walletModel->create($data)) {
            header('Location: /wallets?success=wallet_created');
        } else {
            header('Location: /wallets/create?error=creation_failed');
        }
        exit;
    }

    public function update()
    {
        auth_required();
        echo "Wallet updated via POST.";
    }

    public function updatePut()
    {
        auth_required();
        echo "Wallet updated via PUT.";
    }

    public function delete()
    {
        auth_required();
        echo "Wallet deleted.";
    }

    public function view($id)
    {
        auth_required();
        $userId = $_SESSION['user_id'] ?? null;

        $wallet = $this->walletModel->getById($id);

        if (!$wallet) {
            header("Location: /wallets");
            exit;
        }

        $transactions = $this->transactionsModel->getAllByWalletId($id);
        $incomeTransactions = $this->transactionsModel->getIncomeByWalletId($id);
        $expenseTransactions = $this->transactionsModel->getExpensesByWalletId($id);
        $walletSubscriptions = $this->subscriptionModel->getByWalletId($id);
        $summary = $this->transactionsModel->getSummaryByWalletId($id);

        $totalIncome = $wallet['total_income'] ?? 0;
        $totalExpenses = $wallet['total_expenses'] ?? 0;

        $monthlySubscriptionExpenses = $this->subscriptionModel->calculateMonthlyExpenses($id);
        $monthlyExpenses = $this->monthlyExpenseModel->getByCategory($id, $userId);

        $monthlyStats = [
            'income' => $this->monthlyStatisticModel->getIncomeTotal($id, $userId),
            'expenses' => $this->monthlyStatisticModel->getExpenseTotal($id, $userId),
            'savings' => $this->monthlyStatisticModel->getIncomeTotal($id, $userId) - $this->monthlyStatisticModel->getExpenseTotal($id, $userId),
        ];

        $expenseCategories = $this->categoriesModel->getByType('expense', $userId);
        $incomeCategories = $this->categoriesModel->getByType('income', $userId);
        $walletBudgets = $this->budgetModel->getByWalletId($id);

        $goalModel = new GoalsModel();
        $recentGoals = $goalModel->getRecent($userId, 3);

        require_once 'views/wallets/view.php';
    }
}
?>