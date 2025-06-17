<?php
require_once 'config/database.php';

class WalletsModel
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllByUserId($userId)
    {
        $query = "SELECT * FROM wallets WHERE id_user = :id_user";
        // Query mengambil semua data dompet yang dimiliki oleh pengguna tertentu

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_user', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT * FROM wallets WHERE id_wallet = :id";
        // Query mengambil data dompet berdasarkan ID dompet

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        try {
            $query = "INSERT INTO wallets (id_user, name, balance, currency) 
                      VALUES (:id_user, :name, :balance, :currency)";
            // Query membuat dompet baru dengan data pengguna, nama, saldo, dan mata uang

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_user', $data['id_user'], PDO::PARAM_INT);
            $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(':balance', $data['balance'], PDO::PARAM_STR);
            $stmt->bindParam(':currency', $data['currency'], PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error for debugging
            error_log("Error creating wallet: " . $e->getMessage());
            return false;
        }
    }

    public function update($data)
    {
        $query = "UPDATE wallets SET name = :name, balance = :balance WHERE id_wallet = :id";
        // Query memperbarui nama dan saldo dompet berdasarkan ID dompet

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':balance', $data['balance'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM wallets WHERE id_wallet = :id";
        // Query menghapus dompet berdasarkan ID dompet

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>