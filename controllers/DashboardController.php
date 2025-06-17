<?php
require_once 'models/WalletsModel.php';
require_once 'models/TransactionsModel.php';
require_once 'models/GoalsModel.php';
require_once 'models/SubscriptionModel.php';
require_once 'models/MonthlyExpense.php';
require_once 'models/MonthlyStatistic.php';

class DashboardController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];

        $walletModel = new WalletsModel();
        $transactionModel = new TransactionsModel();
        $goalModel = new GoalsModel();
        $subscriptionModel = new SubscriptionModel();
        $monthlyExpenseModel = new MonthlyExpense();
        $monthlyStatisticModel = new MonthlyStatistic();

        $wallets = $walletModel->getAllByUserId($userId);
        $transactions = $transactionModel->getRecentByUserId($userId, 5);
        $goals = $goalModel->getRecent($userId, 3);
        $subscriptions = $subscriptionModel->getByUserId($userId);
        $monthlyExpenses = $monthlyExpenseModel->getByCategory(null, $userId);

        $monthlyStats = [
            'income' => $monthlyStatisticModel->getIncomeTotal(null, $userId),
            'expenses' => $monthlyStatisticModel->getExpenseTotal(null, $userId),
            'savings' => $monthlyStatisticModel->getIncomeTotal(null, $userId) - $monthlyStatisticModel->getExpenseTotal(null, $userId),
        ];

        include 'views/dashboard.php';
    }
}
?>
