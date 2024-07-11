<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['id'])) {
    $invoiceId = $_GET['id'];

    // Fetch the invoice details
    $sql = "SELECT * FROM invoice WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $invoiceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoice = $result->fetch_assoc();

    // Fetch the customer details associated with the invoice
    $customerSql = "SELECT * FROM customers WHERE id = ?";
    $customerStmt = $conn->prepare($customerSql);
    $customerStmt->bind_param('i', $invoice['customer_id']);
    $customerStmt->execute();
    $customerResult = $customerStmt->get_result();
    $customer = $customerResult->fetch_assoc();

    // Fetch the products
    $productQuery = "SELECT id, name, price FROM products";
    $productResult = $conn->query($productQuery);

    // Initialize an empty array to store products
    $products = [];

    // Check if there are any results
    if ($productResult->num_rows > 0) {
        while ($row = $productResult->fetch_assoc()) {
            $products[] = $row;
        }
    }

    
$invoiceProductsQuery = "SELECT product_id FROM invoice_items WHERE invoice_id = ?";
$invoiceProductsStmt = $conn->prepare($invoiceProductsQuery);
$invoiceProductsStmt->bind_param('i', $invoiceId);
$invoiceProductsStmt->execute();
$invoiceProductsResult = $invoiceProductsStmt->get_result();

$invoiceProducts = [];
if ($invoiceProductsResult->num_rows > 0) {
    while ($row = $invoiceProductsResult->fetch_assoc()) {
        $invoiceProducts[] = $row['product_id'];
    }
}

// Query to fetch quantities from invoice_items for a specific invoice
$query = "SELECT product_id, quantity, price, discount FROM invoice_items WHERE invoice_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $invoiceId);
$stmt->execute();
$result = $stmt->get_result();

// Arrays to hold quantities, prices, and discounts
$quantities = [];
$prices = [];
$discounts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $quantities[$row['product_id']] = $row['quantity'];
        $prices[$row['product_id']] = $row['price'];
        $discounts[$row['product_id']] = $row['discount'];
    }
}

// Handle form submission to update the invoice
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $invoice_type = $_POST['invoice_type'];
    $invoice_status = $_POST['invoice_status'];
    $invoice_date = $_POST['invoice_date'];
    $due_date = $_POST['due_date'];
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_address = $_POST['customer_address'];
    $customer_town = $_POST['customer_town'];
    $customer_country = $_POST['customer_country'];
    $customer_postcode = $_POST['customer_postcode'];
    $customer_phone = $_POST['customer_phone'];
    $shipping_name = $_POST['shipping_name'];
    $shipping_address = $_POST['shipping_address'];
    $shipping_town = $_POST['shipping_town'];
    $shipping_country = $_POST['shipping_country'];
    $shipping_postcode = $_POST['shipping_postcode'];
    $products = $_POST['products'];
    $quantities = $_POST['quantity'];
    $prices = $_POST['price'];
    $discounts = $_POST['discount'];
    $additional_notes = $_POST['additional_notes'];
    $total = $_POST['total'];
    $bill_discount = $_POST['bill_discount'];
    $shipping_charges = $_POST['shipping_charges'];
    $tax = $_POST['tax'];
    $subtotal = $_POST['subtotal'];

    // Prepare JSON arrays for products, quantities, prices, and discounts
    $products_json = json_encode($products);
    $quantities_json = json_encode($quantities);
    $prices_json = json_encode($prices);
    $discounts_json = json_encode($discounts);


    // Update customer details in `customers` table
    $sqlCustomer = "UPDATE customers SET name = ?, email = ?, address = ?, town = ?, country = ?, postcode = ? WHERE id = ?";
    $stmtCustomer = $conn->prepare($sqlCustomer);
    $stmtCustomer->bind_param('ssssssi', $customer_name, $customer_email, $customer_address, $customer_town, $customer_country, $customer_postcode, $invoice['customer_id']);
    $stmtCustomer->execute();
    $stmtCustomer->close();

// Insert updated invoice items
$products = $_POST['products'];
$quantities = $_POST['quantity'];
$prices = $_POST['price'];
$discounts = $_POST['discount'];

$sqlItemsInsert = "INSERT INTO invoice_items (invoice_id, product_id, quantity, price, discount) VALUES (?, ?, ?, ?, ?)";
$stmtItemsInsert = $conn->prepare($sqlItemsInsert);
$stmtItemsInsert->bind_param('iiidd', $invoiceId, $productId, $quantity, $price, $discount);

foreach ($products as $productId) {
    $quantity = $quantities[$productId];
    $price = $prices[$productId];
    $discount = $discounts[$productId];
    $stmtItemsInsert->execute();
}

$stmtItemsInsert->close();

$sqlInvoiceUpdate = "UPDATE invoices SET invoice_type = ?, invoice_status = ?, invoice_date = ?, due_date = ?, additional_notes = ?, total = ?, bill_discount = ?, shipping_charges = ?, tax = ?, subtotal = ? WHERE id = ?";
$stmtInvoiceUpdate = $conn->prepare($sqlInvoiceUpdate);
$stmtInvoiceUpdate->bind_param('ssssssdddi', $invoice_type, $invoice_status, $invoice_date, $due_date, $additional_notes, $total, $bill_discount, $shipping_charges, $tax, $subtotal, $invoiceId);
$stmtInvoiceUpdate->execute();
$stmtInvoiceUpdate->close();

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to manage invoices page after successful update
        header('Location: ../templates/manage_invoices.php');
        exit();
    } else {
        // Handle error
        echo "Error: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
} else {
    // Redirect to manage invoices page if no id provided
    header('Location: ../templates/manage_invoices.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Invoice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f4f4f4;
}

form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    margin: 20px;
}

