<?php
// Include database connection
include('../includes/db.php');

// Check if invoice ID is provided via GET or POST
if (isset($_GET['id'])) {
    $invoice_id = $_GET['id'];

    // Delete related invoice items first
    $sql_delete_items = "DELETE FROM invoice_items WHERE invoice_id = ?";
    $stmt_items = $conn->prepare($sql_delete_items);
    if ($stmt_items) {
        $stmt_items->bind_param("s", $invoice_id); // Assuming invoice_id is string type
        $stmt_items->execute();
        $stmt_items->close();

        // Now delete the invoice
        $sql_delete_invoice = "DELETE FROM invoice WHERE id = ?";
        $stmt_invoice = $conn->prepare($sql_delete_invoice);
        if ($stmt_invoice) {
            $stmt_invoice->bind_param("s", $invoice_id);
            $stmt_invoice->execute();

            // Check if deletion was successful
            if ($stmt_invoice->affected_rows > 0) {
                echo "Invoice deleted successfully.";
            } else {
                echo "No invoice found with ID: " . $invoice_id;
            }

            $stmt_invoice->close();
        } else {
            echo "Error in preparing statement: " . $conn->error;
        }
    } else {
        echo "Error in preparing statement: " . $conn->error;
    }

    // Close database connection
    $conn->close();
} else {
    echo "No invoice ID provided.";
}
?>
