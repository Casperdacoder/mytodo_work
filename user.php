<?php
class User {
    private $pdo;

    // Constructor to initialize the database connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Method to register a new user
    public function register($username, $email, $password) {
        // Check if email already exists
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            return ["success" => false, "message" => "Email already exists."];
        }

        // Insert new user into the database with plain password
        $sql = "INSERT INTO user (user_name, email, password) VALUES (:user_name, :email, :password)";
        $stmt = $this->pdo->prepare($sql);

        // Bind parameters and execute
        return $stmt->execute([
            'user_name' => $username,
            'email' => $email,
            'password' => $password, // Use plain password here
        ]) ? ["success" => true, "message" => "Registration successful!"] : ["success" => false, "message" => "Registration failed."];
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM user WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Directly compare the plain password
            if ($password === $user['password']) {
                return [
                    'success' => true,
                    'user' => $user
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Wrong password.'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'User not found.'
            ];
        }
    }
}
?>
