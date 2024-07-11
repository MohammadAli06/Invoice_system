<?php
include('../includes/db.php');
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

// Handle deletion of user
if (isset($_GET['delete'])) {
    $userIdToDelete = $_GET['delete'];

    // Prepare a statement to delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userIdToDelete);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_users.php");
    exit();
}

// Fetch all users from the database
$stmt = $conn->prepare("SELECT id, username, email, role FROM users");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .content {
    
            padding: 20px;
            width: calc(100% - 250px); /* Adjusting width to accommodate sidebar */
            box-sizing: border-box; /* Ensures padding is included in the width calculation */
        }
        
        h1 {
            text-align: left; /* Align header to the left */
            margin-bottom: 20px; /* Add margin below header */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px; /* Adjusted margin top */
            border: 1px solid #ccc; /* Add border */
        }
        th, td {
            border: 1px solid #ccc; /* Add border to table cells */
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #495057; /* Dark grey background color */
            color: white; /* White text color */
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons a, .action-buttons form button {
            display: inline-block;
            padding: 5px 10px;
            border: none;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .action-buttons form {
            display: inline-block;
            margin: 0;
        }
        .action-buttons form button {
            background-color: #dc3545;
        }
        .search-container {
            display: flex;
            justify-content: flex-start; /* Align search to the left */
            margin-bottom: 20px;
            margin-left: 0px; /* Adjust left margin */
        }
        .search-container input[type=text] {
            padding: 10px;
            margin-right: 10px;
            border: none; /* Remove border */
            border-radius: 4px;
            font-size: 14px;
            width: calc(100% - 120px);
            background-color: #f2f2f2; /* Light grey background color */
        }
        .search-container button {
            padding: 10px 15px;
            background-color: #28a745; /* Green color for button */
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
        <h1>User List</h1>
        <div class="container">
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search by name...">
                <button type="button" onclick="searchTable()">Search</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="../func/edit_user.php?id=<?php echo $user['id']; ?>">&#9998;</a> <!-- Edit symbol -->
                                    <form action="../func/delete_user.php" method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <button type="submit">&#128465;</button> <!-- Delete symbol -->
                        </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        // JavaScript function to search table rows by name
        function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.querySelector("table");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0]; // Search by name (first column)
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

        // JavaScript to toggle dropdowns in sidebar
        document.addEventListener("DOMContentLoaded", function() {
            var dropdownBtns = document.querySelectorAll(".dropdown-btn");
            dropdownBtns.forEach(function(btn) {
                btn.addEventListener("click", function() {
                    this.classList.toggle("active");
                    var dropdownContent = this.nextElementSibling;
                    if (dropdownContent.style.display === "block") {
                        dropdownContent.style.display = "none";
                    } else {
                        dropdownContent.style.display = "block";
                    }
                });
            });
        });
    </script>
</body>
</html>
