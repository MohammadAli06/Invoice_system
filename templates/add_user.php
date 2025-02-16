<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (email, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $username, $password, $role);

    if ($stmt->execute()) {
        header('Location: manage_users.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .containerr {
            width: 700px;
            margin-top: 35px;
            margin-left: 280px;
            overflow-y: auto;
            padding: 10px;
            padding-top: 25px;
            background-color: #343a40;
            color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input[type=text], 
        .form-group input[type=email],
        .form-group input[type=tel],
        .form-group input[type=password] {
            width: calc(100% - 8px); /* Adjusted width */
            padding: 10px;
            border: 3px solid #ccc; /* Increased border thickness */
            border-radius: 8px;
            font-size: 16px;
        }
        .radio-group {
        display: flex;
        align-items: center;
    }

    .radio-group input[type="radio"] {
        margin-right: 5px;
        width: auto;
    }

    .radio-group input[type="radio"] + label {
        margin-right: 50px;
        font-weight: normal;
    }
        .form-group input[type=submit] {
            display: block;
            margin: 0 auto; /* Center align the button */
            margin-top: 30px;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
        }
        
    </style>
</head>
<body>
<?php
include('../includes/sidebar.php');
?>
<div class="center">
    <div class="containerr">
        <h1>Add User</h1>
        <form action="add_user.php" method="post">
            <div class="form-group">
                <label for="email">User Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="username">Enter Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">User Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <label for="role">Role</label>
    <div class="radio-group">
        <input type="radio" id="role_admin" name="role" value="admin" required>
        <label for="role_admin">Admin</label>
        <input type="radio" id="role_user" name="role" value="user" required>
        <label for="role_user">User</label>
    </div>

            <div class="form-group">
                <input type="submit" value="Add User">
            </div>
        </form>
    </div>
</div>
    <script>
        // JavaScript to handle dropdown button functionality
        var dropdownBtns = document.querySelectorAll('.dropdown-btn');
        dropdownBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                this.classList.toggle('active');
                var dropdownContent = this.nextElementSibling;
                if (dropdownContent.style.display === 'block') {
                    dropdownContent.style.display = 'none';
                } else {
                    dropdownContent.style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>
