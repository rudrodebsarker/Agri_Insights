<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Agricultural_Officer') {
    $_SESSION['msg'] = "You must log in as Agricultural Officer first";
    header('location: login.php');
    exit();
}


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agriculture";

$conn = new mysqli($servername, $username, $password, $dbname);

// Fetch sales data for Consumer Demand
function getSalesData($conn) {
    $sql = "SELECT s.sale_id, sd.product_id, sd.quantity_sold as quantity, 
                   sd.unit_price as price, DATE(s.sale_date) as date
            FROM sale s
            JOIN sale_details sd ON s.sale_id = sd.sale_id";
    
    $result = $conn->query($sql);
    
    $data = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    return $data;
}

// Fetch Price Elasticity Data
function getElasticityData($conn) {
    $sql = "SELECT 
                p.product_id, 
                p.name,
                AVG(sd.unit_price) as avg_price,
                SUM(sd.quantity_sold) as total_quantity
            FROM agri_product p
            LEFT JOIN sale_details sd ON p.product_id = sd.product_id
            GROUP BY p.product_id, p.name
            ORDER BY p.product_id";
    
    $result = $conn->query($sql);
    
    $products = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    // Calculate price elasticity (simplified example)
    $elasticityData = [];
    foreach ($products as $product) {
        if ($product['avg_price'] > 0 && $product['total_quantity'] > 0) {
            $price_change = rand(5, 20); 
            $quantity_change = -rand(3, 15); 
            $elasticity = $quantity_change / $price_change;
            
            $elasticityData[] = [
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'price_change' => $price_change,
                'quantity_change' => $quantity_change,
                'elasticity' => round($elasticity, 2)
            ];
        }
    }
    
    return $elasticityData;
}

// Fetch Current Unit Prices by Region
function getUnitPricesByRegion($conn) {
    $sql = "SELECT 
                r.district, 
                AVG(sd.unit_price) as avg_price
            FROM sale_details sd
            JOIN sale s ON sd.sale_id = s.sale_id
            JOIN retailer r ON s.retailer_id = r.retailer_id
            GROUP BY r.district";
    
    $result = $conn->query($sql);
    
    $regionData = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $regionData[] = $row;
        }
    }
    
    return $regionData;
}

// Get initial data
$salesData = getSalesData($conn);
$elasticityData = getElasticityData($conn);
$regionData = getUnitPricesByRegion($conn);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agricultural Officer Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Navbar */
        .navbar {
            background-color:rgb(11, 29, 46); 
            padding: 15px;
            display: flex;
            justify-content: flex-start; 
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
            color: white;
            gap: 20px; 
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .logo img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .nav-links {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 20px; 
        }

        .nav-links button {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #2980b9;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            border: none;
            transition: background-color 0.3s;
        }

        .nav-links button:hover {
            background-color: #3498db;
        }

        /* Main Content */
        .container {
            padding: 50px 20px;
            max-width: 1200px;
            margin: auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 32px;
            color: #2980b9;
        }

        /* Footer */
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        .chart-container {
            width: 90%;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .chart-box {
            width: 48%;
        }

        canvas {
            width: 100%;
            height: 200px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
            .chart-container {
                flex-direction: column;
                align-items: center;
            }
            .chart-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo">
      <img style="width: 100px; height:80px" src="images/pic2.png " alt="Logo">
      <span>AgriInsights</span>
    </div>
    <ul class="nav-links">
      <li><button onclick="window.location.href='agriOficer.php'">Crop Information</button></li>
      <li><button onclick="window.location.href='agriOficer.php'">Fill the Form</button></li>
      <li><button onclick="window.location.href='officer_view_production_list.php'">Crop Production Report</button></li>
      <li><button onclick="window.location.href='weathe.php'">Weather Info</button></li>
      <li><button onclick="window.location.href='officer_recommendations.php'">Recommendations</button></li>
      <li><button onclick="window.location.href='index.php?logout=1'">Logout</button></li>
    </ul>
  </nav>

  <!-- Main Section -->
  <section class="container">
    <h1>Welcome, Agricultural Officer</h1>
    <p>Assist farmers and provide agricultural recommendations.</p>

    <div class="chart-container">
        <div class="chart-box">
            <h3>Consumer Demand Over Time</h3>
            <canvas id="consumptionChart"></canvas>
        </div>

        <div class="chart-box">
            <h3>Price Elasticity by Product</h3>
            <canvas id="elasticityChart"></canvas>
        </div>

        <div class="chart-box">
            <h3>Price Trends Over Time</h3>
            <canvas id="priceTrendChart"></canvas>
        </div>

        <div class="chart-box">
            <h3>Current Unit Prices by Region</h3>
            <canvas id="regionPriceChart"></canvas>
        </div>
    </div>

  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 Agricultural Officer Dashboard | AgriInsights</p>
  </footer>

  <script>
    // Prepare data
    var salesData = <?php echo json_encode($salesData); ?>;
    var salesLabels = salesData.map(item => item.date);
    var salesQuantities = salesData.map(item => item.quantity);

    var elasticityData = <?php echo json_encode($elasticityData); ?>;
    var elasticityLabels = elasticityData.map(item => item.name);
    var elasticityValues = elasticityData.map(item => item.elasticity);

    var priceTrendsLabels = salesData.map(item => item.date);
    var priceTrendsPrices = salesData.map(item => parseFloat(item.price));

    var regionData = <?php echo json_encode($regionData); ?>;
    var regionLabels = regionData.map(item => item.district);
    var regionPrices = regionData.map(item => parseFloat(item.avg_price));

    // Create the Consumer Demand Chart
    var ctx1 = document.getElementById('consumptionChart').getContext('2d');
    var consumptionChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Quantity Sold',
                data: salesQuantities,
                backgroundColor: 'rgba(0, 123, 255, 0.5)',
                borderColor: 'blue',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Product Consumption Over Time'
                }
            }
        }
    });

    // Create the Price Elasticity Chart
    var ctx2 = document.getElementById('elasticityChart').getContext('2d');
    var elasticityChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: elasticityLabels,
            datasets: [{
                label: 'Price Elasticity',
                data: elasticityValues,
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Price Elasticity by Product'
                }
            }
        }
    });

    // Create the Price Trends Over Time Chart
    var ctx3 = document.getElementById('priceTrendChart').getContext('2d');
    var priceTrendChart = new Chart(ctx3, {
        type: 'line',
        data: {
            labels: priceTrendsLabels,
            datasets: [{
                label: 'Unit Price Over Time',
                data: priceTrendsPrices,
                borderColor: '#3b82f6',
                tension: 0.3,
                fill: false
            }]
        },
        options: {
            plugins: { title: { display: true, text: 'Price Trends Over Time' }},
            scales: {
                x: { title: { display: true, text: 'Date' }},
                y: { title: { display: true, text: 'Price' }}
            }
        }
    });

    // Create the Unit Prices by Region Chart
    var ctx4 = document.getElementById('regionPriceChart').getContext('2d');
    var regionPriceChart = new Chart(ctx4, {
        type: 'bar',
        data: {
            labels: regionLabels,
            datasets: [{
                label: 'Avg Price by Region',
                data: regionPrices,
                backgroundColor: '#10b981'
            }]
        },
        options: {
            plugins: { title: { display: true, text: 'Current Unit Prices by Region' }},
            scales: {
                x: { title: { display: true, text: 'District' }},
                y: { title: { display: true, text: 'Avg Price' }}
            }
        }
    });
  </script>

</body>
</html>
