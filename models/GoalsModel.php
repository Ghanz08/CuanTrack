<?php
require_once 'config/database.php';

class GoalsModel
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Fetch goals for the logged-in user
     * @param int $userId
     * @return array
     */
    public function getByUserId($userId)
    {
        $query = "SELECT * FROM goals WHERE id_user = :id_user ORDER BY target_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_user', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($goals as &$goal) {
            $goal['percentage'] = $goal['target_amount'] > 0
                ? min(100, round(($goal['current_amount'] / $goal['target_amount']) * 100))
                : 0;

            // Determine status based on percentage and target date
            $daysLeft = (strtotime($goal['target_date']) - time()) / (60 * 60 * 24);

            if ($goal['percentage'] >= 100) {
                $goal['status'] = 'completed';
                $goal['status_class'] = 'success';
            } elseif ($daysLeft < 0) {
                $goal['status'] = 'expired';
                $goal['status_class'] = 'danger';
            } elseif ($daysLeft < 30 && $goal['percentage'] < 70) {
                $goal['status'] = 'at-risk';
                $goal['status_class'] = 'warning';
            } else {
                $goal['status'] = 'on-track';
                $goal['status_class'] = 'info';
            }

            $goal['days_left'] = max(0, ceil($daysLeft));
        }

        return $goals;
    }

    /**
     * Get a single goal by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        $query = "SELECT * FROM goals WHERE id_goal = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $goal = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($goal) {
            $goal['percentage'] = $goal['target_amount'] > 0
                ? min(100, round(($goal['current_amount'] / $goal['target_amount']) * 100))
                : 0;

            // Determine status based on percentage and target date
            $daysLeft = (strtotime($goal['target_date']) - time()) / (60 * 60 * 24);

            if ($goal['percentage'] >= 100) {
                $goal['status'] = 'completed';
                $goal['status_class'] = 'success';
            } elseif ($daysLeft < 0) {
                $goal['status'] = 'expired';
                $goal['status_class'] = 'danger';
            } elseif ($daysLeft < 30 && $goal['percentage'] < 70) {
                $goal['status'] = 'at-risk';
                $goal['status_class'] = 'warning';
            } else {
                $goal['status'] = 'on-track';
                $goal['status_class'] = 'info';
            }

            $goal['days_left'] = max(0, ceil($daysLeft));
        }

        return $goal;
    }

    /**
     * Create a new goal
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        $query = "INSERT INTO goals (id_user, title, target_amount, current_amount, target_date) 
                  VALUES (:id_user, :title, :target_amount, :current_amount, :target_date)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_user', $data['id_user'], PDO::PARAM_INT);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':target_amount', $data['target_amount']);
        $stmt->bindParam(':current_amount', $data['current_amount']);
        $stmt->bindParam(':target_date', $data['target_date']);

        return $stmt->execute();
    }

    /**
     * Update a goal
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $query = "UPDATE goals SET 
                  title = :title, 
                  target_amount = :target_amount, 
                  current_amount = :current_amount, 
                  target_date = :target_date 
                  WHERE id_goal = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':target_amount', $data['target_amount']);
        $stmt->bindParam(':current_amount', $data['current_amount']);
        $stmt->bindParam(':target_date', $data['target_date']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Delete a goal
     * @param int $id Goal ID
     * @return bool Success flag
     */
    public function delete($id)
    {
        try {
            // Log the delete attempt
            error_log("Deleting goal with ID: $id");

            $query = "DELETE FROM goals WHERE id_goal = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $result = $stmt->execute();

            // Log the result
            error_log("Goal deletion result: " . ($result ? "success" : "failed"));

            return $result;
        } catch (PDOException $e) {
            // Log any error
            error_log("Error deleting goal: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update goal progress
     * @param int $id
     * @param float $amount
     * @return bool
     */
    public function updateProgress($id, $amount)
    {
        $query = "UPDATE goals SET current_amount = current_amount + :amount WHERE id_goal = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Get recent goals (limited number)
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getRecent($userId, $limit = 3)
    {
        $query = "SELECT * FROM goals 
                  WHERE id_user = :id_user 
                  ORDER BY target_date ASC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_user', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($goals as &$goal) {
            $goal['percentage'] = $goal['target_amount'] > 0
                ? min(100, round(($goal['current_amount'] / $goal['target_amount']) * 100))
                : 0;

            // Calculate days left
            $daysLeft = (strtotime($goal['target_date']) - time()) / (60 * 60 * 24);
            $goal['days_left'] = max(0, ceil($daysLeft));

            // Determine status
            if ($goal['percentage'] >= 100) {
                $goal['status'] = 'completed';
                $goal['status_class'] = 'success';
            } elseif ($daysLeft < 0) {
                $goal['status'] = 'expired';
                $goal['status_class'] = 'danger';
            } elseif ($daysLeft < 30 && $goal['percentage'] < 70) {
                $goal['status'] = 'at-risk';
                $goal['status_class'] = 'warning';
            } else {
                $goal['status'] = 'on-track';
                $goal['status_class'] = 'info';
            }
        }

        return $goals;
    }
}
?>