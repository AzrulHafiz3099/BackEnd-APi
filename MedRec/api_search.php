<?php
require_once 'db_connection.php'; // Include your db connection

header("Content-Type: application/json");

$name = $_GET['name'] ?? ''; // Get the name parameter from the URL

if ($name) {
    // Prepare SQL query to fetch the medicine details
    $sql = "SELECT BrandName, GenericName, Dosage, Manufacturer, SideEffects, DrugImage 
            FROM drug_details 
            WHERE BrandName LIKE ? OR GenericName LIKE ? 
            LIMIT 1"; // Limit to 1 result
    if ($stmt = $conn->prepare($sql)) {
        $param = "%" . $name . "%"; // Bind parameter with wildcards for LIKE query
        $stmt->bind_param("ss", $param, $param); // Bind query parameters

        $stmt->execute();

        // Bind result variables
        $stmt->bind_result($brandName, $genericName, $dosage, $manufacturer, $sideEffects, $drugImage);

        if ($stmt->fetch()) {
            // Fetch the first matching result
            $response = [
                "BrandName" => $brandName,
                "GenericName" => $genericName,
                "Dosage" => $dosage,
                "Manufacturer" => $manufacturer,
                "SideEffects" => $sideEffects,
                "DrugImage" => $drugImage
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
