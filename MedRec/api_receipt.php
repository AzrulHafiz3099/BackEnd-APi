<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
error_log("Action: " . $action);

switch ($action) {

    case 'addReceipt':
        $cartID = $_POST['cartID'] ?? '';
        if (!empty($cartID)) {
            addReceipt($conn, $cartID);
        } else {
            echo json_encode(["status" => "error", "message" => "Cart ID is required"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

function addReceipt($conn, $cartID) {
    // Prepare the SQL query to insert data into the receipt table
    $query = "
        INSERT INTO receipt (UserID, OrderID, GenericNames, Quantities, PaymentDate, TotalAmount, PaymentMethod, TransactionReference, Vendor)
        SELECT 
            c.UserID,
            o.OrderID,
            GROUP_CONCAT(dd.GenericName) AS GenericNames,
            GROUP_CONCAT(ci.Quantity) AS Quantities,
            o.OrderDate AS PaymentDate,
            o.TotalPrice AS TotalAmount,
            o.PaymentMethod AS PaymentMethod,
            NULL AS TransactionReference,
            GROUP_CONCAT(DISTINCT v.Fullname) AS Vendor
        FROM 
            cart c
        JOIN 
            cart_item ci ON c.CartID = ci.CartID
        JOIN 
            drug_details dd ON ci.DrugID = dd.DrugID
        JOIN 
            drug_supply ds ON dd.SupplyID = ds.SupplyID
        JOIN 
            vendor v ON ds.VendorID = v.VendorID
        JOIN 
            `order` o ON c.CartID = o.CartID
        WHERE 
            c.CartID = ?
        GROUP BY 
            c.UserID, o.OrderID, o.OrderDate, o.TotalPrice, o.PaymentMethod;
    ";

    // Prepare the statement
    $stmt = $conn->prepare($query);

    // Bind the CartID parameter to the query
    $stmt->bind_param("s", $cartID);

    // Execute the statement
    if ($stmt->execute()) {
        // After insertion, fetch the inserted data using OrderID
        $fetchQuery = "
            SELECT 
                UserID, OrderID, GenericNames, Quantities, PaymentDate, TotalAmount, PaymentMethod, TransactionReference, Vendor
            FROM 
                receipt
            WHERE 
                OrderID = (SELECT OrderID FROM `order` WHERE CartID = ?)
        ";

        // Prepare and execute the fetch query
        $fetchStmt = $conn->prepare($fetchQuery);
        $fetchStmt->bind_param("s", $cartID);
        $fetchStmt->execute();
        $result = $fetchStmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the data and return it as JSON
            $receipt = $result->fetch_assoc();
            echo json_encode(["status" => "success", "message" => "Receipt added successfully", "receipt" => $receipt]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to fetch the receipt details"]);
        }

        // Close the fetch statement
        $fetchStmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add receipt"]);
    }

    // Close the statement
    $stmt->close();
}
?>
