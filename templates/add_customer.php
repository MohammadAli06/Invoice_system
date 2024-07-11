<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Customer information
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_address = $_POST['customer_address'];
    $customer_town = $_POST['customer_town'];
    $customer_country = $_POST['customer_country'];
    $customer_postcode = $_POST['customer_postcode'];
    $customer_phone = $_POST['customer_phone'];

    // Insert customer information into `customers` table
    $stmt_customer = $conn->prepare("INSERT INTO customers (name, email, address, town, country, postcode, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_customer->bind_param("sssssss", $customer_name, $customer_email, $customer_address, $customer_town, $customer_country, $customer_postcode, $customer_phone);

    // Execute customer insertion
    if ($stmt_customer->execute()) {
        header('Location: manage_customers.php');
        exit();
    } else {
        echo "Error inserting customer information: " . $stmt_customer->error;
    }

    // Close statement and connection
    $stmt_customer->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .section {
            width: 1050px;
            justify-content: center;
            align-items: center;
            height: 650px;
            margin-left: 150px;            
            border: 1px solid #ddd;
            padding: 30px;
            padding-left: 70px;
            border-radius: 10px;
            background-color: #343a40;
            color: white;
            margin-top: 20px;
        }
        h1 {
            text-align: center;
        }
        form {
            margin-top: 20px;
        }
        .section h3 {
            margin-top: 0;
        }
        .row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .row > div {
            flex: 1;
            min-width: 200px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: white;
        }
        input[type="text"],
        input[type="email"],
        input[type="number"],
        select,
        textarea {
            width: 95%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
        }
        textarea {
            height: 100px;
        }
        .submit-section {
            text-align: center;
        }
        .submit-section button {
            padding: 15px 30px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .highlighted {
            background-color: #28a745;
        }
        .readonly {
            background-color: #f1f1f1;
            pointer-events: none;
        }
        .gap {
            margin-top: 6vh;
        }
        #shipping_postcode,
        #customer_phone {
            width: 97.5%;
        }
    </style>
</head>
<body>
<?php
include('../includes/sidebar.php');
?>
<div class="center">
    <div class="section">
        <h1>Add Customer</h1>
        <form action="add_customer.php" method="post">
            <div class="gap">
                <h3>Customer Information</h3>
                <div class="row">
                    <div>
                        <label for="customer_name">Name</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    <div>
                        <label for="customer_email">Email</label>
                        <input type="email" id="customer_email" name="customer_email" required>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="customer_address">Address</label>
                        <input type="text" id="customer_address" name="customer_address" required>
                    </div>
                    <div>
                        <label for="customer_town">Town</label>
                        <input type="text" id="customer_town" name="customer_town" required>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="customer_country">Country</label>
                        <input type="text" id="customer_country" name="customer_country" required>
                    </div>
                    <div>
                        <label for="customer_postcode">Postcode</label>
                        <input type="text" id="customer_postcode" name="customer_postcode" required>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="customer_phone">Phone Number</label>
                        <input type="text" id="customer_phone" name="customer_phone" required>
                    </div>
                </div>
            </div>
            <div class="gap submit-section">
                <button type="submit" class="highlighted">Create Customer</button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const customerName = document.getElementById('customer_name');
        const customerAddress = document.getElementById('customer_address');
        const customerTown = document.getElementById('customer_town');
        const customerCountry = document.getElementById('customer_country');
        const customerPostcode = document.getElementById('customer_postcode');

        const shippingName = document.getElementById('shipping_name');
        const shippingAddress = document.getElementById('shipping_address');
        const shippingTown = document.getElementById('shipping_town');
        const shippingCountry = document.getElementById('shipping_country');
        const shippingPostcode = document.getElementById('shipping_postcode');

        // Auto-increment shipping information from customer information
        customerName.addEventListener('input', function () {
            shippingName.value = this.value;
        });

        customerAddress.addEventListener('input', function () {
            shippingAddress.value = this.value;
        });

        customerTown.addEventListener('input', function () {
            shippingTown.value = this.value;
        });

        customerCountry.addEventListener('input', function () {
            shippingCountry.value = this.value;
        });

        customerPostcode.addEventListener('input', function () {
            shippingPostcode.value = this.value;
        });

        // Disabling auto-incrementation for readonly fields
        const readonlyFields = document.querySelectorAll('.readonly');
        readonlyFields.forEach(field => {
            field.addEventListener('input', function () {
                // Prevent editing readonly fields
                this.value = this.defaultValue;
            });
        });
    });
</script>
</body>
</html>
