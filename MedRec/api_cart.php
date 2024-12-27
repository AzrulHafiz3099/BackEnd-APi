<?php
require_once 'db_connection.php';

header('Content-Type: application/json');


$action = $_POST['action'] ?? '';

switch ($action) {
    case 'check_or_create_cart':
        $userID = $_POST['UserID'] ?? '';
        if (!empty($userID)) {
            checkOrCreateCart($conn, $userID);
        } else {
            echo json_encode(["status" => "error", "message" => "User ID is required"]);
        }
        break;

    case 'addCart':
        $userID = $_POST['UserID'] ?? '';
        if (!empty($userID)) {
            addCart($conn, $userID);
        } else {
            echo json_encode(["status" => "error", "message" => "User ID is required"]);
        }
        break;

    case 'updateCart':
        $userID = $_POST['userID'] ?? '';
        $cartID = $_POST['cartID'] ?? '';
        $status = 'Completed';
    
        if (!empty($userID) && !empty($cartID) && !empty($status)) {
            updateCartStatus($conn, $userID, $cartID, $status);
        } else {
            echo json_encode(["status" => "error", "message" => "UserID, CartID, and status are required"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

function checkOrCreateCart($conn, $userID) {
    // Check if user already has a cart
    $query = "SELECT CartID FROM cart WHERE UserID = ? AND Status = 'active'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Cart exists, fetch CartID
        $row = $result->fetch_assoc();
        echo json_encode(["status" => "success", "CartID" => $row['CartID']]);
    } else {
        // Create a new cart
        $cartID = uniqid("CART_");
        $createdDate = date("Y-m-d");
        $status = "active";

        $insertQuery = "INSERT INTO cart (CartID, UserID, createdDate, Status) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssss", $cartID, $userID, $createdDate, $status);

        if ($insertStmt->execute()) {
            echo json_encode(["status" => "success", "CartID" => $cartID]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to create cart"]);
        }
    }
}

function addCart($conn) {
    // Get the UserID from the POST request
    $userID = $_POST['UserID'] ?? '';  // Assuming 'UserID' is passed via POST
    error_log("UserID received: " . $userID);  // Log to check if UserID is correctly passed


    if (empty($userID)) {
        echo json_encode(["status" => "error", "message" => "UserID is required"]);
        return;
    }

    $createdDate = date("Y-m-d");
    $status = "active";

    $insertQuery = "INSERT INTO cart (UserID, createdDate, Status) VALUES (?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("sss", $userID, $createdDate, $status); // Corrected the binding to "sss"

    if ($insertStmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to create cart"]);
    }
}

function updateCartStatus($conn, $userID, $cartID, $status) {
    // Update the cart status to "completed"
    $query = "UPDATE cart SET Status = ? WHERE CartID = ? AND UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $status, $cartID, $userID);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Cart status updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update cart status"]);
    }
}

?>
