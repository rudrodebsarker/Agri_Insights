<?php
session_start();

// Check if the user is logged in and their user_type is 'Warehouse_manager'
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Warehouse_manager') {
    $_SESSION['msg'] = "You must log in as Warehouse Manager first";
    header('location: login.php');
    exit();
}

// Database connection
$db = mysqli_connect('localhost', 'root', '', 'agriculture');

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all warehouse records from WAREHOUSE table
$sql = "SELECT * FROM WAREHOUSE";
$result = $db->query($sql);

// Fetch warehouse name and available stock for the Donut chart (sum of available stock)
$query_donut = "SELECT name, SUM(available_stock_of_product) AS total_stock FROM WAREHOUSE GROUP BY name";
$result_donut = mysqli_query($db, $query_donut);
$donut_data = [];
while ($row = mysqli_fetch_assoc($result_donut)) {
    $donut_data[] = $row;
}

// Handle form submission to edit warehouse data
if (isset($_POST['edit'])) {
    $warehouse_id = $_POST['warehouse_id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $contact_num = $_POST['contact_num'];
    $available_stock = $_POST['available_stock_of_product'];
    $last_updated = $_POST['last_updated'];

    // Update query to save the changes
    $update_sql = "UPDATE WAREHOUSE 
                   SET name='$name', location='$location', contact_num='$contact_num', available_stock_of_product='$available_stock', last_updated='$last_updated' 
                   WHERE warehouse_id='$warehouse_id'";

    if ($db->query($update_sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $db->error;
    }
}

// Close the connection
mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Manager Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Reset & Global Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Navbar */
        .navbar {
            background-color: #2c3e50;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ecf0f1;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        .nav-links a {
            color: #ecf0f1;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .nav-links a:hover {
            background-color: rgb(97, 153, 210);
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            font-size: 36px;
            margin-bottom: 15px;
        }

        p {
            font-size: 18px;
            color: #555;
        }

        /* Table Styles */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #5a2a94;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        .actions a {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .actions a:hover {
            background-color: #45a049;
        }

        /* Edit Form Styles */
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .form-container button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        /* Footer */
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: 60px;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                gap: 10px;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .container {
                margin: 20px;
                padding: 20px;
            }

            h1 {
                font-size: 28px;
            }

            .form-container {
                padding: 20px;
                margin-top: 20px;
            }
        }

        /* Donut Chart Styles */
        .chart-container {
            display: flex;
            justify-content: center; /* Center the chart */
            margin-top: 40px;
        }

        canvas {
            width: 400px !important;  /* Set width to 500px */
            height: 350px !important; /* Set height to 450px */
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="logo-section">
        <img class="logo-img" src="images/pic2.png" alt="Logo">
        <div class="logo">AgriInsights</div>
    </div>
    <ul class="nav-links">
        <li><a href="inventory.php">Inventory</a></li>
        <li><a href="storage.php">Storage</a></li>
        <li><a href="Warehouse_management.php">Warehouse</a></li>
        <li><a href="index.php?logout='1'">Logout</a></li>
    </ul>
</nav>

<!-- Main Section -->
<section class="container">
    <h1>Welcome, Warehouse Manager</h1>
    <p>Manage warehouse data and monitor stock levels here.</p>

    <!-- Donut Chart Section -->
    <h2>Warehouse Stock Distribution</h2>
    <div style="width: 80%; margin: 20px auto;">
        <canvas id="donutChart"></canvas>
    </div>

    <!-- Warehouse Table -->
    <h2>Existing Warehouses</h2>
    <table>
        <thead>
            <tr>
                <th>Warehouse ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Contact Number</th>
                <th>Available Stock</th>
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch and display warehouse records
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['warehouse_id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['location']}</td>
                            <td>{$row['contact_num']}</td>
                            <td>{$row['available_stock_of_product']}</td>
                            <td>{$row['last_updated']}</td>
                            <td class='actions'>
                                <a href='?edit_id={$row['warehouse_id']}#editForm'>Edit</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No warehouses available.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</section>

<!-- Edit Form Section -->
<?php
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    // Fetch the warehouse data for editing
    $db = mysqli_connect('localhost', 'root', '', 'agriculture');
    $sql = "SELECT * FROM WAREHOUSE WHERE warehouse_id='$edit_id'";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    mysqli_close($db);
?>
    <!-- Edit Form -->
    <div id="editForm" class="form-container">
        <h2>Edit Warehouse Information</h2>
        <form method="POST" action="warehouse_management.php">
            <input type="hidden" name="warehouse_id" value="<?php echo $row['warehouse_id']; ?>">

            <label for="name">Warehouse Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>" required><br>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" value="<?php echo $row['location']; ?>" required><br>

            <label for="contact_num">Contact Number:</label>
            <input type="text" id="contact_num" name="contact_num" value="<?php echo $row['contact_num']; ?>" required><br>

            <label for="available_stock_of_product">Available Stock:</label>
            <input type="number" id="available_stock_of_product" name="available_stock_of_product" value="<?php echo $row['available_stock_of_product']; ?>" required><br>

            <label for="last_updated">Last Updated:</label>
            <input type="date" id="last_updated" name="last_updated" value="<?php echo $row['last_updated']; ?>" required><br>

            <button type="submit" name="edit">Save Changes</button>
        </form>
    </div>
<?php } ?>

<!-- Footer -->
<footer>
    <p>&copy; 2025 Warehouse Manager Dashboard | AgriInsights</p>
</footer>

<script>
    // Prepare Donut Chart Data
    const donutData = {
        labels: <?php echo json_encode(array_column($donut_data, 'name')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($donut_data, 'total_stock')); ?>,
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800'],
            borderWidth: 1
        }]
    };

    // Create Donut Chart
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: donutData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw + ' units';
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>
