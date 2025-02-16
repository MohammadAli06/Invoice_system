<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjusted path to autoload.php
include('../includes/db.php');

// Retrieve the customer ID and invoice ID from the query parameters
if (isset($_GET['customer_id']) && isset($_GET['id'])) {
    $customer_id = $_GET['customer_id'];
    $invoice_id = $_GET['id'];
} else {
    die("Customer ID or Invoice ID not provided.");
}


// Retrieve the customer's email address
$sql = "SELECT email FROM customers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->bind_result($customer_email);
$stmt->fetch();
$stmt->close();

// Check if email was retrieved
if (!$customer_email) {
    die("No email address found for the given customer ID.");
}

// Retrieve invoice items for the specific invoice ID
$sql = "SELECT ii.invoice_id, ii.product_id, ii.quantity, ii.price, p.name AS product_name 
        FROM invoice_items ii
        JOIN products p ON ii.product_id = p.id
        WHERE ii.invoice_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $invoice_id);
$stmt->execute();
$stmt->bind_result($id, $product_id, $quantity, $price, $product_name);

// Build the HTML table for invoice items
$invoice_items_html = '<table border="1" cellpadding="5" cellspacing="0">';
$invoice_items_html .= '<tr><th>Invoice ID</th><th>Product ID</th><th>Product Name</th><th>Quantity</th><th>Price</th></tr>';

while ($stmt->fetch()) {
    $invoice_items_html .= "<tr>
        <td>{$id}</td>
        <td>{$product_id}</td>
        <td>{$product_name}</td>
        <td>{$quantity}</td>
        <td>" . number_format($price, 2) . "</td>
    </tr>";
}

$invoice_items_html .= '</table>';

$stmt->close();

// Retrieve the invoice total details
$sql = "SELECT total, bill_discount, shipping_charges, tax, subtotal FROM invoice WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $invoice_id);
$stmt->execute();
$stmt->bind_result($total, $bill_discount, $shipping_charges, $tax, $subtotal);
$stmt->fetch();

// Build the HTML table for invoice totals
$invoice_totals_html = '<br><table border="1" cellpadding="5" cellspacing="0">';
$invoice_totals_html .= '<tr><th>Total</th><td>' . number_format($total, 2) . '</td></tr>';
$invoice_totals_html .= '<tr><th>Discount</th><td>' . number_format($bill_discount, 2) . '</td></tr>';
$invoice_totals_html .= '<tr><th>Shipping Charges</th><td>' . number_format($shipping_charges, 2) . '</td></tr>';
$invoice_totals_html .= '<tr><th>Tax</th><td>' . number_format($tax, 2) . '</td></tr>';
$invoice_totals_html .= '<tr><th>Subtotal</th><td>' . number_format($subtotal, 2) . '</td></tr>';
$invoice_totals_html .= '</table>';

$stmt->close();

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;
    $mail->Username = 'quickfinisher19811@gmail.com'; // SMTP username
    $mail->Password = 'aucg gvnl lrji kmch';         // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('quickfinisher19811@gmail.com', 'Invoc PVT');
    $mail->addAddress($customer_email); // Add the customer's email address

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Invoice Notification';
    $mail->Body    = 'This is a reminder for your invoice. Please find the details below:<br><br>' . $invoice_items_html . '<br><br>' . $invoice_totals_html;
    $mail->AltBody = 'This is a reminder for your invoice. Please find the details attached.';

    $mail->send();
    echo 'Message has been sent to ' . $customer_email;
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

// Close the database connection
$conn->close();
?>