h1 {
    text-align: center;

}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input[type="text"],
input[type="date"],
input[type="email"],
textarea, 
select {
    width: calc(100% - 24px);
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

textarea {
    resize: vertical;
    height: 100px;
}

button {
    display: block;
    width: 100%;
    padding: 10px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    margin-top: 20px;
}

button:hover {
    background-color: #218838;
}
   
    </style>
</head>
<body>
    <form action="edit_invoice.php" method="POST">
        <h1>Edit Invoice</h1>
        <label for="invoice_type">Invoice Type:</label>
        <input type="text" id="invoice_type" name="invoice_type" value="<?php echo $invoice['invoice_type']; ?>" required><br>
        <label for="invoice_status">Invoice Status:</label>
<select id="invoice_status" name="invoice_status" required>
    <option value="Paid" <?php echo ($invoice['invoice_status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
    <option value="Pending" <?php echo ($invoice['invoice_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
</select><br>

        <label for="invoice_date">Invoice Date:</label>
        <input type="date" id="invoice_date" name="invoice_date" value="<?php echo $invoice['invoice_date']; ?>" required><br>

        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" value="<?php echo $invoice['due_date']; ?>" required><br>

        <label for="customer_name">Customer Name:</label>
        <input type="text" id="customer_name" name="customer_name" value="<?php echo  $customer['name']; ?>" required><br>

        <label for="customer_email">Customer Email:</label>
        <input type="email" id="customer_email" name="customer_email" value="<?php echo  $customer['email']; ?>" required><br>

        <label for="customer_address">Customer Address:</label>
        <input type="text" id="customer_address" name="customer_address" value="<?php echo  $customer['address']; ?>" required><br>

        <label for="customer_town">Customer Town:</label>
        <input type="text" id="customer_town" name="customer_town" value="<?php echo  $customer['town']; ?>" required><br>

        <label for="customer_country">Customer Country:</label>
        <input type="text" id="customer_country" name="customer_country" value="<?php echo  $customer['country']; ?>" required><br>

        <label for="customer_postcode">Customer Postcode:</label>
        <input type="text" id="customer_postcode" name="customer_postcode" value="<?php echo $customer['postcode']; ?>" required><br>

        <label for="customer_phone">Customer Phone:</label>
        <input type="text" id="customer_phone" name="customer_phone" value="<?php echo  $customer['phone']; ?>" required><br>

        <label for="shipping_name">Shipping Name:</label>
        <input type="text" id="shipping_name" name="shipping_name" value="<?php echo $invoice['shipping_name']; ?>" required><br>

        <label for="shipping_address">Shipping Address:</label>
        <input type="text" id="shipping_address" name="shipping_address" value="<?php echo $invoice['shipping_address']; ?>" required><br>

        <label for="shipping_town">Shipping Town:</label>
        <input type="text" id="shipping_town" name="shipping_town" value="<?php echo $invoice['shipping_town']; ?>" required><br>

        <label for="shipping_country">Shipping Country:</label>
        <input type="text" id="shipping_country" name="shipping_country" value="<?php echo $invoice['shipping_country']; ?>" required><br>

        <label for="shipping_postcode">Shipping Postcode:</label>
        <input type="text" id="shipping_postcode" name="shipping_postcode" value="<?php echo $invoice['shipping_postcode']; ?>" required><br>

        <label for="products">Products:</label>
        <input type="text" id="products" name="products" value="<?php
    $productsText = '';
    foreach ($products as $product) {
        if (in_array($product['id'], $invoiceProducts)) {
            $productsText .= $product['name'] . ' - $' . number_format($product['price'], 2) . ', ';
        }
    }
    echo rtrim($productsText, ', ');
?>" readonly><br>

        <label for="quantities">Quantities:</label>
        <?php foreach ($products as $product) : ?>
            <input type="text" name="quantity[<?php echo $product['id']; ?>]" value="<?php echo isset($quantities[$product['id']]) ? htmlspecialchars($quantities[$product['id']]) : ''; ?>"><br>
        <?php endforeach; ?>

        <label for="prices">Prices:</label>
        <?php foreach ($products as $product) : ?>
            <input type="text" name="price[<?php echo $product['id']; ?>]" value="<?php echo isset($prices[$product['id']]) ? htmlspecialchars($prices[$product['id']]) : ''; ?>"><br>
        <?php endforeach; ?>

        <label for="discounts">Discounts:</label>
        <?php foreach ($products as $product) : ?>
            <input type="text" name="discount[<?php echo $product['id']; ?>]" value="<?php echo isset($discounts[$product['id']]) ? htmlspecialchars($discounts[$product['id']]) : ''; ?>"><br>
        <?php endforeach; ?>

        <label for="additional_notes">Additional Notes:</label>
        <textarea id="additional_notes" name="additional_notes" ><?php echo $invoice['additional_notes']; ?></textarea><br>

        <label for="total">Total:</label>
        <input type="text" id="total" name="total" value="<?php echo $invoice['total']; ?>" required><br>

        <label for="bill_discount">Bill Discount:</label>
        <input type="text" id="bill_discount" name="bill_discount" value="<?php echo $invoice['bill_discount']; ?>" required><br>

        <label for="shipping_charges">Shipping Charges:</label>
        <input type="text" id="shipping_charges" name="shipping_charges" value="<?php echo $invoice['shipping_charges']; ?>" required><br>

        <label for="tax">Tax:</label>
        <input type="text" id="tax" name="tax" value="<?php echo $invoice['tax']; ?>" required><br>

        <label for="subtotal">Subtotal:</label>
        <input type="text" id="subtotal" name="subtotal" value="<?php echo $invoice['subtotal']; ?>" required><br>

        <button type="submit">Update Invoice</button>
    </form>
</body>
</html>
