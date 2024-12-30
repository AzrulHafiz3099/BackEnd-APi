<?php
require_once 'db_connection.php'; // Include the connection file

header('Content-Type: application/json');

// Retrieve the action from the request
$action = $_POST['action'] ?? '';

// Log the action for debugging purposes
error_log("Received action: " . $action);

// Process the action
switch ($action) {
    case 'getPatients':
        $userID = $_POST['userID'] ?? '';
        error_log("UserID: " . $userID);
        getPatients($conn,$userID);
        break;

    case 'registerPatients':
        $userID = $_POST['userID'] ?? '';
        $name = $_POST['name'] ?? '';
        $age = $_POST['age'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $address = $_POST['address'] ?? '';
        $medicalHistory = $_POST['medicalHistory'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $symptomID = $_POST['symptomID'] ?? '';
        error_log("UserID: " . $userID);
        error_log("Name: " . $name);
        error_log("Age: " . $age);
        error_log("Gender: " . $gender);
        error_log("Address: " . $address);
        error_log("MedicalHistory: " . $medicalHistory);
        error_log("Phone: " . $phone);
        error_log("SymptomID: " . $symptomID);
        registerPatients($conn, $userID, $symptomID, $name, $age, $gender, $address, $medicalHistory, $phone);
        break;

    case 'deletePatient':
        $patientID = $_POST['patientID'] ?? '';
        error_log("PatientID to delete: " . $patientID);
        deletePatient($conn, $patientID);
        break;

    case 'getPatientsByID':
        $patientID = $_POST['patientID'] ?? '';
        error_log("PatientID: " . $patientID);
        fetchPatientByID($conn, $patientID);
        break;

    case 'updatePatient':
        $patientID = $_POST['patientID'] ?? '';
        $name = $_POST['name'] ?? '';
        $age = $_POST['age'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $address = $_POST['address'] ?? '';
        $medicalHistory = $_POST['medicalHistory'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $symptomID = $_POST['symptomID'] ?? '';
        
        updatePatient($conn, $patientID, $name, $age, $gender, $address, $medicalHistory, $phone, $symptomID);
        break;
        

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

function getPatients($conn, $userID) {
    $response = ["status" => "error", "message" => "No patients found"];

    if (!empty($userID)) {
        // Prepare the SQL query to fetch patients based on userID
        $query = "SELECT * FROM Patient WHERE UserID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $userID);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $patients = [];

                while ($row = $result->fetch_assoc()) {
                    $patients[] = $row; // Add each row to the patients array
                }

                $response = ["status" => "success", "patients" => $patients];
            } else {
                $response = ["status" => "error", "message" => "No patients found for this userID"];
            }
        } else {
            $response = ["status" => "error", "message" => "Query execution failed"];
        }

        $stmt->close();
    } else {
        $response = ["status" => "error", "message" => "UserID is required"];
    }

    echo json_encode($response);
}

function registerPatients($conn, $userID, $symptomID, $name, $age, $gender, $address, $medicalHistory, $phone) {
    $response = ["status" => "error", "message" => "Failed to register patient"];

    if (!empty($userID) && !empty($name) && !empty($age) && !empty($gender) && !empty($address) && !empty($medicalHistory) && !empty($phone) && !empty($symptomID)) {
        // Insert patient data along with the symptomID, set DrugHeaderID as NULL
        $query = "INSERT INTO Patient (UserID, SymptomID, `Name`, Age, Gender, `Address`, MedicalHistory, PhoneNumber, DrugHeaderID) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssisisss", $userID, $symptomID, $name, $age, $gender, $address, $medicalHistory, $phone, $drugHeaderID);

        // Set DrugHeaderID to NULL for this case
        $drugHeaderID = NULL;

        if ($stmt->execute()) {
            $response = ["status" => "success", "message" => "Patient registered successfully"];
        } else {
            $response = ["status" => "error", "message" => "Database insertion failed"];
        }

        $stmt->close();
    } else {
        $response = ["status" => "error", "message" => "All fields are required"];
    }

    echo json_encode($response);
}



function deletePatient($conn, $patientID) {
    $response = ["status" => "error", "message" => "Failed to delete patient"];

    if (!empty($patientID)) {
        // Prepare the SQL query to delete the patient
        $query = "DELETE FROM Patient WHERE PatientID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $patientID);

        if ($stmt->execute()) {
            $response = ["status" => "success", "message" => "Patient deleted successfully"];
        } else {
            $response = ["status" => "error", "message" => "Database deletion failed"];
        }

        $stmt->close();
    } else {
        $response = ["status" => "error", "message" => "PatientID is required"];
    }

    echo json_encode($response);
}

function fetchPatientByID($conn, $patientID) {
    $response = ["status" => "error", "message" => "Patient not found"];

    if (!empty($patientID)) {
        // Prepare the SQL query to fetch patient details by PatientID
        $query = "SELECT * FROM Patient WHERE PatientID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $patientID);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $patient = $result->fetch_assoc(); // Fetch the patient record as an associative array
                $response = ["status" => "success", "patient" => $patient];
            } else {
                $response = ["status" => "error", "message" => "No patient found with this PatientID"];
            }
        } else {
            $response = ["status" => "error", "message" => "Query execution failed"];
        }

        $stmt->close();
    } else {
        $response = ["status" => "error", "message" => "PatientID is required"];
    }

    echo json_encode($response);
}

function updatePatient($conn, $patientID, $name, $age, $gender, $address, $medicalHistory, $phone, $symptomID) {
    $response = ["status" => "error", "message" => "Failed to update patient"];

    if (!empty($patientID) && !empty($name) && !empty($age) && !empty($gender) && !empty($address) &&
        !empty($medicalHistory) && !empty($phone) && !empty($symptomID)) {
        
        // Update the patient data
        $query = "UPDATE Patient SET Name = ?, Age = ?, Gender = ?, Address = ?, MedicalHistory = ?, 
                  PhoneNumber = ?, SymptomID = ? WHERE PatientID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sissssss", $name, $age, $gender, $address, $medicalHistory, $phone, $symptomID, $patientID);

        if ($stmt->execute()) {
            $response = ["status" => "success", "message" => "Patient updated successfully"];
        } else {
            $response = ["status" => "error", "message" => "Database update failed"];
        }

        $stmt->close();
    } else {
        $response = ["status" => "error", "message" => "All fields are required"];
    }

    echo json_encode($response);
}


?>