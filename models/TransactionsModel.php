<?php
require_once 'config/database.php';

class TransactionsModel
{
    protected $db;

    public function __construct()
    {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Fetch transactions for the logged-in user
     * @param int $userId
     * @return array
     */
    public function getByUserId($userId)
    {
        $query = "SELECT t.*, c.name as category_name 
                  FROM transactions t 
                  LEFT JOIN categories c ON t.id_category = c.id_category 
                  WHERE t.id_user = :id_user
                  ORDER BY t.transaction_date DESC";
        // Query mengambil semua transaksi milik pengguna dengan nama kategori, diurutkan berdasarkan tanggal

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_user', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByWalletId($walletId)
    {
        $sql = "SELECT t.*, c.name as category_name 
                FROM transactions t 
                LEFT JOIN categories c ON t.id_category = c.id_category 
                WHERE t.id_wallet = :wallet_id
                ORDER BY t.transaction_date DESC";
        // Query mengambil semua transaksi untuk dompet tertentu dengan nama kategori, diurutkan berdasarkan tanggal

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':wallet_id', $walletId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIncomeByWalletId($walletId)
    {
        $sql = "SELECT t.*, c.name as category_name 
                FROM transactions t 
                LEFT JOIN categories c ON t.id_category = c.id_category 
                WHERE t.id_wallet = :wallet_id AND t.type = 'income'
                ORDER BY t.transaction_date DESC";
        // Query mengambil transaksi pemasukan untuk dompet tertentu dengan nama kategori

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':wallet_id', $walletId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExpensesByWalletId($walletId)
    {
        $sql = "SELECT t.*, c.name as category_name 
                FROM transactions t 
                LEFT JOIN categories c ON t.id_category = c.id_category 
                WHERE t.id_wallet = :wallet_id AND t.type = 'expense'
                ORDER BY t.transaction_date DESC";
        // Query mengambil transaksi pengeluaran untuk dompet tertentu dengan nama kategori

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':wallet_id', $walletId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentByUserId($userId, $limit = 5)
    {
        $sql = "SELECT t.*, c.name as category_name, w.name as wallet_name
                FROM transactions t
                JOIN wallets w ON t.id_wallet = w.id_wallet
                LEFT JOIN categories c ON t.id_category = c.id_category
                WHERE t.id_user = :id_user
                ORDER BY t.transaction_date DESC
                LIMIT :limit";
        // Query mengambil transaksi terbaru dengan nama dompet dan kategori, dibatasi jumlah tertentu

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_user', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        // Get category type to determine transaction type
        $categoryStmt = $this->db->prepare("SELECT type FROM categories WHERE id_category = :id_category");
        // Query mengambil tipe kategori untuk menentukan jenis transaksi

        $categoryStmt->bindParam(':id_category', $data['id_category'], PDO::PARAM_INT);
        $categoryStmt->execute();
        $category = $categoryStmt->fetch(PDO::FETCH_ASSOC);

        // Set transaction type based on category
        $transactionType = $category ? $category['type'] : 'expense';

        $sql = "INSERT INTO transactions (id_user, id_wallet, id_category, amount, type, description, transaction_date) 
                VALUES (:id_user, :id_wallet, :id_category, :amount, :type, :description, :transaction_date)";
        // Query membuat transaksi baru dengan data pengguna, dompet, kategori, jumlah, tipe, deskripsi, dan tanggal

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_user', $data['id_user']);
        $stmt->bindParam(':id_wallet', $data['id_wallet']);
        $stmt->bindParam(':id_category', $data['id_category']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':type', $transactionType);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':transaction_date', $data['transaction_date']);
        return $stmt->execute();
    }

    public function getSummaryByWalletId($walletId)
    {
        $sql = "SELECT 
                SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE 0 END) as total_income,
                SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END) as total_expenses
                FROM transactions t
                WHERE t.id_wallet = :wallet_id";
        // Query menghitung total pemasukan dan pengeluaran untuk dompet tertentu menggunakan agregasi kondisional

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':wallet_id', $walletId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch transactions for the logged-in user with wallet names
     * @param int $userId
     * @return array
     */
    public function getByUserIdWithWalletNames($userId)
    {
        $query = "SELECT t.*, c.name as category_name, w.name as wallet_name 
                  FROM transactions t 
                  LEFT JOIN categories c ON t.id_category = c.id_category 
                  LEFT JOIN wallets w ON t.id_wallet = w.id_wallet
                  WHERE t.id_user = :id_user
                  ORDER BY t.transaction_date DESC";
        // Query mengambil semua transaksi pengguna dengan nama kategori dan dompet, diurutkan berdasarkan tanggal

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_user', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get transaction by ID
     * @param int $id
     * @return array|bool
     */
    public function getById($id)
    {
        $query = "SELECT t.*, c.name as category_name, w.name as wallet_name 
                  FROM transactions t 
                  LEFT JOIN categories c ON t.id_category = c.id_category 
                  LEFT JOIN wallets w ON t.id_wallet = w.id_wallet
                  WHERE t.id_transaction = :id";
        // Query mengambil transaksi tertentu dengan nama kategori dan dompet

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update transaction
     * @param array $data
     * @return bool
     */
    public function update($data)
    {
        $query = "UPDATE transactions 
                  SET id_wallet = :id_wallet,
                      id_category = :id_category,
                      amount = :amount,
                      type = :type,
                      description = :description,
                      transaction_date = :transaction_date
                  WHERE id_transaction = :id_transaction";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_transaction', $data['id_transaction'], PDO::PARAM_INT);
        $stmt->bindParam(':id_wallet', $data['id_wallet'], PDO::PARAM_INT);
        $stmt->bindParam(':id_category', $data['id_category'], PDO::PARAM_INT);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':transaction_date', $data['transaction_date']);

        return $stmt->execute();
    }

    /**
     * Delete transaction
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $query = "DELETE FROM transactions WHERE id_transaction = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Verify if transaction belongs to user
     * @param int $transactionId
     * @param int $userId
     * @return bool
     */
    public function verifyOwnership($transactionId, $userId)
    {
        $query = "SELECT COUNT(*) FROM transactions WHERE id_transaction = :id AND id_user = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $transactionId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Count transactions based on filters
     * @param array $filters
     * @return int
     */
    public function countByFilters($filters)
    {
        $conditions = [];
        $params = [];

        // User ID filter (required)
        $conditions[] = "t.id_user = :user_id";
        $params[':user_id'] = $filters['user_id'];

        // Optional filters
        if (isset($filters['wallet_id'])) {
            $conditions[] = "t.id_wallet = :wallet_id";
            $params[':wallet_id'] = $filters['wallet_id'];
        }

        if (isset($filters['category_id'])) {
            $conditions[] = "t.id_category = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }

        if (isset($filters['type'])) {
            $conditions[] = "t.type = :type";
            $params[':type'] = $filters['type'];
        }

        if (isset($filters['date_from'])) {
            $conditions[] = "t.transaction_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (isset($filters['date_to'])) {
            $conditions[] = "t.transaction_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $whereClause = implode(" AND ", $conditions);

        $query = "SELECT COUNT(*) FROM transactions t WHERE $whereClause";
        $stmt = $this->db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get transactions by filters with pagination
     * @param array $filters
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getByFilters($filters, $limit = null, $offset = null)
    {
        $conditions = [];
        $params = [];

        // User ID filter (required)
        $conditions[] = "t.id_user = :user_id";
        $params[':user_id'] = $filters['user_id'];

        // Optional filters
        if (isset($filters['wallet_id'])) {
            $conditions[] = "t.id_wallet = :wallet_id";
            $params[':wallet_id'] = $filters['wallet_id'];
        }

        if (isset($filters['category_id'])) {
            $conditions[] = "t.id_category = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }

        if (isset($filters['type'])) {
            $conditions[] = "t.type = :type";
            $params[':type'] = $filters['type'];
        }

        if (isset($filters['date_from'])) {
            $conditions[] = "t.transaction_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (isset($filters['date_to'])) {
            $conditions[] = "t.transaction_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $whereClause = implode(" AND ", $conditions);

        $query = "SELECT t.*, c.name as category_name, w.name as wallet_name 
                  FROM transactions t 
                  LEFT JOIN categories c ON t.id_category = c.id_category 
                  LEFT JOIN wallets w ON t.id_wallet = w.id_wallet
                  WHERE $whereClause
                  ORDER BY t.transaction_date DESC";

        // Add pagination if needed
        if ($limit !== null) {
            $query .= " LIMIT :limit";
            if ($offset !== null) {
                $query .= " OFFSET :offset";
            }
        }

        $stmt = $this->db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>