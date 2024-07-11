<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $customerId = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $customerId);

    if ($stmt->execute()) {
        header('Location: ../templates/manage_customers.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
