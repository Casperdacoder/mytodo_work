<?php
//register.php
require 'database.php';
require 'user.php';

// Start the session
session_start();

// Initialize the database connection and pass it to the User class
$database = new Database();
$pdo = $database->connect();
$user = new User($pdo);

// Define variables and set to empty values
$username = $email = $password = $confirm_password = "";
$usernameErr = $emailErr = $passwordErr = $confirmPasswordErr = "";
$success = "";
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = true;

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $usernameErr = "Username is required";
        $valid = false;
    } else {
        $username = htmlspecialchars(trim($_POST["username"]));
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $emailErr = "Email is required";
        $valid = false;
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
        $valid = false;
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $passwordErr = "Password is required";
        $valid = false;
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $passwordErr = "Password must be at least 6 characters long";
        $valid = false;
    } else {
        $password = trim($_POST["password"]); // Store the plain text password directly
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirmPasswordErr = "Please confirm your password";
        $valid = false;
    } elseif ($_POST["password"] != $_POST["confirm_password"]) {
        $confirmPasswordErr = "Passwords do not match";
        $valid = false;
    }

    // If everything is valid, save user data to the database
    if ($valid) {
        // Attempt to register user
        $response = $user->register($username, $email, $password); // Use plain password here
        if (isset($response['success']) && $response['success']) {
            $success = $response['message'];
        } else {
            $error_message = $response['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
            <form method="GET" action="login.php">
                <button type="submit">Go to Login</button>
            </form>
        <?php else: ?>
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="username">Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>" required>
                <span class="error"><?php echo $usernameErr; ?></span>

                <label for="email">Email</label>
                <input type="email" name="email" value="<?php echo $email; ?>" required>
                <span class="error"><?php echo $emailErr; ?></span>

                <label for="password">Password</label>
                <input type="password" name="password" required>
                <span class="error"><?php echo $passwordErr; ?></span>

                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" required>
                <span class="error"><?php echo $confirmPasswordErr; ?></span>

                <button type="submit">Register</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
