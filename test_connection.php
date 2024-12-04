<?php
require_once 'db_connection.php'; // Include the connection file

// Check if the connection is successful
if ($conn) {
    echo json_encode(["status" => "success", "message" => "Database connected successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
}

// Close the connection (optional)
$conn->close();
?>
