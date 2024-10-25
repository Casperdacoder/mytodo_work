<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header('Location: index.php');

    exit;

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Todo List</title>

    <link rel="stylesheet" href="assets/css/styles.css">

</head>

<body>

    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>

    <div id="message"></div>

    <button id="logoutButton">Logout</button>

    <div>

        <input type="text" id="newTask" placeholder="New Task">

        <button id="addTaskButton">Add Task</button>

    </div>

    <div id="todoApp">

        <h3>Your Tasks</h3>

        <table>

          <thead>

            <tr>

              <th>ID</th>

              <th>Task</th>

              <th>Date Added</th>

              <th>Status</th>

              <th>Date Completed</th>

              <th>Actions</th>

            </tr>

          </thead>

          <tbody id="taskTableBody">

          </tbody>

        </table>

    </div>

    <script src="assets/js/todo.js"></script>

</body>

</html>