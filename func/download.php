<?php
require('../fpdf/fpdf.php');
include('../includes/db.php');

// Retrieve the invoice ID from the URL
if (isset($_GET['id'])) {
    $invoice_id = $_GET['id'];

    // Retrieve invoice data
    $sql_invoice = "SELECT * FROM invoice WHERE id = ?";
    $stmt_invoice = $conn->prepare($sql_invoice);
    if ($stmt_invoice) {
        $stmt_invoice->bind_param("s", $invoice_id); // Assuming invoice_id is string type
        $stmt_invoice->execute();
        $invoice = $stmt_invoice->get_result()->fetch_assoc();
        $customer_id = $invoice['customer_id']; // Retrieve customer ID from invoice
        $stmt_invoice->close();

        if (!$invoice) {
            die("Invoice not found.");
        }

        // Retrieve customer data
        $sql_customer = "SELECT * FROM customers WHERE id = ?";
        $stmt_customer = $conn->prepare($sql_customer);
        if ($stmt_customer) {
            $stmt_customer->bind_param("i", $customer_id); // Bind customer ID to retrieve customer details
            $stmt_customer->execute();
            $customer = $stmt_customer->get_result()->fetch_assoc();
            $stmt_customer->close();

            if (!$customer) {
                die("Customer not found for this invoice.");
            }

            // Retrieve invoice items data related to the specific invoice
            $sql_items = "SELECT ii.product_id, ii.quantity, ii.price, p.name AS product_name
            FROM invoice_items ii
            JOIN products p ON ii.product_id = p.id
            WHERE ii.invoice_id = ?";

            $stmt_items = $conn->prepare($sql_items);
            if ($stmt_items) {
                $stmt_items->bind_param("s", $invoice_id); // Bind invoice ID
                $stmt_items->execute();
                $result_items = $stmt_items->get_result();
                $invoice_items = [];
                while ($row = $result_items->fetch_assoc()) {
                    $invoice_items[] = $row;
                }
                $stmt_items->close();

                // Close the database connection
                $conn->close();

                // Create PDF using FPDF class
                class PDF extends FPDF
                {
                    private $logo;

                    function __construct()
                    {
                        parent::__construct();
                        // Load logo image
                        $this->logo = '../assets/images/logo1.jpeg'; // Adjust the path as necessary
                    }

                    function Header()
                    {
                        // Logo
                        $this->Image($this->logo, 170, 10, 30);
                    
                        // Move down to leave space below the logo
                        $this->SetY(32);
                    
                        // Title
                        $this->SetFont('Arial', 'B', 20);
                        $this->Cell(0, 10, '', 0, 1, 'R');
                    
                        // Line below title
                        $this->Ln(5);
                        $this->SetLineWidth(0.5);
                        $this->Line(10, 55, 200, 55);
                    
                        // Extra space after line
                        $this->Ln(10);
                    }
                    

                    function Footer()
                    {
                        $this->SetY(-15);
                        $this->SetFont('Arial', 'I', 8);
                        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
                    }

                    function InvoiceTitle()
                    {
                        $this->SetFont('Arial', 'B', 20);
                        $this->Cell(0, 15, 'INVOICE', 0, 1, 'C');
                        $this->Ln(5);
                    }

                    function InvoiceInfo($invoice, $customer)
{
    $this->SetFont('Arial', '', 12);

    $this->Cell(100, 10, 'Invoice ID: ' . $invoice['id'], 0, 0);

    $this->SetX(110);
    $this->Cell(90, 10, 'Customer Name: ' . $customer['name'], 0, 1, 'R');

    $this->Cell(0, 10, 'Invoice Date: ' . $invoice['invoice_date'], 0, 1);
    $this->Ln(10);


    
    // Add shipping details if available
    if (!empty($invoice['shipping_name'])) {
        $this->Cell(0, 10, 'Ship To:', 0, 1);
        $this->Cell(0, 10, $invoice['shipping_name'], 0, 1);
        $this->Cell(0, 10, $invoice['shipping_address'], 0, 1);
        $this->Cell(0, 10, $invoice['shipping_town'] . ', ' . $invoice['shipping_country'], 0, 1);
        $this->Cell(0, 10, 'Postal Code: ' . $invoice['shipping_postcode'], 0, 1);
    }
    
    $this->Ln(10); // Add extra space after the block
}

                    function InvoiceTable($header, $data)
                    { 
                        $w = array(30, 60, 30, 30, 30); // Adjusted widths for columns
                        for ($i = 0; $i < count($header); $i++) {
                            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
                        }
                        $this->Ln();
                        
                        foreach ($data as $row) {
                            $this->Cell($w[0], 6, $row['product_id'], 1);
                            $this->Cell($w[1], 6, $row['product_name'], 1);
                            $this->Cell($w[2], 6, $row['quantity'], 1);
                            $this->Cell($w[3], 6, number_format($row['price'], 2), 1);
                            $this->Ln();
                        }
                    }
                    function InvoiceTotal($invoice)
                    {
                        $this->SetFont('Arial', 'B', 12);
                        
                        $this->Cell(130, 7, 'Total', 1);
                        $this->Cell(30, 7, number_format($invoice['total'], 2), 1, 0, 'R');
                        $this->Ln();
                        
                        $this->Cell(130, 7, 'Discount', 1);
                        $this->Cell(30, 7, number_format($invoice['bill_discount'], 2), 1, 0, 'R');
                        $this->Ln();
                        
                        $this->Cell(130, 7, 'Shipping Charges', 1);
                        $this->Cell(30, 7, number_format($invoice['shipping_charges'], 2), 1, 0, 'R');
                        $this->Ln();
                        
                        $this->Cell(130, 7, 'Tax', 1);
                        $this->Cell(30, 7, number_format($invoice['tax'], 2), 1, 0, 'R');
                        $this->Ln();
                        
                        $this->Cell(130, 7, 'Subtotal', 1);
                        $this->Cell(30, 7, number_format($invoice['subtotal'], 2), 1, 0, 'R');                              
                        $this->Ln();
                        
                   }
                }

                $pdf = new PDF();
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->InvoiceTitle();
                $pdf->InvoiceInfo($invoice, $customer);
                $header = array('Product ID', 'Product Name', 'Quantity', 'Price');
                $pdf->InvoiceTable($header, $invoice_items);
                $pdf->InvoiceTotal($invoice); // Assuming 'total' is from the invoice table
                $pdf->Output('D', 'invoice_' . $invoice['id'] . '.pdf');
            } else {
                die("Error in preparing statement: " . $conn->error);
            }
        } else {
            die("Error in preparing statement: " . $conn->error);
        }
    } else {
        die("Error in preparing statement: " . $conn->error);
    }
} else {
    echo "No invoice ID provided.";
}
?>
