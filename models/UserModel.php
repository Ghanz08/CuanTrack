<?php
require_once 'config/database.php';

class UserModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Get user by ID
     * @param int $id
     * @return array|bool
     */
    public function getById($id)
    {
        $query = "SELECT * FROM users WHERE id_user = :id";
        // Query mengambil data pengguna berdasarkan ID pengguna

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if email exists
     * @param string $email
     * @return bool
     */
    public function emailExists($email)
    {
        $query = "SELECT COUNT(*) FROM users WHERE email = :email";
        // Query menghitung jumlah pengguna dengan email tertentu untuk memeriksa keberadaannya

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Update user profile
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        // Start building the query
        $query = "UPDATE users SET ";
        $params = [];

        // Add username and email
        if (isset($data['username'])) {
            $query .= "username = :username, ";
            $params[':username'] = $data['username'];
        }

        if (isset($data['email'])) {
            $query .= "email = :email, ";
            $params[':email'] = $data['email'];
        }

        // Add password if provided
        if (isset($data['password'])) {
            $query .= "password = :password, ";
            $params[':password'] = $data['password'];
        }

        // Add image if provided
        if (isset($data['image'])) {
            $query .= "image = :image, ";
            $params[':image'] = $data['image'];
        }

        // Remove trailing comma and space
        $query = rtrim($query, ", ");

        // Complete the query
        $query .= " WHERE id_user = :id";
        // Query memperbarui informasi profil pengguna secara dinamis berdasarkan data yang diberikan

        $params[':id'] = $id;

        // Execute the query
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $stmt->execute();
    }
}
?>