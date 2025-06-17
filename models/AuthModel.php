<?php
namespace Auth;

use PDO;

class AuthModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function validateUser($username, $password)
    {
        $query = "SELECT password FROM users WHERE email = :email";
        // Query mengambil password yang dienkripsi untuk pengguna dengan email tertentu

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashedPassword = $row['password'];

            // Verify the password
            return password_verify($password, $hashedPassword);
        }

        return false;
    }

    public function getUserId($email)
    {
        $query = "SELECT id_user FROM users WHERE email = :email";
        // Query mengambil ID pengguna berdasarkan alamat email

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getUserName($email)
    {
        $query = "SELECT username FROM users WHERE email = :email";
        // Query mengambil nama pengguna berdasarkan alamat email

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function registerUser($username, $email, $password)
    {
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        // Query membuat pengguna baru dengan nama pengguna, email, dan password yang dienkripsi

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        return $stmt->execute();
    }

    public function emailExists($email)
    {
        $query = "SELECT COUNT(*) FROM users WHERE email = :email";
        // Query menghitung pengguna dengan email tertentu untuk memeriksa keberadaannya

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}