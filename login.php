<?php
require 'database.php';
require 'user.php';

// Start the session
session_start();

// Initialize the database connection
$database = new Database();
$pdo = $database->connect();

// Initialize the User class
$user = new User($pdo);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    // Attempt to log in the user
    $response = $user->login($email, $password);

    // Check if login was successful
    if ($response['success']) {
        // Store user information in session variables
        $_SESSION['user_id'] = $response['user']['user_id'];
        $_SESSION['user_name'] = $response['user']['user_name'];
        $_SESSION['email'] = $response['user']['email'];

        // Redirect to index.php
        header("Location: index.php");
        exit();
    } else {
        // Display error message
        $error_message = $response['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>
