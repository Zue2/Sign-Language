<?php
require 'D:\Programming\Sign-Language\utils\db.php'; // Ensure this file connects to your MySQL database

if (isset($_GET['username'])) {  // Get username from AJAX request
    $username = $_GET['username'];

    // Query the database for this username
    $stmt = $conn->prepare("SELECT username, profile FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {  // If a match is found in the database
        // Return user data as JSON, including username and profile image path
        echo json_encode([
            'username' => $row['username'],
            'profile' => $row['profile']
        ]);
    } else {
        echo json_encode(["error" => "User not found"]);  // No match found
    }
}
$stmt->close();
$conn->close();
?>
