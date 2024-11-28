<?php

require_once 'db_connection.php'; // Include the connection file

// Get POST data from the request
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// SQL query to insert the new user
$sql = "INSERT INTO user (`Username`, `Email`, `Password`) VALUES ('$full_name', '$email', '$password')";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the connection
$conn->close();

?>
