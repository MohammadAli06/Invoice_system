<?php
session_start();
include('./includes/db.php');

if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: index.php');
    exit();
}

// Fetch data from database
$total_invoices_query = "SELECT COUNT(*) AS total_invoices FROM invoice";
$result = $conn->query($total_invoices_query);
$total_invoices = $result->fetch_assoc()['total_invoices'];

$pending_invoices_query = "SELECT COUNT(*) AS pending_invoices, SUM(subtotal) AS due_amount FROM invoice WHERE invoice_status = 'Open'";
$result = $conn->query($pending_invoices_query);
$pending_data = $result->fetch_assoc();
$pending_invoices = $pending_data['pending_invoices'];
$due_amount = $pending_data['due_amount'];

$sales_amount_query = "SELECT SUM(subtotal) AS sales_amount FROM invoice WHERE invoice_status = 'Paid'";
$result = $conn->query($sales_amount_query);
$sales_amount = $result->fetch_assoc()['sales_amount'];

$total_products_query = "SELECT COUNT(*) AS total_products FROM products";
$result = $conn->query($total_products_query);
$total_products = $result->fetch_assoc()['total_products'];

$total_customers_query = "SELECT COUNT(*) AS total_customers FROM customers";
$result = $conn->query($total_customers_query);
$total_customers = $result->fetch_assoc()['total_customers'];

$paid_bills_query = "SELECT COUNT(*) AS paid_bills FROM invoice WHERE invoice_status = 'Paid'";
$result = $conn->query($paid_bills_query);
$paid_bills = $result->fetch_assoc()['paid_bills'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap');

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            overflow-y: auto;
        }
        .content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .box {
            padding: 20px;
            margin: 10px 0;
            border: 2px solid #ddd; /* Bolder border */
            border-radius: 4px;
            font-family: 'Roboto', sans-serif; /* Stylish font */
            font-size: 20px; /* Larger font size */
        }

        .box h3 {
            margin: 0 0 10px 0;
            font-size: 24px; /* Larger font size */
        }

        .dropdown-btn {
            border: none;
            background: none;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
<?php
include('./includes/sidebar.php');
?>
    <div class="content">
        <h2>Welcome to the Invoice System, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <div class="box">
            <h3>Sales Amount</h3>
            <p>₹<?php echo $sales_amount; ?></p>
        </div>
        <div class="box">
            <h3>Total Invoices</h3>
            <p><?php echo $total_invoices; ?></p>
        </div>
        <div class="box">
            <h3>Pending Invoices</h3>
            <p><?php echo $pending_invoices; ?></p>
        </div>
        <div class="box">
            <h3>Due Amount</h3>
            <p>₹<?php echo $due_amount; ?></p>
        </div>
        <div class="box">
            <h3>Total Products</h3>
            <p><?php echo $total_products; ?></p>
        </div>
        <div class="box">
            <h3>Total Customers</h3>
            <p><?php echo $total_customers; ?></p>
        </div>
        <div class="box">
            <h3>Paid Bills</h3>
            <p><?php echo $paid_bills; ?></p>
        </div>
    </div>
</body>
</html>
