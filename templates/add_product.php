<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_description = $_POST['product_description'];

    // Validate inputs
    if (!empty($product_name) && !empty($product_price) && !empty($product_description)) {
        // Insert product into database
        $sql = "INSERT INTO products (name, price, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sds", $product_name, $product_price, $product_description);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Product added successfully.";
        } else {
            $_SESSION['message'] = "Error adding product: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = "All fields are required.";
    }

    // Redirect to the same page to clear POST data
    header("Location: manage_products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
        /* Global styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Default font */
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .content-container {
            margin-left: 50px;
            padding: 20px;
            width: calc(100% - 250px); /* Adjusting width to accommodate sidebar */
            box-sizing: border-box; /* Ensures padding is included in the width calculation */
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .containerr {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background-color: #495057;
            color:white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-family: 'Poppins', Arial, sans-serif; /* Custom font */
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            width: calc(100% - 20px);
        }

        textarea {
            height: 100px;
        }

        button[type="submit"] {
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 150px;
            margin-top: 10px;
            margin-left: 240px;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
    <!-- Link to Google Fonts for 'Poppins' -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
</head>
<body>
<?php
include('../includes/sidebar.php');
?>
       <div class="content-container">
           <div class="containerr">
            <h1>Add Product</h1>
            <form action="add_product.php" method="post">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" required>
                
                <label for="product_price">Product Price:</label>
                <input type="number" id="product_price" name="product_price" step="0.01" required>
                
                <label for="product_description">Description:</label>
                <textarea id="product_description" name="product_description" placeholder="Enter product description..." required></textarea>
                
                <button type="submit">Add Product</button>
            </form>
        </div>
    </div>
    <script>
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
