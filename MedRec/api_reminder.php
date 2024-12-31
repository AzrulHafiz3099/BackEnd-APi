<?php
require_once 'db_connection.php'; // Include the connection file

header('Content-Type: application/json');

// Retrieve the action from the request
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'addReminder':
        addReminder($conn);
        break;
    case 'updateReminder':
        updateReminder($conn);
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

    // Insert the reminder data
    $stmt = $conn->prepare("INSERT INTO reminder (PatientID, Title, Description, GenericName, ReminderDate, ReminderTime, isCompleted) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Server error: " . $conn->error]);
        return;
    }

    $stmt->bind_param("sssssss", $patientID, $title, $description, $genericName, $reminderDate, $reminderTime, $isCompleted);
    $stmt->execute();

    // Check if the insertion was successful
    if ($stmt->affected_rows > 0) {
        // Retrieve the last inserted ReminderID using LAST_INSERT_ID()
        $stmt = $conn->prepare("SELECT ReminderID FROM reminder ORDER BY ReminderID DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $reminderID = $row['ReminderID'];

        // Return the status and the newly inserted ReminderID
        echo json_encode(["status" => "success", "message" => "Reminder added successfully", "ReminderID" => $reminderID]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add reminder"]);
    }

    $stmt->close();
}


function updateReminder($conn){
    $patientID = $_POST['PatientID'] ?? '';
    $reminderID = $_POST['ReminderID'] ?? '';
    $isCompleted = 'Completed';

    // Corrected SQL query
    $stmt = $conn->prepare("UPDATE reminder SET isCompleted=? WHERE ReminderID=?");

    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Server error: " . $conn->error]);
        return;
    }

    // Bind the parameters correctly
    $stmt->bind_param("ss", $isCompleted, $reminderID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Reminder updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update reminder"]);
    }

    $stmt->close();
}



$conn->close();
?>