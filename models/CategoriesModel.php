<?php
require_once 'config/database.php';

class CategoriesModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Get categories by type and user
     * @param string $type
     * @param int $userId
     * @return array
     */
    public function getByType($type, $userId)
    {
        $query = "SELECT * FROM categories WHERE type = :type AND id_user = :userId ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create new category
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        $query = "INSERT INTO categories (id_user, name, type) VALUES (:userId, :name, :type)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $data['id_user'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':type', $data['type']);

        return $stmt->execute();
    }

    /**
     * Update category
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $query = "UPDATE categories SET name = :name WHERE id_category = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Get a category by ID
     * @param int $id Category ID
     * @return array|false The category data or false if not found
     */
    public function getById($id)
    {
        try {
            $query = "SELECT * FROM categories WHERE id_category = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error fetching category: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a category is in use by any transactions
     * @param int $id Category ID
     * @return bool True if in use, false otherwise
     */
    public function isInUse($id)
    {
        try {
            $query = "SELECT COUNT(*) as count FROM transactions WHERE id_category = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error checking if category is in use: " . $e->getMessage());
            return true; // Assume it's in use in case of error to prevent deletion
        }
    }

    /**
     * Delete a category
     * @param int $id Category ID
     * @return bool Success flag
     */
    public function delete($id)
    {
        try {
            // Log deletion attempt
            error_log("Deleting category with ID: $id");

            $query = "DELETE FROM categories WHERE id_category = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $result = $stmt->execute();

            // Log result
            error_log("Category deletion result: " . ($result ? "success" : "failed"));

            return $result;
        } catch (PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Get all categories for a user
     * @param int $userId
     * @return array
     */
    public function getAllByUserId($userId)
    {
        $query = "SELECT * FROM categories WHERE id_user = :userId ORDER BY type, name";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>