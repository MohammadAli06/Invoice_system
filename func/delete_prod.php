<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: ../templates/manage_products.php');
        exit();
    } else {
        echo "Error deleting product";
    }
} else {
    echo "Invalid product ID";
}
?>
