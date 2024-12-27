<?php
require_once 'db_connection.php';

// Get the action
$action = $_POST['action'] ?? '';

// Log incoming action for debugging
error_log("Action: " . $action);

switch ($action) {
    case 'add_cart_item':
        $cartID = $_POST['CartID'] ?? '';
        $drugID = $_POST['DrugID'] ?? '';
        $quantity = $_POST['Quantity'] ?? 1;
        $price = $_POST['Price'] ?? 0.0;
        $genericName = $_POST['GenericName'] ?? '';
        $drugImage = $_POST['DrugImage'] ?? '';

        // Log incoming data for debugging
        error_log("CartID: " . $cartID);
        error_log("DrugID: " . $drugID);
        error_log("Quantity: " . $quantity);
        error_log("Price: " . $price);
        error_log("GenericName: " . $genericName);
        error_log("DrugImage: " . $drugImage);

        // Check if CartID and DrugID are provided
        if ($cartID && $drugID) {
            addCartItem($conn, $cartID, $drugID, $quantity, $price, $genericName, $drugImage);
        } else {
            echo json_encode(["status" => "error", "message" => "CartID and DrugID are required"]);
            error_log("Error: CartID and DrugID are required.");
        }
        break;
    
    case 'fetch_cart_items':
        $userID = $_POST['UserID'] ?? '';
        if (!empty($userID)) {
            getCartItems($conn, $userID);
        } else {
            echo json_encode(["status" => "error", "message" => "User ID is required"]);
        }
        break;

    case 'delete_cart_item':
        $cartItemID = $_POST['CartItemID'] ?? '';

        if ($cartItemID) {
            deleteCartItem($conn, $cartItemID);
        } else {
            echo json_encode(["status" => "error", "message" => "CartItemID is required"]);
            error_log("Error: CartItemID is required.");
        }
        break;

    case 'update_cart_item_quantity':
        $cartItemID = $_POST['CartItemID'] ?? '';
        $quantity = $_POST['Quantity'] ?? 1;
    
        if ($cartItemID && $quantity) {
            updateCartItemQuantity($conn, $cartItemID, $quantity);
        } else {
            echo json_encode(["status" => "error", "message" => "CartItemID and Quantity are required"]);
            error_log("Error: CartItemID and Quantity are required.");
        }
        break;
        
    

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        error_log("Error: Invalid action.");
        break;
}

// Function to add an item to the cart
function addCartItem($conn, $cartID, $drugID, $quantity, $price, $genericName, $drugImage) {
    // First, check if the item already exists in the cart
    $query = "SELECT * FROM cart_item WHERE CartID = ? AND DrugID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $cartID, $drugID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Item already exists in the cart, so update the quantity
        $row = $result->fetch_assoc();
        $newQuantity = $row['Quantity'] + $quantity; // Increase quantity by the amount the user wants to add

        // Update the quantity in the database
        $updateQuery = "UPDATE cart_item SET Quantity = ? WHERE CartID = ? AND DrugID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("iss", $newQuantity, $cartID, $drugID);

        if ($updateStmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Quantity updated in cart"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update quantity in cart"]);
        }

        $updateStmt->close();
    } else {
        // Item doesn't exist, so insert it into the cart
        $cartItemID = uniqid("ITEM_");
        $insertQuery = "INSERT INTO cart_item (CartItemID, CartID, DrugID, GenericName, DrugImage, Quantity, Price) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("sssssid", $cartItemID, $cartID, $drugID, $genericName, $drugImage, $quantity, $price);

        if ($insertStmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Item added to cart"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add item to cart"]);
        }

        $insertStmt->close();
    }

    // Close the initial check statement
    $stmt->close();
}


function getCartItems($conn, $userID) {
    if (empty($userID)) {
        echo json_encode(["status" => "error", "message" => "UserID is required"]);
        return;
    }

    $query = "SELECT ci.CartItemID, ci.CartID, ci.DrugID, ci.GenericName, ci.DrugImage, ci.Quantity, ci.Price
          FROM cart_item ci
          JOIN cart c ON ci.CartID = c.CartID
          WHERE c.UserID = ? AND c.Status = 'active'";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Failed to prepare the SQL query"]);
        error_log("SQL Error: " . $conn->error);
        return;
    }

    $stmt->bind_param("s", $userID);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $cartItems = [];

        while ($row = $result->fetch_assoc()) {
            $cartItems[] = $row;
        }

        echo json_encode(["status" => "success", "cart_items" => $cartItems]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to execute the SQL query"]);
        error_log("Execution Error: " . $stmt->error);
    }

    $stmt->close();
}

function deleteCartItem($conn, $cartItemID) {
    // Prepare the delete query
    $query = "DELETE FROM cart_item WHERE CartItemID = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $cartItemID); // Use 's' for string type parameter
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Item deleted"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete item"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare query"]);
    }
}

function updateCartItemQuantity($conn, $cartItemID, $quantity) {
    $query = "UPDATE cart_item SET Quantity = ? WHERE CartItemID = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("is", $quantity, $cartItemID); // Use 'i' for integer and 's' for string
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Quantity updated"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update quantity"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare query"]);
    }
}


?>
