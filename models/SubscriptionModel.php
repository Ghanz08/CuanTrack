<?php
require_once 'config/database.php';

class SubscriptionModel
{
    protected $db;

    public function __construct()
    {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Get all subscriptions by user ID
     * @param int $userId
     * @return array
     */
    public function getByUserId($userId)
    {
        $sql = "SELECT s.*, w.name as wallet_name 
                FROM subscriptions s
                LEFT JOIN wallets w ON s.id_wallet = w.id_wallet
                WHERE s.id_user = :id_user
                ORDER BY s.next_due_date ASC";
        // Query mengambil semua langganan untuk pengguna dengan nama dompet, diurutkan berdasarkan tanggal jatuh tempo

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_user', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get subscriptions by wallet ID
     * @param int $walletId
     * @return array
     */
    public function getByWalletId($walletId)
    {
        $sql = "SELECT * FROM subscriptions 
                WHERE id_wallet = :wallet_id
                ORDER BY next_due_date ASC";
        // Query mengambil semua langganan untuk dompet tertentu, diurutkan berdasarkan tanggal jatuh tempo

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':wallet_id', $walletId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active subscriptions that are due
     * @param string $date Optional date to check against (defaults to today)
     * @return array
     */
    public function getDueSubscriptions($date = null)
    {
        $date = $date ?? date('Y-m-d');

        $sql = "SELECT s.*, w.name as wallet_name, u.email
                FROM subscriptions s
                JOIN wallets w ON s.id_wallet = w.id_wallet
                JOIN users u ON s.id_user = u.id_user
                WHERE s.status = 'active' AND s.next_due_date <= :date";
        // Query retrieves active subscriptions that are due by comparing the next_due_date with the given date

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate monthly subscription costs for a wallet
     * @param int $walletId
     * @return float
     */
    public function calculateMonthlyExpenses($walletId)
    {
        $sql = "SELECT 
                SUM(CASE 
                    WHEN billing_cycle = 'monthly' THEN amount
                    WHEN billing_cycle = 'yearly' THEN amount / 12
                    WHEN billing_cycle = 'weekly' THEN amount * 4.33
                    WHEN billing_cycle = 'daily' THEN amount * 30
                    ELSE 0
                END) as monthly_cost
                FROM subscriptions
                WHERE id_wallet = :wallet_id AND status = 'active'";
        // Query calculates the total monthly cost of active subscriptions in a wallet, considering different billing cycles

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':wallet_id', $walletId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['monthly_cost'] ?? 0;
    }

    /**
     * Create a new subscription
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        $sql = "INSERT INTO subscriptions (id_user, id_wallet, name, amount, billing_cycle, next_due_date, status) 
                VALUES (:id_user, :id_wallet, :name, :amount, :billing_cycle, :next_due_date, :status)";
        // Query inserts a new subscription record into the database

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_user', $data['id_user']);
        $stmt->bindParam(':id_wallet', $data['id_wallet']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':billing_cycle', $data['billing_cycle']);
        $stmt->bindParam(':next_due_date', $data['next_due_date']);
        $stmt->bindParam(':status', $data['status']);
        return $stmt->execute();
    }

    /**
     * Get a subscription by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        $sql = "SELECT s.*, w.name as wallet_name 
                FROM subscriptions s
                LEFT JOIN wallets w ON s.id_wallet = w.id_wallet
                WHERE s.id_subscription = :id";
        // Query retrieves a subscription by its ID, including the wallet name

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update a subscription
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $sql = "UPDATE subscriptions SET 
                id_wallet = :id_wallet,
                name = :name,
                amount = :amount,
                billing_cycle = :billing_cycle,
                next_due_date = :next_due_date,
                status = :status
                WHERE id_subscription = :id";
        // Query updates an existing subscription's details

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_wallet', $data['id_wallet']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':billing_cycle', $data['billing_cycle']);
        $stmt->bindParam(':next_due_date', $data['next_due_date']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Delete a subscription
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $sql = "DELETE FROM subscriptions WHERE id_subscription = :id";
        // Query deletes a subscription from the database

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>