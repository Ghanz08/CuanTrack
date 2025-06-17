<?php
require_once 'core/Middleware.php';
require_once 'models/GoalsModel.php';

class GoalsController
{
    private $goalsModel;

    public function __construct()
    {
        $this->goalsModel = new GoalsModel();
    }

    public function index()
    {
        auth_required();
        $userId = $_SESSION['user_id'];
        $goals = $this->goalsModel->getByUserId($userId);
        $totalGoals = count($goals);
        $completedGoals = 0;
        $totalSaved = 0;
        $totalTarget = 0;

        foreach ($goals as $goal) {
            if ($goal['percentage'] >= 100) {
                $completedGoals++;
            }
            $totalSaved += $goal['current_amount'];
            $totalTarget += $goal['target_amount'];
        }

        $completionRate = $totalGoals > 0 ? round(($completedGoals / $totalGoals) * 100) : 0;
        $overallProgress = $totalTarget > 0 ? round(($totalSaved / $totalTarget) * 100) : 0;

        require_once 'views/goals/index.php';
    }

    public function create()
    {
        auth_required();
        require_once 'views/goals/create.php';
    }

    public function store()
    {
        auth_required();
        $userId = $_SESSION['user_id'];
        $title = trim($_POST['title'] ?? '');
        $targetAmount = floatval($_POST['target_amount'] ?? 0);
        $currentAmount = floatval($_POST['current_amount'] ?? 0);
        $targetDate = $_POST['target_date'] ?? date('Y-m-d', strtotime('+1 month'));

        if (empty($title) || $targetAmount <= 0) {
            header('Location: /goals/create?error=invalid_input');
            exit;
        }

        $data = [
            'id_user' => $userId,
            'title' => $title,
            'target_amount' => $targetAmount,
            'current_amount' => $currentAmount,
            'target_date' => $targetDate
        ];

        if ($this->goalsModel->create($data)) {
            header('Location: /goals?success=goal_created');
        } else {
            header('Location: /goals/create?error=create_failed');
        }
        exit;
    }

    public function view($id)
    {
        auth_required();
        $userId = $_SESSION['user_id'];
        $goal = $this->goalsModel->getById($id);

        if (!$goal || $goal['id_user'] != $userId) {
            header('Location: /goals?error=goal_not_found');
            exit;
        }

        require_once 'views/goals/view.php';
    }

    public function edit($id)
    {
        auth_required();
        $userId = $_SESSION['user_id'];
        $goal = $this->goalsModel->getById($id);

        if (!$goal || $goal['id_user'] != $userId) {
            header('Location: /goals?error=goal_not_found');
            exit;
        }

        require_once 'views/goals/edit.php';
    }

    public function update()
    {
        auth_required();
        $userId = $_SESSION['user_id'];
        $goalId = intval($_POST['id_goal'] ?? 0);
        $goal = $this->goalsModel->getById($goalId);

        if (!$goal || $goal['id_user'] != $userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Goal not found or unauthorized']);
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        $targetAmount = floatval($_POST['target_amount'] ?? 0);
        $currentAmount = floatval($_POST['current_amount'] ?? 0);
        $targetDate = $_POST['target_date'] ?? date('Y-m-d', strtotime('+1 month'));

        if (empty($title) || $targetAmount <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            exit;
        }

        $data = [
            'title' => $title,
            'target_amount' => $targetAmount,
            'current_amount' => $currentAmount,
            'target_date' => $targetDate
        ];

        $success = $this->goalsModel->update($goalId, $data);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Goal updated successfully',
                'redirect' => "/goals/view/$goalId"
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update goal']);
        }
    }

    public function delete()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        // Check if the request is a JSON API request
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === 'application/json') {
            // Handle API request with JSON
            $data = json_decode(file_get_contents('php://input'), true);
            $goalId = intval($data['id_goal'] ?? 0);
        } else {
            // Handle regular form submission
            $goalId = intval($_POST['id_goal'] ?? 0);
        }

        // Debug logging
        error_log("Attempting to delete goal ID: $goalId for user ID: $userId");

        $goal = $this->goalsModel->getById($goalId);

        if (!$goal || $goal['id_user'] != $userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Goal not found or unauthorized']);
            exit;
        }

        try {
            $success = $this->goalsModel->delete($goalId);

            header('Content-Type: application/json');
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Goal deleted successfully',
                    'redirect' => '/goals'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete goal']);
            }
        } catch (Exception $e) {
            error_log("Error deleting goal: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error deleting goal: ' . $e->getMessage()
            ]);
        }
    }

    public function updateProgress()
    {
        auth_required();
        $userId = $_SESSION['user_id'];

        // Check if the request has JSON content type
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
            // If not JSON, try to get data from POST
            $data = $_POST;
        }

        if (!isset($data['id_goal']) || !isset($data['amount'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $goalId = intval($data['id_goal']);
        $amount = floatval($data['amount']);
        $goal = $this->goalsModel->getById($goalId);

        if (!$goal || $goal['id_user'] != $userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Goal not found or unauthorized']);
            exit;
        }

        $success = $this->goalsModel->updateProgress($goalId, $amount);
        $updatedGoal = $this->goalsModel->getById($goalId);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Progress updated successfully',
                'goal' => $updatedGoal
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update progress']);
        }
    }
}
?>