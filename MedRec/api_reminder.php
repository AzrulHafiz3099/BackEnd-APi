<?php
require_once 'db_connection.php'; // Include the connection file

header('Content-Type: application/json');

// Retrieve the action from the request
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'addReminder':
        addReminder($conn);
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

function addReminder($conn) {
    $patientID = $_POST['PatientID'] ?? '';
    $title = $_POST['Title'] ?? '';
    $description = $_POST['Description'] ?? '';
    $genericName = $_POST['GenericName'] ?? '';
    $reminderDate = $_POST['ReminderDate'] ?? '';
    $reminderTime = $_POST['ReminderTime'] ?? '';
    $isCompleted = 'Active'; // Default value

    if (empty($patientID) || empty($title) || empty($description) || empty($genericName) || empty($reminderDate) || empty($reminderTime)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO reminder (PatientID, Title, Description, GenericName, ReminderDate, ReminderTime, isCompleted) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Server error: " . $conn->error]);
        return;
    }

    $stmt->bind_param("sssssss", $patientID, $title, $description, $genericName, $reminderDate, $reminderTime, $isCompleted);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Reminder added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add reminder"]);
    }

    $stmt->close();
}


$conn->close();
?>