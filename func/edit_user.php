<?php
include('../includes/db.php');
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

// Get the user ID from the URL
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if user ID is valid
if ($userId <= 0) {
    die("Invalid user ID.");
}

// Fetch user details from the database
$sql = "SELECT username, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Check if user exists
if (!$user) {
    die("User not found.");
}

// If the form is submitted, update the user details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password']; // optional

    // Prepare and bind parameters to prevent SQL injection
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?");
    
    if ($stmt === false) {
        die("Error preparing the update statement: " . $conn->error);
    }

    $stmt->bind_param("ssssi", $username, $email, $role, $password, $userId);
    $stmt->execute();
    $stmt->close();

    header("Location: ../templates/manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 400px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            margin-bottom: 5px;
        }
        .form-group input[type=text], 
        .form-group input[type=email], 
        .form-group input[type=tel],
        .form-group input[type=password] {
            width: calc(100% - 12px); /* Adjusted width */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group input[type=submit] {
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group input[type=submit]:hover {
            background-color: #0056b3;
        }
        .radio-group {
            display: flex;
            justify-content: space-around;
        }
        .radio-group input[type=radio] {
            margin-right: 5px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Edit User</h1>
        <form action="edit_user.php?id=<?php echo $userId; ?>" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <div class="radio-group">
                    <div>
                        <input type="radio" id="role_admin" name="role" value="admin" <?php echo ($user['role'] === 'admin') ? 'checked' : ''; ?>>
                        <label for="role_admin">Admin</label>
                    </div>
                    <div>
                        <input type="radio" id="role_user" name="role" value="user" <?php echo ($user['role'] === 'user') ? 'checked' : ''; ?>>
                        <label for="role_user">User</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter new password">
            </div>
            <div class="form-group">
                <input type="submit" value="Update User">
            </div>
        </form>
    </div>
</body>
</html>
