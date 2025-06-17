<?php
require_once 'config/database.php';

class MonthlyStatistic
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Fetch monthly income total for a specific user and optionally for a specific wallet
     * @param int|null $walletId Optional wallet ID to filter by
     * @param int|null $userId User ID to filter by, defaults to logged in user
     * @return float
     */
    public function getIncomeTotal($walletId = null, $userId = null)
    {
        // If no user ID is provided, get it from the session
        if ($userId === null && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }

        $query = "SELECT SUM(t.amount) as total 
                 FROM transactions t 
                 JOIN categories c ON t.id_category = c.id_category 
                 JOIN wallets w ON t.id_wallet = w.id_wallet 
                 WHERE c.type = 'income' AND w.id_user = :userId";
        // Query menghitung total pemasukan dengan menjumlahkan transaksi berkategori tipe pemasukan

        $params = [':userId' => $userId];

        // Add wallet filter if provided
        if ($walletId !== null) {
            $query .= " AND t.id_wallet = :walletId";
            $params[':walletId'] = $walletId;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    /**
     * Fetch monthly expense total for a specific user and optionally for a specific wallet
     * @param int|null $walletId Optional wallet ID to filter by
     * @param int|null $userId User ID to filter by, defaults to logged in user
     * @return float
     */
    public function getExpenseTotal($walletId = null, $userId = null)
    {
        // If no user ID is provided, get it from the session
        if ($userId === null && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }

        $query = "SELECT SUM(ABS(t.amount)) as total 
                 FROM transactions t 
                 JOIN categories c ON t.id_category = c.id_category 
                 JOIN wallets w ON t.id_wallet = w.id_wallet 
                 WHERE c.type = 'expense' AND w.id_user = :userId";
        // Query menghitung total pengeluaran dengan menjumlahkan transaksi berkategori tipe pengeluaran

        $params = [':userId' => $userId];

        // Add wallet filter if provided
        if ($walletId !== null) {
            $query .= " AND t.id_wallet = :walletId";
            $params[':walletId'] = $walletId;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }
}
?>