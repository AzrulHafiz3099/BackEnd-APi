<?php
require_once 'db_connection.php'; // Include the connection file

header('Content-Type: application/json');

// Retrieve the action from the request
$action = $_POST['action'] ?? '';

// Log the action for debugging purposes
error_log("Received action: " . $action);

// Process the action
switch ($action) {
    case 'getVendors':
        getVendors($conn);
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

// Function to get all vendors
function getVendors($conn) {
    $stmt = $conn->prepare("SELECT VendorID, Fullname, Address, ContactNumber, Email FROM vendor");
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Server error: " . $conn->error]);
        return;
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $vendors = [];
        while ($row = $result->fetch_assoc()) {
            $vendors[] = $row;
        }
        echo json_encode(["status" => "success", "vendors" => $vendors]);
    } else {
        echo json_encode(["status" => "error", "message" => "No vendors found"]);
    }

    $stmt->close();
}


// Close the database connection
$conn->close();
?>
