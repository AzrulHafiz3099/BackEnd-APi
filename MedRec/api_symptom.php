<?php
require_once 'db_connection.php'; // Include the connection file

header('Content-Type: application/json');

// Retrieve the action from the request
$action = $_POST['action'] ?? '';

// Log the action for debugging purposes
error_log("Received action: " . $action);

switch ($action) {
    case 'getSymptoms':
        getSymptoms($conn);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

// Function to get symptoms from the database
function getSymptoms($conn) {
    // Query to fetch symptoms from the database
    $sql = "SELECT `SymptomID`, `Description` FROM symptoms"; // Make sure `SymptomID` is included
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $symptoms = array();
        while($row = $result->fetch_assoc()) {
            $symptoms[] = array("SymptomID" => $row['SymptomID'], "Description" => $row['Description']);
        }
        echo json_encode(["status" => "success", "symptoms" => $symptoms]);
    } else {
        echo json_encode(["status" => "error", "message" => "No symptoms found"]);
    }
}

?>