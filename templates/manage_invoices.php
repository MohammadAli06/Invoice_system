<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: ../index.php');
    exit();
}

// Initialize search result variable
$search_result = [];

// Handle search form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search_term = $conn->real_escape_string($_POST['search']);
    $sql = "SELECT i.id, i.customer_id, c.name AS customer_name, i.invoice_date, i.due_date, i.invoice_type, i.invoice_status, c.email, 
                   GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ', ') AS product_names
            FROM invoice i
            JOIN customers c ON i.customer_id = c.id
            LEFT JOIN invoice_items ii ON i.id = ii.invoice_id
            LEFT JOIN products p ON ii.product_id = p.id
            WHERE c.name LIKE '%$search_term%'
            GROUP BY i.id";
} else {
    $sql = "SELECT i.id, i.customer_id, c.name AS customer_name, i.invoice_date, i.due_date, i.invoice_type, i.invoice_status, c.email, 
                   GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ', ') AS product_names
            FROM invoice i
            JOIN customers c ON i.customer_id = c.id
            LEFT JOIN invoice_items ii ON i.id = ii.invoice_id
            LEFT JOIN products p ON ii.product_id = p.id
            GROUP BY i.id";
}

$result = $conn->query($sql);

// Check for query error
if (!$result) {
    die("Query failed: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $search_result[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Invoices</title>
    <!-- Include Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap');

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .box {
            padding: 20px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .box h3 {
            margin: 0 0 10px 0;
        }

        .dropdown-btn {
            border: none;
            background: none;
            padding: 10px 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd; /* Thinner border */
            text-align: left;
        }

        th, td {
            padding: 10px;
        }

        th {
            background-color: #495057;
            color: white;
        }

        .search-container {
            margin-bottom: 20px;
            overflow: hidden;
            text-align: right;
        }

        .search-container input[type=text] {
            padding: 10px;
            margin-top: 10px;
            font-size: 17px;
            border: none; /* No border */
            width: 85%; /* Adjusted width */
            background: #f1f1f1;
            float: left;
            border-radius: 5px; /* Rounded shape */
        }

        .search-container button {
            float: left;
            width: 10%;
            padding: 10px;
            margin-top: 10px;
            margin-left: 10px; /* Space between button and search bar */
            background: #28a745;
            color: white;
            font-size: 14px; /* Smaller font size */
            border: none; /* No border */
            cursor: pointer;
            border-radius: 5px; /* Rounded shape */
        }

        .search-container button:hover {
            background: #218838;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            border: 2px solid transparent; /* Bolder border */
            background-color: transparent;
            font-size: 18px;
            cursor: pointer;
            padding: 5px;
        }

        .action-buttons button::before {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
        }

        .action-buttons button.edit::before {
            content: "\f044"; /* Edit icon */
            color: #ffc107;
        }

        .action-buttons button.download::before {
            content: "\f019"; /* Download icon */
            color: #007bff;
        }

        .action-buttons button.email::before {
            content: "\f0e0"; /* Email icon */
            color: #28a745;
        }

        .action-buttons button.delete::before {
            content: "\f2ed"; /* Trash icon */
            color: #dc3545;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dropdowns = document.getElementsByClassName("dropdown-btn");
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].addEventListener("click", function () {
                    this.classList.toggle("active");
                    var dropdownContent = this.nextElementSibling;
                    if (dropdownContent.style.display === "block") {
                        dropdownContent.style.display = "none";
                    } else {
                        dropdownContent.style.display = "block";
                    }
                });
            }
        });
    </script>
</head>
<body>
<?php
include('../includes/sidebar.php');
?>
   
    <div class="content">
        <h1>Invoice List</h1>

        <!-- Search bar -->
        <div class="search-container">
            <form action="" method="post">
                <input type="text" placeholder="Search by Customer Name" name="search">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Invoice table -->
        <table>
            <tr>
                <th>Invoice Number</th>
                <th>Product Name</th>
                <th>Customer Name</th>
                <th>Invoice Date</th>
                <th>Due Date</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($search_result as $invoice) : ?>
                <tr>
                    <td><?php echo $invoice['id']; ?></td>
                    <td><?php echo $invoice['product_names']; ?></td>
                    <td><?php echo $invoice['customer_name']; ?></td>
                    <td><?php echo $invoice['invoice_date']; ?></td>
                    <td><?php echo $invoice['due_date']; ?></td>
                    <td><?php echo $invoice['invoice_type']; ?></td>
                    <td><?php echo $invoice['invoice_status']; ?></td>
                    <td class="action-buttons">
                        <a href="../func/edit_invoice.php?id=<?php echo $invoice['id']; ?>" title="Edit"><button class="edit"></button></a>
                        <a href="../func/download.php?id=<?php echo $invoice['id']; ?>" title="Download"><button class="download"></button></a>
                        <a href="../func/send_email.php?customer_id=<?php echo $invoice['customer_id']; ?>&id=<?php echo $invoice['id']; ?>" title="Send Email"><button class="email"></button></a>
                        <a href="../func/delete_invoice.php?id=<?php echo $invoice['id']; ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this invoice?');"><button class="delete"></button></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
