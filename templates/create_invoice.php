<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: ../index.php');
    exit();
}

// Fetch products from the database
$sql = "SELECT id, name, price FROM products";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Generate a random invoice ID
function generateRandomInvoiceId($length = 10) {
    return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoiceId = generateRandomInvoiceId();
    
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
    $products = $_POST['product'];
    $quantities = $_POST['quantity'];
    $prices = $_POST['price'];
    $discounts = $_POST['discount'];
    $additional_notes = $_POST['additional_notes'];
    $total = $_POST['total'];
    $bill_discount = $_POST['bill_discount'];
    $shipping_charges = $_POST['shipping_charges'];
    $tax = $_POST['tax'];
    $subtotal = $_POST['subtotal'];

    // Insert customer data into the database
    // Check if customer already exists based on email
    $checkCustomerQuery = "SELECT id FROM customers WHERE email = ?";
    $checkStmt = $conn->prepare($checkCustomerQuery);
    $checkStmt->bind_param('s', $customer_email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // Customer already exists, retrieve their ID
        $checkStmt->bind_result($customerId);
        $checkStmt->fetch();
        $checkStmt->close();
    } else {
        // Customer doesn't exist, insert new customer data
        $insertCustomerQuery = "INSERT INTO customers (name, email, address, town, country, postcode, phone) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $customerStmt = $conn->prepare($insertCustomerQuery);
        $customerStmt->bind_param('sssssss', $customer_name, $customer_email, $customer_address, $customer_town, $customer_country, $customer_postcode, $customer_phone);
        
        if ($customerStmt->execute()) {
            $customerId = $conn->insert_id; // Retrieve the newly inserted customer ID
            $customerStmt->close();
        } else {
            // Handle error
            echo "Error inserting customer: " . $conn->error;
            exit();
        }
    }

    // Insert invoice data into the database
    $insertInvoiceQuery = "INSERT INTO invoice (id, invoice_type, invoice_status, invoice_date, due_date, customer_id, shipping_name, shipping_address, shipping_town, shipping_country, shipping_postcode, additional_notes, total, bill_discount, shipping_charges, tax, subtotal) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $invoiceStmt = $conn->prepare($insertInvoiceQuery);
    $invoiceStmt->bind_param('sssssissssssddddd', $invoiceId, $invoice_type, $invoice_status, $invoice_date, $due_date, $customerId, $shipping_name, $shipping_address, $shipping_town, $shipping_country, $shipping_postcode, $additional_notes, $total, $bill_discount, $shipping_charges, $tax, $subtotal);

    // Execute the invoice insertion
    if ($invoiceStmt->execute()) {
        // Insert each product as a line item
        foreach ($products as $index => $productId) {
            $quantity = $quantities[$index];
            $price = $prices[$index];
            $discount = $discounts[$index];
            
            $insertItemQuery = "INSERT INTO invoice_items (invoice_id, product_id, quantity, price, discount) VALUES (?, ?, ?, ?, ?)";
            $itemStmt = $conn->prepare($insertItemQuery);
            $itemStmt->bind_param('siidd', $invoiceId, $productId, $quantity, $price, $discount);
            $itemStmt->execute();
            $itemStmt->close();
        }

        // Redirect to a success page or manage invoices page
        header("Location: manage_invoices.php");
        exit();
    } else {
        // Handle error
        echo "Error inserting invoice: " . $conn->error;
    }

    // Close statement and connection
    $invoiceStmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f1f1f1; /* Set background color for the body */
        overflow-y: auto;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        margin-top: 20px;
        padding: 20px;
        background-color: #31363F; /* Set background color for the container */
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
        height: 100%;
        overflow-y: auto;
    }

    h1 {
        text-align: center;
        color: #fff; /* Set color for headings */
    }

    form {
        margin-top: 20px;
    }

    .section {
        margin-bottom: 20px;
        /* border: 1px solid #EEEE; */
        padding: 20px;
        border-radius: 4px;
        background-color: #31363F; /* Dark grey background */
        color: #ffffff; /* White text color */
    }

    .section h3 {
        margin-top: 0;
        color: #ffffff; /* White text color */
    }
    .gap{
        margin-top: 8.5vh;
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
    #due_date,
    #invoice_date {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    label {
        display: block;
        margin-bottom: 5px;
        color: #ffffff; /* White text color */
    }

    input[type="text"],
    input[type="email"],
    input[type="number"],
    select,
    textarea {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid black;
        border-radius: 4px;
        color: #333333;
        background-color: #DEE2E6; /* Dark grey text color */
    }
    textarea {
        height: 100px;
    }

    .add-product,
    .remove-product-btn {
        display: block;
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 4px;
        cursor: pointer;
    }
