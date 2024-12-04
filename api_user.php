<?php
require_once 'db_connection.php'; // Include the connection file
require_once 'vendor/autoload.php'; // Include the JWT library

use \Firebase\JWT\JWT;

header('Content-Type: application/json');

// Define a secret key (use a strong key in production)
define('SECRET_KEY', 'ZXhhbXBsZXNlY3JldGtleWZvc3RlYGVy');  // Change this to a more secure key in production

// Retrieve the action from the request
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        login($conn);
        break;
    case 'register':
        registerUser($conn);
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

function login($conn) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and password are required"]);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT Password, Email FROM user WHERE Email = ?");
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error); // Show error if prepare fails
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            // Successful login, generate JWT
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600;  // JWT valid for 1 hour from the issued time
            $payload = array(
                "iat" => $issuedAt,
                "exp" => $expirationTime,
                "email" => $row['Email']  // Include email in the payload instead of ID
            );

            // Encode the JWT with the algorithm 'HS256'
            $jwt = JWT::encode($payload, SECRET_KEY, 'HS256');

            // Respond with the token
            echo json_encode(["status" => "success", "message" => "Login successful", "token" => $jwt]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
    }

    $stmt->close();
}

function registerUser($conn) {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($full_name) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        return;
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO user (Username, Email, Password) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error); // Show error if prepare fails
    }

    $stmt->bind_param("sss", $full_name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User registered successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to register user: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
