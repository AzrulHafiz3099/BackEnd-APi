<?php
require_once 'db_connection.php';

// Get the action
$action = $_POST['action'] ?? '';

// Log incoming action for debugging
error_log("Action: " . $action);

switch ($action) {
    
    case 'add_order':
        $userID = $_POST['userID'] ?? '';
        $cartID = $_POST['cartID'] ?? '';
        $totalPrice = $_POST['totalPrice'] ?? 0;
        $paymentMethod = $_POST['paymentMethod'] ?? '';

        error_log("UserID: " . $userID);
        error_log("CartID: " . $cartID);
        error_log("TotalPrice: " . $totalPrice);
        error_log("PaymentMethod: " . $paymentMethod);
        
        // Validate data
        if ($userID && $cartID && $totalPrice && $paymentMethod) {
            // Process the order based on payment method
            addOrder($conn, $cartID, $totalPrice, $paymentMethod);
        } else {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        error_log("Error: Invalid action.");
        break;
}

function addOrder($conn, $cartID, $totalPrice, $paymentMethod) {
    // Set PaymentStatus (default to COD)
    $PaymentStatus = "Pending";

    // Insert order into the orders table
    $query = "INSERT INTO `order` (CartID, TotalPrice, OrderDate, PaymentMethod, PaymentStatus) 
              VALUES (?, ?, NOW(), ?, ?)";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        // Corrected bind_param (CartID, TotalPrice, PaymentMethod, PaymentStatus)
        // cartID and paymentMethod are strings, totalPrice is a decimal (double)
        $stmt->bind_param("sdss", $cartID, $totalPrice, $paymentMethod, $PaymentStatus);
        
        if ($stmt->execute()) {
            // Get the last inserted order ID
            $orderID = $stmt->insert_id;

            // Successfully added order
            echo json_encode(["status" => "success", "message" => "Order placed successfully", "orderID" => $orderID]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to place the order"]);
            error_log("SQL Execution Error: " . $stmt->error);
        }
        
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare SQL query"]);
        error_log("SQL Preparation Error: " . $conn->error);
    }
}
?>