.but{
    display: flex;
    justify-content: center;
    align-items: baseline;
    gap: 20px;
}
    .add-product {
        display: block;
        margin-top: 10px;
    }

    .remove-product-btn {
        margin-top: 20px;
    }

    .product-item {
        display: flex;
        align-items: baseline;
        gap: 10px;
        width: 100%;
        margin-bottom: 10px;
    }

    .product-item select,
    .product-item input {
        flex: 1;
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

    .readonly {
        background-color: #f1f1f1;
        pointer-events: none;
    }
</style>

</head>
<body>
<?php
include('../includes/sidebar.php');
?>
    <div class="container">
        <h1>Create Invoice</h1>
        <form action="create_invoice.php" method="post">
            <div class="section">
                <h3>Invoice Details</h3>
                <div class="row">
                    <div>
                        <label for="invoice_type">Invoice Type</label>
                        <select id="invoice_type" name="invoice_type" required>
                            <option value="Invoice">Invoice</option>
                            <option value="Quote">Quote</option>
                            <option value="Receipt">Receipt</option>
                        </select>
                    </div>
                    <div>
                        <label for="invoice_status">Status</label>
                        <select id="invoice_status" name="invoice_status" required>
                            <option value="Paid">Paid</option>
                            <option value="Open">Pending</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="invoice_date">Invoice Date</label>
                        <input type="date" id="invoice_date" name="invoice_date" required>
                    </div>
                    <div>
                        <label for="due_date">Due Date</label>
                        <input type="date" id="due_date" name="due_date" required>
                    </div>
                </div>

                <div class="gap customer-info">
                       <h3>Customer Information</h3>
                       <a href="#" data-toggle="modal" data-target="#customerModal">or select existing customer</a>
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
                <div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Select Existing Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- This is where customer data will be inserted -->
                        <?php
                        include('../includes/db.php');
                        $sql = "SELECT id, name, email, address, town, country, postcode, phone FROM customers";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . $row['name'] . '</td>';
                                echo '<td>' . $row['email'] . '</td>';
                                echo '<td><button class="btn btn-primary select-customer" data-customer=\''.json_encode($row).'\'>Select</button></td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
            <div class="gap shipping-info">
                <h3>Shipping Information</h3>
                <div class="row">
                    <div>
                        <label for="shipping_name">Name</label>
                        <input type="text" id="shipping_name" name="shipping_name" class="readonly" required>
                    </div>
                    <div>
                        <label for="shipping_address">Address</label>
                        <input type="text" id="shipping_address" name="shipping_address" class="readonly" required>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="shipping_town">Town</label>
                        <input type="text" id="shipping_town" name="shipping_town" class="readonly" required>
                    </div>
                    <div>
                        <label for="shipping_country">Country</label>
                        <input type="text" id="shipping_country" name="shipping_country" class="readonly" required>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="shipping_postcode">Postcode</label>
                        <input type="text" id="shipping_postcode" name="shipping_postcode" class="readonly" required>
                    </div>
                </div>
            </div>

            <div class="gap products-section">
                <h3>Products</h3>
                <div class="product-items">
                    <div class="product-item">
                        <select name="product[]" class="product-select" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $product) : ?>
                                <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>"><?php echo $product['name']; ?> - ₹<?php echo $product['price']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="quantity[]" placeholder="Quantity" min="1" class="quantity-input" required>
                        <input type="number" name="price[]" placeholder="Price (calculated automatically)" step="0.01" class="price-input" readonly required>
                        <input type="text" name="discount[]" placeholder="Discount (enter with %)" class="discount-input">
                        
                    </div>
                    
                </div>
               <div class="but">
                <button type="button" id="add-product-btn" class="add-product">Add Product</button>
                
           
            </div>
            </div>

            <div class="gap">
                <h3>Additional Notes</h3>
                <textarea name="additional_notes" placeholder="Enter any additional notes here"></textarea>
            </div>

            <div class="gap">
                <h3>Bill Details</h3>
                <div class="row">
                    <div>
                        <label for="total">Total</label>
                        <input type="number" id="total" name="total" step="0.01" readonly>
                    </div>
                    <div>
                        <label for="bill_discount">Discount</label>
                        <input type="number" id="bill_discount" name="bill_discount" step="0.01" readonly>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="shipping_charges">Shipping Charges</label>
                        <input type="number" id="shipping_charges" name="shipping_charges" step="0.01" required>
                    </div>
                    <div>
                        <label for="tax">Tax</label>
                        <input type="number" id="tax" name="tax" step="0.01" required>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="subtotal">Subtotal</label>
                        <input type="number" id="subtotal" name="subtotal" step="0.01" readonly>
                    </div>
                </div>
            </div>

            <div class=" gap submit-section">
                <button type="submit">Create Invoice</button>
            </div>
             </div>
            </div>
        </form>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
    // Handle customer selection
    $('.select-customer').on('click', function() {
        var customer = $(this).data('customer');
        $('#customer_name').val(customer.name);
        $('#customer_email').val(customer.email);
        $('#customer_address').val(customer.address);
        $('#customer_town').val(customer.town);
        $('#customer_country').val(customer.country);
        $('#customer_postcode').val(customer.postcode);
        $('#customer_phone').val(customer.phone);

        // Update shipping information fields
        $('#shipping_name').val(customer.name);
        $('#shipping_address').val(customer.address);
        $('#shipping_town').val(customer.town);
        $('#shipping_country').val(customer.country);
        $('#shipping_postcode').val(customer.postcode);


    });
});
</script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const customerInputs = document.querySelectorAll('.customer-info input');
                const shippingInputs = document.querySelectorAll('.shipping-info input');

                customerInputs.forEach((input, index) => {
                    input.addEventListener('input', function () {
                        if (index === 0) { // Name
                            document.getElementById('shipping_name').value = this.value;
                        } else if (index === 1) { // Email
                            // No shipping field for email, skipping
                        } else if (index === 2) { // Address
                            document.getElementById('shipping_address').value = this.value;
                        } else if (index === 3) { // Town
                            document.getElementById('shipping_town').value = this.value;
                        } else if (index === 4) { // Country
                            document.getElementById('shipping_country').value = this.value;
                        } else if (index === 5) { // Postcode
                            document.getElementById('shipping_postcode').value = this.value;
                        }
                    });
                });

                document.getElementById('add-product-btn').addEventListener('click', function () {
                    const productSection = document.querySelector('.products-section .product-items');
                    const productItem = document.createElement('div');
                    productItem.classList.add('product-item');
                    productItem.innerHTML = `
                        <select name="product[]" class="product-select" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $product) : ?>
                                <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>"><?php echo $product['name']; ?> - $<?php echo $product['price']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="quantity[]" placeholder="Quantity" min="1" class="quantity-input" required>
                        <input type="number" name="price[]" placeholder="Price" step="0.01" class="price-input" readonly required>
                        <input type="text" name="discount[]" placeholder="Discount" class="discount-input">
                        <button type="button" class="remove-product-btn">Remove</button>
                    `;
                    productSection.appendChild(productItem);

                    productItem.querySelector('.remove-product-btn').addEventListener('click', function () {
                        productSection.removeChild(productItem);
                        calculateBill();
                    });

                    productItem.querySelectorAll('input, select').forEach(input => {
                        input.addEventListener('input', calculateBill);
                    });
                });

                document.querySelectorAll('.product-item input, .product-item select').forEach(input => {
                    input.addEventListener('input', calculateBill);
                });

                document.getElementById('shipping_charges').addEventListener('input', calculateBill);
                document.getElementById('tax').addEventListener('input', calculateBill);

                function calculateBill() {
                    let total = 0;
                    let totalDiscount = 0;

                    document.querySelectorAll('.product-item').forEach(item => {
                        const price = parseFloat(item.querySelector('.product-select').selectedOptions[0].getAttribute('data-price')) || 0;
                        const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
                        const discount = item.querySelector('.discount-input').value || 0;

                        const itemTotal = price * quantity;
                        total += itemTotal;

                        if (discount.includes('%')) {
                            const discountPercent = parseFloat(discount) / 100;
                            totalDiscount += itemTotal * discountPercent;
                        } else {
                            totalDiscount += parseFloat(discount) || 0;
                        }

                        // Update price input field with selected product price
                        item.querySelector('.price-input').value = price.toFixed(2);
                    });

                    const shippingCharges = parseFloat(document.getElementById('shipping_charges').value) || 0;
                    const tax = parseFloat(document.getElementById('tax').value) || 0;

                    const subtotal = total - totalDiscount + shippingCharges + tax;

                    document.getElementById('total').value = total.toFixed(2);
                    document.getElementById('bill_discount').value = totalDiscount.toFixed(2);
                    document.getElementById('subtotal').value = subtotal.toFixed(2);
                }
            });
        </script>
    </div>
</body>
</html>
