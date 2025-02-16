<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $customerId = $_GET['id'];

    // Step 1: Delete related invoices from invoice_items table (if exists)
    $stmt = $conn->prepare("DELETE FROM invoice_items WHERE invoice_id IN (SELECT id FROM invoice WHERE customer_id = ?)");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $stmt->close();

    // Step 2: Delete invoices associated with this customer
    $stmt = $conn->prepare("DELETE FROM invoice WHERE customer_id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $stmt->close();

    // Step 3: Now delete the customer
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
