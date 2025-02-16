<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $customerId = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    } else {
        echo "Customer not found.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_address = $_POST['customer_address'];
    $customer_town = $_POST['customer_town'];
    $customer_country = $_POST['customer_country'];
    $customer_postcode = $_POST['customer_postcode'];
    $customer_phone = $_POST['customer_phone'];

    $stmt = $conn->prepare("UPDATE customers SET name = ?, email = ?, address = ?, town = ?, country = ?, postcode = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $customer_name, $customer_email, $customer_address, $customer_town, $customer_country, $customer_postcode, $customer_phone, $customerId);

    if ($stmt->execute()) {
        header('Location: ../templates/manage_customers.php');
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
    <title>Edit Customer</title>
    <style>
        body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
}

.content {
  margin-left: 200px; /* adjust to match the width of the sidebar */
  padding: 20px;
  overflow-y: auto;
}

.containerr {
  width: 800px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
  margin-top: 0;
  font-weight: bold;
  color: #333;
}

form {
  margin-top: 20px;
}

label {
  display: block;
  margin-bottom: 10px;
  font-weight: bold;
  color: #333;
}

input[type="text"], input[type="email"] {
  width: 100%;
  height: 40px;
  margin-bottom: 20px;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

input[type="text"]:focus, input[type="email"]:focus {
  border-color: #aaa;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

button[type="submit"] {
  background-color: #4CAF50;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

button[type="submit"]:hover {
  background-color: #3e8e41;
}
    </style>
</head>
<body>
    <?php include('../includes/sidebar.php'); ?>
    <div class="content">
        <div class="containerr">
            <h1 style="text-align: center;">Edit Customer</h1>
            <form action="edit_customer.php?id=<?php echo $customerId; ?>" method="POST">
                <label for="customer_name">Name:</label>
                <input type="text" id="customer_name" name="customer_name" value="<?php echo $customer['name']; ?>" required><br>
                
                <label for="customer_email">Email:</label>
                <input type="email" id="customer_email" name="customer_email" value="<?php echo $customer['email']; ?>" required><br>
                
                <label for="customer_address">Address:</label>
                <input type="text" id="customer_address" name="customer_address" value="<?php echo $customer['address']; ?>" required><br>
                
                <label for="customer_town">Town:</label>
                <input type="text" id="customer_town" name="customer_town" value="<?php echo $customer['town']; ?>" required><br>
                
                <label for="customer_country">Country:</label>
                <input type="text" id="customer_country" name="customer_country" value="<?php echo $customer['country']; ?>" required><br>
                
                <label for="customer_postcode">Postcode:</label>
                <input type="text" id="customer_postcode" name="customer_postcode" value="<?php echo $customer['postcode']; ?>" required><br>
                
                <label for="customer_phone">Phone:</label>
                <input type="text" id="customer_phone" name="customer_phone" value="<?php echo $customer['phone']; ?>" required><br>
                
                <button type="submit">Update Customer</button>
            </form>
        </div>
    </div>
</body>
</html>
