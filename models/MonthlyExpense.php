<?php
require_once 'config/database.php';

class MonthlyExpense
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get monthly expenses grouped by category
     * @param int|null $walletId Optional wallet ID to filter expenses
     * @param int|null $userId User ID to filter by, defaults to logged in user
     * @return array
     */
    public function getByCategory($walletId = null, $userId = null)
    {
        // If no user ID is provided, get it from the session
        if ($userId === null && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }

        $query = "SELECT 
                    c.name as category, 
                    SUM(ABS(t.amount)) as amount 
                  FROM transactions t 
                  JOIN categories c ON t.id_category = c.id_category 
                  JOIN wallets w ON t.id_wallet = w.id_wallet
                  WHERE c.type = 'expense' 
                  AND w.id_user = :userId";
        // Query mengelompokkan pengeluaran berdasarkan kategori dan menghitung total jumlah untuk setiap kategori

        $params = [':userId' => $userId];

        // Add wallet filter if provided
        if ($walletId !== null) {
            $query .= " AND t.id_wallet = :walletId";
            $params[':walletId'] = $walletId;
        }

        $query .= " GROUP BY c.id_category ORDER BY amount DESC";
        // Mengelompokkan berdasarkan ID kategori dan mengurutkan berdasarkan jumlah secara menurun

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate percentages and assign colors programmatically
        $total = array_sum(array_column($result, 'amount'));
        $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#858796', '#6610f2', '#6f42c1', '#fd7e14'];

        foreach ($result as $index => &$item) {
            $item['percentage'] = $total > 0 ? ($item['amount'] / $total) * 100 : 0;
            // Assign a color from our predefined palette, cycling through if we have more categories than colors
            $item['color'] = $colors[$index % count($colors)];
        }

        return $result;
    }
}
?>