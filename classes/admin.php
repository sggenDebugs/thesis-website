<?php
class Admin{
    private $mysqli;
    public function __construct($mysqli) {
        $this->$mysqli = $mysqli;
    }
    
    public function register($first_name, $last_name, $email, $password, $contact_number, $gov_id) {
        // Validate inputs
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($gov_id)) {
            return "Please input the required fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }

        // Check if email or government ID already exists (unique fields)
        $stmt = $this->mysqli->prepare("SELECT id FROM users WHERE email = ? OR gov_id = ?");
        $stmt->bind_param('ss', $email, $gov_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return "Email or Government ID already exists.";
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the database
        $stmt = $this->mysqli->prepare("INSERT INTO users (first_name, last_name, email, password, contact_num, gov_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $first_name, $last_name, $email, $hashed_password, $contact_number, $gov_id);

        if ($stmt->execute()) {
            return "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            return "Registration failed. Please try again.";
        }
    }

    // Method to log in a user
    public function login($email, $password) {
        // Fetch user from database
        $stmt = $this->mysqli->prepare("SELECT id, email, password FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                // Start session and store user data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                return true;
            } else {
                return "Invalid email or password.";
            }
            
        } else {
            return "Login failed. Please try again.";
        }
        
        
    }
}