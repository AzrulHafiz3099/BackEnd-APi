<?php
require_once 'db_connection.php'; // Include your database connection

header("Content-Type: application/json");

// Get the query parameter from the URL
$query = $_GET['query'] ?? '';

if (!empty($query)) {
    // Prepare SQL query to fetch distinct descriptions
    $sql = "SELECT DISTINCT Description FROM symptoms WHERE Description LIKE ? LIMIT 10";
    
    if ($stmt = $conn->prepare($sql)) {
        // Add wildcards for the LIKE query
        $param = "%" . $query . "%";
        $stmt->bind_param("s", $param); // Bind a single parameter

        // Execute the statement
        if ($stmt->execute()) {
            // Bind the result
            $stmt->bind_result($description);

            // Initialize an array for suggestions
            $suggestions = [];
            while ($stmt->fetch()) {
                $suggestions[] = $description; // Add each description to the array
            }

            // Return the suggestions as JSON
            echo json_encode($suggestions);
        } else {
            // Handle query execution error
            echo json_encode(["error" => "Query execution failed"]);
        }

        $stmt->close(); // Close the statement
    } else {
        // Handle query preparation error
        echo json_encode(["error" => "SQL preparation failed"]);
    }
} else {
    // Return an error if no query parameter is provided
    echo json_encode(["error" => "Query parameter is missing or empty"]);
}

// Close the database connection
$conn->close();
?>
