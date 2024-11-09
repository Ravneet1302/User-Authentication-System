<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Use your database password
$dbname = "projectdb"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["message" => "Database connection failed: " . $conn->connect_error]));
}

// Handle request
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true);

if (isset($data['action'])) {
    switch ($data['action']) {
        case 'register':
            // Registration logic
            $username = $data['username'];
            $email = $data['email'];
            $password = $data['password'];

            // Check if user already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo json_encode(["message" => "Email already registered."]);
            } else {
                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $password);
                if ($stmt->execute()) {
                    echo json_encode(["message" => "Registration successful!"]);
                } else {
                    echo json_encode(["message" => "Registration failed."]);
                }
            }
            $stmt->close();
            break;

        case 'login':
            // Login logic
            $username = $data['username'];
            $email = $data['email'];
            $password = $data['password'];

            // Fetch user
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo json_encode(["message" => "Login successful!"]);
            } else {
                echo json_encode(["message" => "Invalid username or password."]);
            }
            $stmt->close();
            break;

        default:
            echo json_encode(["message" => "Invalid action."]);
            break;
    }
} else {
    echo json_encode(["message" => "No action specified."]);
}

$conn->close();
?>
