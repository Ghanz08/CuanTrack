<?php
require_once 'config/database.php';

class BudgetModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Get all budgets for a specific user
     * @param int $userId
     * @return array
     */
    public function getByUserId($userId)
    {
        $query = "SELECT b.*, c.name as category_name, w.name as wallet_name
                 FROM budgets b
                 LEFT JOIN categories c ON b.id_category = c.id_category
                 LEFT JOIN wallets w ON b.id_wallet = w.id_wallet
                 WHERE b.id_user = :userId
                 ORDER BY b.start_date DESC";
        // Query mengambil semua anggaran untuk pengguna dengan nama kategori dan dompet, diurutkan berdasarkan tanggal mulai

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all budgets for a specific wallet
     * @param int $walletId
     * @return array
     */
    public function getByWalletId($walletId)
    {
        $query = "SELECT b.*, c.name as category_name,
                    (SELECT COALESCE(SUM(t.amount), 0)
                     FROM transactions t
                     WHERE t.id_wallet = b.id_wallet
                       AND t.id_category = b.id_category
                       AND t.type = 'expense'
                       AND t.transaction_date BETWEEN b.start_date AND b.end_date) as spent_amount
                 FROM budgets b
                 LEFT JOIN categories c ON b.id_category = c.id_category
                 WHERE b.id_wallet = :walletId
                 ORDER BY b.start_date DESC";
        // Query mengambil anggaran untuk dompet dan menghitung jumlah yang sudah dibelanjakan menggunakan subquery
        // Subquery menjumlahkan pengeluaran untuk kategori anggaran dalam rentang tanggal anggaran

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':walletId', $walletId, PDO::PARAM_INT);
        $stmt->execute();

        $budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate percentage and status for each budget
        foreach ($budgets as &$budget) {
            $budget['spent_amount'] = floatval($budget['spent_amount']);
            $budget['percentage'] = $budget['amount'] > 0
                ? min(100, round(($budget['spent_amount'] / $budget['amount']) * 100))
                : 0;

            // Determine status based on percentage
            if ($budget['percentage'] < 70) {
                $budget['status'] = 'success';
            } elseif ($budget['percentage'] < 90) {
                $budget['status'] = 'warning';
            } else {
                $budget['status'] = 'danger';
            }
        }

        return $budgets;
    }

    /**
     * Calculate budget performance for visualization
     * @param int $userId
     * @return array
     */
    public function calculatePerformance($userId)
    {
        $query = "SELECT 
                    b.id_budget,
                    b.amount as budget_amount,
                    c.name as category,
                    b.start_date,
                    b.end_date,
                    w.name as wallet_name,
                    COALESCE((
                        SELECT SUM(t.amount)
                        FROM transactions t
                        WHERE t.id_category = b.id_category
                          AND t.id_wallet = b.id_wallet
                          AND t.type = 'expense'
                          AND t.transaction_date BETWEEN b.start_date AND b.end_date
                    ), 0) as actual_amount
                FROM budgets b
                LEFT JOIN categories c ON b.id_category = c.id_category
                LEFT JOIN wallets w ON b.id_wallet = w.id_wallet
                WHERE b.id_user = :userId
                ORDER BY c.name";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $budgetPerformance = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate percentages and status for each budget
        foreach ($budgetPerformance as &$budget) {
            $budget['actual_amount'] = floatval($budget['actual_amount']);
            $budget['percentage'] = $budget['budget_amount'] > 0
                ? min(100, round(($budget['actual_amount'] / $budget['budget_amount']) * 100))
                : 0;

            // Determine status based on percentage
            if ($budget['percentage'] < 70) {
                $budget['status'] = 'success';
            } elseif ($budget['percentage'] < 90) {
                $budget['status'] = 'warning';
            } else {
                $budget['status'] = 'danger';
            }
        }

        return $budgetPerformance;
    }

    /**
     * Create a new budget
     * @param array $data Budget data
     * @return bool Success flag
     */
    public function create($data)
    {
        try {
            // Log creation attempt
            error_log("Creating budget with data: " . json_encode($data));

            $query = "INSERT INTO budgets (id_user, id_wallet, id_category, amount, start_date, end_date) 
                    VALUES (:userId, :walletId, :categoryId, :amount, :startDate, :endDate)";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':userId', $data['id_user'], PDO::PARAM_INT);
            $stmt->bindParam(':walletId', $data['id_wallet'], PDO::PARAM_INT);
            $stmt->bindParam(':categoryId', $data['id_category'], PDO::PARAM_INT);
            $stmt->bindParam(':amount', $data['amount']);
            $stmt->bindParam(':startDate', $data['start_date']);
            $stmt->bindParam(':endDate', $data['end_date']);

            $result = $stmt->execute();

            // Log result
            error_log("Budget creation result: " . ($result ? "success" : "failed"));

            return $result;
        } catch (PDOException $e) {
            // Log error
            error_log("Error creating budget: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing budget
     * @param int $id Budget ID
     * @param array $data Budget data
     * @return bool Success flag
     */
    public function update($id, $data)
    {
        try {
            // Log update attempt
            error_log("Updating budget ID: $id with data: " . json_encode($data));

            $query = "UPDATE budgets 
                     SET amount = :amount
                     WHERE id_budget = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':amount', $data['amount']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $result = $stmt->execute();

            // Log result
            error_log("Budget update result: " . ($result ? "success" : "failed"));

            return $result;
        } catch (PDOException $e) {
            // Log error
            error_log("Error updating budget: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a budget
     * @param int $id Budget ID
     * @return bool Success flag
     */
    public function delete($id)
    {
        $query = "DELETE FROM budgets WHERE id_budget = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>