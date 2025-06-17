<?php

class Database
{
    private $host = "localhost";
    private $db_name = "cuantrack";
    private $username = "root";
    private $password = "";
    private $conn;
    private $lastError = null;

    public function getConnection()
    {
        $this->conn = null;
        $this->lastError = null;

        try {
            error_log("Attempting to connect to database: {$this->db_name} on {$this->host}");
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
            error_log("Database connection established successfully");
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log("Connection error: " . $e->getMessage());
        }

        return $this->conn;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function testConnection()
    {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                $stmt = $conn->query("SELECT 1");
                $result = $stmt->fetch();
                error_log("Database test query result: " . json_encode($result));
                return true;
            }
            return false;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log("Database test connection error: " . $e->getMessage());
            return false;
        }
    }
}
?>