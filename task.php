<?php

require_once 'Database.php';
require_once 'Crud.php';

class Task extends Crud {
    private $conn;
    private $database;

    public function __construct() {
        $this->database = new Database();
        $this->conn = $this->database->connect();
    }

    public function show($userId) {
        $query = "SELECT * FROM tasks WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return count($tasks) > 0 ? $tasks : ['message' => 'No tasks available'];
    }

    public function store($userId, $taskName) {
        $query = "INSERT INTO tasks (user_id, task_name, created_at) VALUES (:user_id, :task_name, :created_at)";
        $stmt = $this->conn->prepare($query);

        $createdAt = date('Y-m-d H:i:s');
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':task_name', $taskName);
        $stmt->bindParam(':created_at', $createdAt);
        
        return $stmt->execute();
    }

    public function update($taskId, $taskName) {
        $query = "UPDATE tasks SET task_name = :task_name WHERE task_id = :task_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':task_id', $taskId);
        $stmt->bindParam(':task_name', $taskName);
        
        return $stmt->execute();
    }

    public function delete($taskId) {
        $query = "DELETE FROM tasks WHERE task_id = :task_id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':task_id', $taskId);
        
        return $stmt->execute();
    }
}
