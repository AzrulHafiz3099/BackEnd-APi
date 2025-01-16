<?php
require_once 'db_connection.php'; // Include your db connection

header("Content-Type: application/json");

$name = $_GET['name'] ?? ''; // Get the name parameter from the URL

if ($name) {
    // Prepare SQL query to fetch the medicine details
    $sql = "SELECT * FROM drug_details WHERE BrandName LIKE ? OR GenericName LIKE ?";
    if ($stmt = $conn->prepare($sql)) {
        $param = "%" . $name . "%"; // Bind parameter with wildcards for LIKE query
        $stmt->bind_param("ss", $param, $param); // Bind query parameters

        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any result is returned
        if ($result->num_rows > 0) {
            $medicine = $result->fetch_assoc(); // Fetch the first matching result
            // Only return the necessary fields
            $response = [
                "BrandName" => $medicine['BrandName'],
                "GenericName" => $medicine['GenericName'],
                "Dosage" => $medicine['Dosage'],
                "Manufacturer" => $medicine['Manufacturer'],
                "SideEffects" => $medicine['SideEffects'],
                "DrugImage" => $medicine['DrugImage']
            ];
            echo json_encode($response); // Return the required fields as JSON
        } else {
            echo json_encode(["error" => "Medicine not found"]); // If no results, return error
        }

        $stmt->close(); // Close the statement
    } else {
        echo json_encode(["error" => "SQL preparation failed"]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}

$conn->close(); // Close the database connection
?>
