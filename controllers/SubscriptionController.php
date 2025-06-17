<?php
require_once 'core/Middleware.php';
require_once 'models/SubscriptionModel.php';
require_once 'models/WalletsModel.php';

class SubscriptionController
{
    private $subscriptionModel;
    private $walletModel;

    public function __construct()
    {
        $this->subscriptionModel = new SubscriptionModel();
        $this->walletModel = new WalletsModel();
    }

    public function index()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $subscriptions = $this->subscriptionModel->getByUserId($userId);

        $totalMonthlyCost = 0;
        foreach ($subscriptions as $subscription) {
            $amount = $subscription['amount'];
            $billing_cycle = $subscription['billing_cycle'];

            switch ($billing_cycle) {
                case 'weekly':
                    $totalMonthlyCost += $amount * 4.33;
                    break;
                case 'monthly':
                    $totalMonthlyCost += $amount;
                    break;
                case 'yearly':
                    $totalMonthlyCost += $amount / 12;
                    break;
                case 'daily':
                    $totalMonthlyCost += $amount * 30;
                    break;
                default:
                    $totalMonthlyCost += $amount;
            }
        }

        $wallets = $this->walletModel->getAllByUserId($userId);

        require_once 'views/subscriptions/index.php';
    }

    public function create()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $wallets = $this->walletModel->getAllByUserId($userId);

        $selectedWalletId = isset($_GET['wallet_id']) ? (int) $_GET['wallet_id'] : null;

        require_once 'views/subscriptions/create.php';
    }

    public function store()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $walletId = isset($_POST['wallet_id']) ? (int) $_POST['wallet_id'] : null;
        $name = trim($_POST['name'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $billingCycle = $_POST['billing_cycle'] ?? 'monthly';
        $nextDueDate = $_POST['next_due_date'] ?? date('Y-m-d');
        $status = $_POST['status'] ?? 'active';

        if (empty($name) || $amount <= 0 || empty($walletId)) {
            header('Location: /subscription/create?error=invalid_input');
            exit;
        }

        $data = [
            'id_user' => $userId,
            'id_wallet' => $walletId,
            'name' => $name,
            'amount' => $amount,
            'billing_cycle' => $billingCycle,
            'next_due_date' => $nextDueDate,
            'status' => $status
        ];

        if ($this->subscriptionModel->create($data)) {
            header('Location: /subscription?success=subscription_created');
        } else {
            header('Location: /subscription/create?error=create_failed');
        }
        exit;
    }

    public function edit($id)
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $subscription = $this->subscriptionModel->getById($id);

        if (!$subscription || $subscription['id_user'] != $userId) {
            header('Location: /subscription?error=subscription_not_found');
            exit;
        }

        $wallets = $this->walletModel->getAllByUserId($userId);

        require_once 'views/subscriptions/edit.php';
    }

    public function update()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $subscriptionId = intval($_POST['id_subscription'] ?? 0);

        $walletId = isset($_POST['wallet_id']) ? (int) $_POST['wallet_id'] : null;
        $name = trim($_POST['name'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $billingCycle = $_POST['billing_cycle'] ?? 'monthly';
        $nextDueDate = $_POST['next_due_date'] ?? date('Y-m-d');
        $status = $_POST['status'] ?? 'active';

        if (empty($name) || $amount <= 0 || empty($walletId)) {
            header('Location: /subscription/edit/' . $subscriptionId . '?error=invalid_input');
            exit;
        }

        $subscription = $this->subscriptionModel->getById($subscriptionId);
        if (!$subscription || $subscription['id_user'] != $userId) {
            header('Location: /subscription?error=unauthorized');
            exit;
        }

        $data = [
            'id_wallet' => $walletId,
            'name' => $name,
            'amount' => $amount,
            'billing_cycle' => $billingCycle,
            'next_due_date' => $nextDueDate,
            'status' => $status
        ];

        if ($this->subscriptionModel->update($subscriptionId, $data)) {
            header('Location: /subscription?success=subscription_updated');
        } else {
            header('Location: /subscription/edit/' . $subscriptionId . '?error=update_failed');
        }
        exit;
    }

    public function delete()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        $subscriptionId = intval($_POST['id_subscription'] ?? 0);

        $subscription = $this->subscriptionModel->getById($subscriptionId);
        if (!$subscription || $subscription['id_user'] != $userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Subscription not found or unauthorized']);
            exit;
        }

        $success = $this->subscriptionModel->delete($subscriptionId);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Subscription deleted successfully',
                'redirect' => '/subscription'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete subscription']);
        }
    }
}
?>
