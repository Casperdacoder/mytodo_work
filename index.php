<?php
//index.php
require_once 'database.php';
require_once 'crud.php';

class Task extends Crud {
    private $conn;
    private $database;

    public function __construct() {
        $this->database = new Database;
        $this->conn = $this->database->connect();
    }

    public function show($userId) {
        $query = "SELECT * FROM task WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function store($userId, $taskName) {
        $query = "INSERT INTO task (user_id, task_name, added_at) VALUES (:user_id, :task_name, :added_at)";
        $stmt = $this->conn->prepare($query);
        
        $createdAt = date('Y-m-d H:i:s');
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':task_name', $taskName);
        $stmt->bindParam(':added_at', $createdAt);
        
        return $stmt->execute();
    }

    public function update($taskId, $taskName) {
        $query = "UPDATE task SET task_name = :task_name WHERE task_id = :task_id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':task_id', $taskId);
        $stmt->bindParam(':task_name', $taskName);
        
        return $stmt->execute();
    }

    public function delete($taskId) {
        $query = "DELETE FROM task WHERE task_id = :task_id"; 
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':task_id', $taskId);
        
        return $stmt->execute();
    }

    public function toggleComplete($taskId, $isCompleted) {
        $query = "UPDATE task SET completed = :completed WHERE task_id = :task_id";
        $stmt = $this->conn->prepare($query);
        
        $completedValue = $isCompleted ? 1 : 0;
        $stmt->bindParam(':completed', $completedValue);
        $stmt->bindParam(':task_id', $taskId);
        
        return $stmt->execute();
    }
}

session_start();
$taskManager = new Task();
$userId = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_task']) && !empty(trim($_POST['new_task']))) {
        $taskManager->store($userId, htmlspecialchars($_POST['new_task']));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['delete_task'])) {
        $taskManager->delete($_POST['delete_task']);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['toggle_complete'])) {
        $taskId = $_POST['toggle_complete'];
        $isCompleted = isset($_POST['checkbox_completed']) && $_POST['checkbox_completed'] === 'true';
        $taskManager->toggleComplete($taskId, !$isCompleted);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['edit_task'])) {
        $taskId = $_POST['edit_task_id'];
        $newTaskName = htmlspecialchars($_POST['new_task_name']);
        $taskManager->update($taskId, $newTaskName);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$tasks = $taskManager->show($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>
    <link rel="stylesheet" href="task.css">
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>

        <!-- Task Form -->
        <form method="POST" action="" class="task-form">
            <input type="text" name="new_task" placeholder="Enter a new task" required>
            <button type="submit">Add Task</button>
        </form>

        <!-- Task Table -->
        <table class="task-table">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tasks)): ?>
                    <tr>
                        <td colspan="4">No tasks yet! Add a task above.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td>
                                <span id="task-name-<?php echo $task['task_id']; ?>" class="task-name <?php echo $task['completed'] ? 'completed' : ''; ?>">
                                    <?php echo $task['task_name']; ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($task['added_at'])); ?></td>
                            <td><?php echo $task['completed'] ? 'Completed' : 'Pending'; ?></td>
                            <td>
                                <div class="actions">
                                    <form method="POST" action="" onsubmit="return confirmToggle(this);">
                                        <input type="hidden" name="checkbox_completed" value="<?php echo $task['completed'] ? 'true' : 'false'; ?>">
                                        <input type="checkbox" name="toggle_complete" value="<?php echo $task['task_id']; ?>" 
                                               <?php echo $task['completed'] ? 'checked' : ''; ?>
                                               onclick="return confirmToggleCheckbox(this, '<?php echo $task['task_id']; ?>')">
                                    </form>
                                    <form method="POST" action="" style="display:inline;">
                                        <button type="submit" name="delete_task" value="<?php echo $task['task_id']; ?>">Delete</button>
                                    </form>
                                    <button type="button" onclick="document.getElementById('edit-form-<?php echo $task['task_id']; ?>').style.display='block'">Edit</button>
                                </div>

                                <!-- Edit Task Form -->
                                <div id="edit-form-<?php echo $task['task_id']; ?>" class="edit-form" style="display:none;">
                                    <form method="POST" action="">
                                        <input type="hidden" name="edit_task_id" value="<?php echo $task['task_id']; ?>">
                                        <input type="text" name="new_task_name" value="<?php echo $task['task_name']; ?>" required>
                                        <button type="submit" name="edit_task">Save</button>
                                        <button type="button" onclick="document.getElementById('edit-form-<?php echo $task['task_id']; ?>').style.display='none'">Cancel</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function confirmToggleCheckbox(checkbox, taskId) {
            const isChecked = checkbox.checked;
            const taskNameElement = document.getElementById('task-name-' + taskId);
            const confirmation = confirm("Are you sure you want to mark this task as " + (isChecked ? "completed" : "pending") + "?");

            if (!confirmation) {
                checkbox.checked = !isChecked; 
            } else {
                if (isChecked) {
                    taskNameElement.classList.add('completed');
                } else {
                    taskNameElement.classList.remove('completed');
                }
            }

            return confirmation;
        }
    </script>
</body>
</html>
