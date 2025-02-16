<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

// Fetch customers from the database
$sql = "SELECT * FROM customers";
$result = $conn->query($sql);

$customers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        
        }

        .container {
            max-width: 1200px; /* Increased max-width for the container */
            margin: 0px auto; /* Adjusted margin for top spacing */
            padding: 20px;
        }

        h1 {
            text-align: left; /* Align header text to the left */
            color: black; /* Darker grey color for header */
            margin-top: 0; /* Remove default top margin */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px; /* Increased padding for better spacing */
            text-align: left;
            border: 1px solid #ddd;
            position: relative; /* Ensure relative positioning for ::after pseudo-element */
        }

        th {
            background-color: #495057; /* Dark grey color for column headers */
            color: white; /* White text color */
        }

        th::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid #495057; /* Triangle shape for highlight */
        }

        .actions {
            text-align: center;
        }

        .actions a {
            text-decoration: none;
            padding: 5px 10px;
            margin-right: 5px;
            color: inherit; /* Inherit text color */
        }

        .edit {
            background-color: #007bff;
            color: white;
            padding: 5px;
            border-radius: 3px;
        }

        .delete {
            background-color: #dc3545;
            color: white;
            padding: 5px;
            border-radius: 3px;
        }

        .search-container {
            margin-bottom: 20px;
            text-align: left; /* Align search bar and button to the left */
            display: flex;
        }

        .search-container input[type=text] {
            padding: 10px;
            margin-right: 10px;
            border: 0px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            width: 1050px; /* Increased width for search input */
            background-color: #f2f2f2;
        }

        .search-container button {
            padding: 10px 15px;
            background-color: #28a745; /* Green color */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
    </style>
</head>
<body>
<?php
include('../includes/sidebar.php');
?>
    <div class="content">
        <div class="container">
            <h1>Customer List</h1>

            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search for names...">
                <button type="button" onclick="searchTable()">Search</button>
            </div>

            <table id="customerTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr id="customer<?php echo $customer['id']; ?>">
                            <td><?php echo $customer['name']; ?></td>
                            <td><?php echo $customer['email']; ?></td>
                            <td><?php echo $customer['phone']; ?></td>
                            <td class="actions">
                                <a href="../func/edit_customer.php?id=<?php echo $customer['id']; ?>" class="edit">&#9998;</a>
                                <a href="javascript:void(0);" onclick="deleteCustomer(<?php echo $customer['id']; ?>)" class="delete">&#128465;</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dropdowns = document.getElementsByClassName("dropdown-btn");
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].addEventListener("click", function () {
                    var dropdownContent = this.nextElementSibling;
                    if (dropdownContent.style.display === "block") {
                        dropdownContent.style.display = "none";
                    } else {
                        dropdownContent.style.display = "block";
                    }
                });
            }
        });

        function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("customerTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0]; // Search by customer name (first column)
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

     function deleteCustomer(customerId) {
        if (confirm("Are you sure you want to delete this customer?")) {
            // Implement your deletion logic here
            window.location.href = '../func/delete_customer.php?id=' + customerId;
        }
    }

    </script>
</body>
</html>
