<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Generator</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #343a40;
            color: #fff;
            transition: all 0.3s;
        }
        #sidebar.active {
            margin-left: -250px;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: #4e555b;
        }
        #sidebar .list-group-item {
            background: none;
            border: none;
            color: #fff;
        }
        #sidebar .list-group-item:hover {
            background: #495057;
        }
        #sidebar .dropdown-menu {
            background: #343a40;
            border: none;
        }
        #sidebar .dropdown-item {
            color: #fff;
        }
        #sidebar .dropdown-item:hover {
            background: #495057;
        }
        #content {
            flex: 1;
            padding: 20px;
            overflow: auto;
        }
        .navbar {
            background: #343a40;
            color: #fff;
        }
    </style>
</head>
<body>

    <div id="sidebar" class="bg-dark">
        <div class="sidebar-header text-center py-4">
            <h3>Invoice Generator</h3>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><a href="./user_dashboard.php" class="text-white">Dashboard</a></li>
            <li class="list-group-item">
            <a href="#invoice" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle text-white">Invoice</a>
                <ul class="collapse list-unstyled" id="invoice">
                    <li class="list-group-item">
                        <a href="#" class="text-white">Create invoice</a>
                    </li>
                    <li class="list-group-item">
                        <a href="#" class="text-white">Manage Invoice</a>
                    </li>
                </ul>
              </li>
            <li class="list-group-item">
            <a href="#products" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle text-white">Products</a>
                <ul class="collapse list-unstyled" id="products">
                    <li class="list-group-item">
                        <a href="#" class="text-white">Add products</a>
                    </li>
                    <li class="list-group-item">
                        <a href="#" class="text-white">Manage products</a>
                    </li>
                </ul>
              </li>
            <li class="list-group-item">
            <a href="#cust" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle text-white">Customers</a>
                <ul class="collapse list-unstyled" id="cust">
                    <li class="list-group-item">
                        <a href="#" class="text-white">Add Customer</a>
                    </li>
                    <li class="list-group-item">
                        <a href="#" class="text-white">Manage Customers</a>
                    </li>
                </ul>
              </li>
            <li class="list-group-item">
            <a href="#profile" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle text-white">Profile</a>
                <ul class="collapse list-unstyled" id="profile">
                    <li class="list-group-item">
                        <a href="#" class="text-white">Edit Profile</a>
                    </li>
                    <li class="list-group-item">
                        <a href="#" class="text-white">Logout</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script>
        document.getElementById('sidebarCollapse').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>
