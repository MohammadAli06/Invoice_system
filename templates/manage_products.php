<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: ../index.php');
    exit();
}

// Fetch products from the database
$sql = "SELECT id, name, description, price FROM products";
$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    min-height: 100vh;
    background-color: #f8f9fa;
}
.content {
    flex: 1;
    padding: 20px;
    margin-top: 0; /* Adjusted to start from the top */
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    position: relative; /* Added to use absolute positioning */
}
h1 {
    text-align: left; /* Align title to the left */
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
table, th, td {
    border: 1px solid #ccc;
}
th {
    background-color: #495057;
    color: white;
    text-align: left;
    padding: 10px;
}
td {
    padding: 10px;
    text-align: left;
}
.search-container {
    margin-bottom: 20px;
    text-align: left; /* Align search container to the left */
}
.search-container input[type=text] {
    padding: 10px;
    margin-right: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    width: calc(100% - 120px); /* Adjust width to fit the search bar and button */
    background-color: #f2f2f2;
}
.search-container button {
    padding: 10px 20px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.search-container button:hover {
    background-color: #218838;
}
.action-buttons {
    display: flex;
    gap: 10px;
}
.action-buttons a button {
    border: none; /* Removed border */
    background-color: transparent;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    transition: color 0.3s ease;
}
.action-buttons a button.edit::before {
    content: "\f044"; /* Edit icon */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    color: #ffc107;
}
.action-buttons a button.delete::before {
    content: "\f2ed"; /* Trash icon */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    color: #dc3545;
}
.action-buttons a button.edit:hover {
    color: #e0a800; /* Darker yellow on hover */
}
.action-buttons a button.delete:hover {
    color: #c82333; /* Darker red on hover */
}

    </style>
</head>
<body>
<?php
include('../includes/sidebar.php');
?>

    <div class="content">
        <h1>Product List</h1>

        <div class="search-container">
            <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search by Product Name...">
            <button type="button" onclick="searchTable()">Search</button>
        </div>

        <table id="productTable">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Product Description</th>
                    <th>Product Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                    <td class="action-buttons">
                        <a href="../func/edit_prod.php?id=<?php echo $product['id']; ?>" title="Edit"><button class="edit"></button></a>
                        <a href="../func/delete_prod.php?id=<?php echo $product['id']; ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this product?');"><button class="delete"></button></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    </div>

    <script>
        function toggleDropdown(dropdownId) {
            var dropdownContainer = document.getElementById(dropdownId + 'Dropdown');
            if (dropdownContainer.style.display === 'block') {
                dropdownContainer.style.display = 'none';
            } else {
                dropdownContainer.style.display = 'block';
            }
        }

        function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("productTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0]; // Search by product name (first column)
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
    </script>
</body>
</html>
