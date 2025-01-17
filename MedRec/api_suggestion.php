<?php
require_once 'db_connection.php'; // Include your db connection

header("Content-Type: application/json");

$query = $_GET['query'] ?? ''; // Get the query parameter from the URL

if ($query) {
    // Prepare SQL query to fetch suggestions (medicine names)
    $sql = "SELECT DISTINCT BrandName FROM drug_details WHERE BrandName LIKE ? OR GenericName LIKE ? LIMIT 10";
    if ($stmt = $conn->prepare($sql)) {
        $param = "%" . $query . "%"; // Bind parameter with wildcards for LIKE query
        $stmt->bind_param("ss", $param, $param); // Bind query parameters

        $stmt->execute();

        // Bind result variables
        $stmt->bind_result($brandName);

        // Initialize an empty array for storing suggestions
        $suggestions = [];
        while ($stmt->fetch()) {
            $suggestions[] = $brandName; // Add each suggestion to the array
        }

        // Return the suggestions in JSON format
        echo json_encode($suggestions);

        $stmt->close(); // Close the statement
    } else {
        echo json_encode(["error" => "SQL preparation failed"]);
    }
} else {
    echo json_encode([]); // Return an empty array if no query parameter is provided
}

$conn->close(); // Close the database connection
?>
