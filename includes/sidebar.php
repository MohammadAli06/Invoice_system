<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if it's not already started
}// Start the session to access session variables
if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

// Check if the user is logged in and determine their role
if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role'];

    // Sidebar navigation items
    $sidebar_items = [
        ['label' => 'Dashboard', 'link' => '../dashboard.php'],
        ['label' => 'Invoice', 'link' => '#invoice', 'submenu' => [
            ['label' => 'Create Invoice', 'link' => '../../INVOICE_SYSTEM_FINAL/templates/create_invoice.php'],
            ['label' => 'Manage Invoice', 'link' => '../../INVOICE_SYSTEM_FINAL/templates/manage_invoices.php']
        ]],
        ['label' => 'Products', 'link' => '#products', 'submenu' => [
            ['label' => 'Add Products', 'link' => '../../INVOICE_SYSTEM_FINAL/templates/add_product.php'],
            ['label' => 'Manage Products', 'link' => '../../INVOICE_SYSTEM_FINAL/templates/manage_products.php']
        ]],
        ['label' => 'Customers', 'link' => '#cust', 'submenu' => [
            ['label' => 'Add Customer', 'link' => '../../INVOICE_SYSTEM_FINAL/templates/add_customer.php'],
            ['label' => 'Manage Customers', 'link' => '../../INVOICE_SYSTEM_FINAL/templates/manage_customers.php']
        ]],
        ['label' => 'Profile', 'link' => '#profile', 'submenu' => [
            ['label' => 'Logout', 'link' => '../../INVOICE_SYSTEM_FINAL/logout.php']
        ]]
    ];

    // Conditionally add User Management based on role
    if ($user_role === 'Admin') {
        $sidebar_items[] = ['label' => 'User Management', 'link' => '#userSubmenu', 'submenu' => [
            ['label' => 'Add User', 'link' => '../../INVOICE_SYSTEM_FINAL/templates/add_user.php'],
            ['label' => 'Manage Users', 'link' => '../../INVOICE_SYSTEM_FINAL/templates/manage_users.php']
        ]];
    }

    // Display sidebar
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>Invoice Generator</title>';
    echo '<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">';
    echo '<style>';
    echo 'body { display: flex; height: 100vh; overflow: hidden; }';
    echo '#sidebar { min-width: 250px; max-width: 250px; background: #343a40; color: #fff; transition: all 0.3s; }';
    echo '#sidebar.active { margin-left: -250px; }';
    echo '#sidebar .sidebar-header { padding: 20px; background: #4e555b; }';
    echo '#sidebar .list-group-item { background: none; border: none; color: #fff; }';
    echo '#sidebar .list-group-item:hover { background: #495057; }';
    echo '#sidebar .dropdown-menu { background: #343a40; border: none; }';
    echo '#sidebar .dropdown-item { color: #fff; }';
    echo '#sidebar .dropdown-item:hover { background: #495057; }';
    echo '#content { flex: 1; padding: 20px; overflow: auto; }';
    echo '.navbar { background: #343a40; color: #fff; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';

    echo '<div id="sidebar" class="bg-dark">';
    echo '<div class="sidebar-header text-center py-4">';
    echo '<h3>Invoice Generator</h3>';
    echo '</div>';
    echo '<ul class="list-group list-group-flush">';

    // Loop through sidebar items
    foreach ($sidebar_items as $item) {
        echo '<li class="list-group-item">';
        if (isset($item['submenu'])) {
            echo '<a href="' . $item['link'] . '" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle text-white">' . $item['label'] . '</a>';
            echo '<ul class="collapse list-unstyled" id="' . substr($item['link'], 1) . '">';
            foreach ($item['submenu'] as $subitem) {
                echo '<li class="list-group-item">';
                echo '<a href="' . $subitem['link'] . '" class="text-white">' . $subitem['label'] . '</a>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<a href="' . $item['link'] . '" class="text-white">' . $item['label'] . '</a>';
        }
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';

    echo '<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>';
    echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>';
    echo '<script src="https://kit.fontawesome.com/a076d05399.js"></script>';
    echo '<script>';
    echo 'document.getElementById(\'sidebarCollapse\').addEventListener(\'click\', function () {';
    echo 'document.getElementById(\'sidebar\').classList.toggle(\'active\');';
    echo '});';
    echo '</script>';
    echo '</body>';
    echo '</html>';
} else {
    // Handle case where role is not set (not logged in scenario)
    echo 'User role not found. Please log in.';
}
?>
