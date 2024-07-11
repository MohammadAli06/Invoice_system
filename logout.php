<?php
// Initialize the session
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login page or any other page after logout
header("location: ../INVOICE_SYSTEM_FINAL/index.php");
exit;
?>
