<?php
require_once 'db_connection.php'; // Include the connection file

$sql = "SELECT * FROM user"; // Example query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data); // Return data as JSON
} else {
    echo json_encode(["status" => "error", "message" => "No users found"]);
}

$conn->close();
?>
