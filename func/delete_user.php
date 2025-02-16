<?php
include('../includes/db.php');
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

// Get the user ID from the POST request
if (!isset($_POST['id']) || !filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    die("Invalid user ID.");
}

$userId = intval($_POST['id']);

// Prepare and execute the deletion query
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->bind_param("i", $userId);
if ($stmt->execute() === false) {
    die("Error executing the statement: " . $stmt->error);
}

$stmt->close();

// Redirect back to the manage_users.php page
header("Location: ../templates/manage_users.php");
exit();
?>
