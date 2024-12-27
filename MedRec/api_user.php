<?php
require_once 'db_connection.php'; // Include the connection file
require_once 'vendor/autoload.php'; // Include the JWT library

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

// Add this to log incoming headers for debugging
$headers = apache_request_headers();
error_log(print_r($headers, true));

// Define a secret key (use a strong key in production)
define('SECRET_KEY', 'ZXhhbXBsZXNlY3JldGtleWZvc3RlYGVy');  // Change this to a more secure key in production

// Retrieve the action from the request
$action = $_POST['action'] ?? '';
error_log("Received action user: " . $action); // Log the action received for debugging

switch ($action) {
    case 'login':
        login($conn);
        break;
    case 'register':
        registerUser($conn);
        break;
    case 'getProfile':
        getUserProfile($conn);
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

// Function to authorize the JWT token
function authorizeJWT($conn) {
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        echo json_encode(["status" => "error", "message" => "Authorization header not found"]);
        return false;
    }

    $authHeader = $headers['Authorization'];
    if (strpos($authHeader, 'Bearer ') !== 0) {
        echo json_encode(["status" => "error", "message" => "Invalid token format"]);
        return false;
    }

    $jwt = str_replace('Bearer ', '', $authHeader);

    try {
        // Decode the JWT using the new method signature
        $decoded = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));
        return $decoded;
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Invalid token", "error" => $e->getMessage()]);
        return false;
    }
}


//test commit



function login($conn) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and password are required"]);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT UserID, Password FROM user_account WHERE Email = ?");
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
                "id" => $row['UserID']  // Include UserID in the payload instead of Email
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

    // Check if the email already exists
    $stmt_check = $conn->prepare("SELECT Email FROM user_account WHERE Email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
        $stmt_check->close();
        return;
    }
    $stmt_check->close();

    // Insert the new user with the role set to 'user'
    $stmt = $conn->prepare("INSERT INTO user_account (Fullname, Email, Password, Role) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Server error: " . $conn->error]);
        return;
    }

    $role = 'user'; // Explicitly set role as 'user'
    $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        // Retrieve the UserID based on the email after inserting the user
        $stmt_userID = $conn->prepare("SELECT UserID FROM user_account WHERE Email = ?");
        $stmt_userID->bind_param("s", $email);
        $stmt_userID->execute();
        $stmt_userID->store_result();
        $stmt_userID->bind_result($userID);
        
        if ($stmt_userID->fetch()) {
            error_log("UserID: " . $userID);
            echo json_encode(["status" => "success", "message" => "User registered successfully", "UserID" => $userID]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to retrieve UserID"]);
        }

        $stmt_userID->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to register user: " . $stmt->error]);
    }

    $stmt->close();
}




// Function to get user profile information
function getUserProfile($conn) {
    $decoded = authorizeJWT($conn);
    if (!$decoded) {
        return; // Exit the function if the token is invalid
    }

    $userID = $decoded->id;
    error_log("Debug: UserID before query: " . $userID); // Log to PHP error log

    error_log("Debug: Query with UserID: " . "SELECT UserID, Fullname, Email, Password, ProfilePicture FROM user_account WHERE UserID = " . "$userID");



    session_write_close();  // Close the session before querying
    $conn->query("FLUSH QUERY CACHE"); // Ensure cache is cleared
    $conn->query("RESET QUERY CACHE");
    $stmt2 = $conn->prepare("SELECT UserID, Fullname, Email, Password, ProfilePicture FROM user_account WHERE UserID = ?");
    if ($stmt2 === false) {
        die('MySQL prepare error: ' . $conn->error);
    }
    
    error_log("Debug: UserID before query 2: " . $userID); // Log to PHP error log
    $stmt2->bind_param("s", $userID);


    error_log("Debug: UserID before query 3: " . $userID); // Log to PHP error log
    $stmt2->execute();
    $conn->query("SET SESSION query_cache_type = OFF");

    $result2 = $stmt2->get_result();

    // Log the raw result
    error_log("Debug: Raw query result: " . print_r($result2, true));

    error_log("Debug: UserID before query 4: " . $userID); // Log to PHP error log

    if ($result2->num_rows > 0) {
        $user = $result2->fetch_assoc();

        error_log("Debug: UserID before query 5: " . $userID); // Log to PHP error log
        error_log("Debug: user before query: " . json_encode($user)); // Log the user array as JSON


        // Ensure the profile picture URL is complete and accessible
        $baseURL = 'http://192.168.0.28/BackEnd-APi/MedRec/ProfilePicture/'; // Change this to match your server setup
        $user['ProfilePicture'] = $baseURL . $user['ProfilePicture'];

        // Include the userID in the response for debugging
        echo json_encode(["status" => "success", "data" => $user, "debug_user_id" => $userID]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found", "debug_user_id" => $userID]);
    }

    $stmt2->close();
}


$conn->close();
?>
