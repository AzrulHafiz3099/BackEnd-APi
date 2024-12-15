<?php
require_once 'db_connection.php'; // Include the connection file

header('Content-Type: application/json');

// Retrieve the action from the request
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'getDrugDetails':
        getDrugDetails($conn);
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

// Function to get drug details
function getDrugDetails($conn) {
    $drugID = $_POST['DrugID'] ?? '';

    if (empty($drugID)) {
        echo json_encode(["status" => "error", "message" => "DrugID is required"]);
        return;
    }

    $stmt = $conn->prepare("SELECT GenericName, Price, DrugImage FROM drug_details WHERE DrugID = ?");
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Server error: " . $conn->error]);
        return;
    }

    $stmt->bind_param("s", $drugID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $drugDetails = $result->fetch_assoc();
        // Append base URL to the image
        $baseURL = 'http://192.168.0.28/BackEnd-APi/MedRec/DrugImages/';
        $drugDetails['DrugImage'] = $baseURL . $drugDetails['DrugImage'];
        echo json_encode(["status" => "success", "drugDetails" => $drugDetails]);
    } else {
        echo json_encode(["status" => "error", "message" => "Drug not found"]);
    }

    $stmt->close();
}

$conn->close();
?>
