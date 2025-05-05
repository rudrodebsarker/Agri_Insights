
<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Farmer') {
    $_SESSION['msg'] = "You must log in as Farmer first";
    header('location: login.php');
    exit();
}

// Example dummy data (replace with actual DB queries)
$farmer_name = " Farmer Dashboard";
$crop_count = 5;
$sales_total = 8000;
$weather_station = "Dhaka Central";
$monthly_rainfall = "220 mm";

// Database connection
$db = mysqli_connect('localhost', 'root', '', 'agriculture');

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch crop data for Pie chart (crop distribution)
$query_crop = "SELECT name, COUNT(*) AS count FROM agri_product GROUP BY name";
$result_crop = mysqli_query($db, $query_crop);
$crops = [];
while ($row = mysqli_fetch_assoc($result_crop)) {
    $crops[] = $row;
}

// Fetch production data for Bar chart (production statistics)
$query_production = "SELECT product_id, SUM(yield) AS total_yield FROM production_data GROUP BY product_id";
$result_production = mysqli_query($db, $query_production);
$production_data = [];
while ($row = mysqli_fetch_assoc($result_production)) {
    $production_data[] = $row;
}

mysqli_close($db);

$product_names = [];
$product_prices = [];
foreach ($crops as $product) {
    $product_names[] = $product['name'];
    $product_prices[] = $product['count'];
}

$production_labels = [];
$production_yields = [];
foreach ($production_data as $data) {
    $production_labels[] = $data['product_id'];  // You can modify this to fetch the actual product names
    $production_yields[] = $data['total_yield'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Farmer Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
  body {
  font-family: Arial, sans-serif;
  background-color: #f7f9fc;
  margin: 0;
  padding: 0;
  color: #333;
  overflow-x: hidden;
}

.navbar {
  background-color: #5a2a94;
  padding: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #4c1c77;
}

.navbar .logo {
  font-size: 24px;
  color: white;
  font-weight: bold;
  margin-left: 10px;
}

.navbar-container {
  display: flex;
  justify-content: space-between;
  width: 100%;
  align-items: center;
}

.nav-links {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
}

.nav-links li {
  margin: 0 15px;
}

.nav-links a {
  color: white;
  text-decoration: none;
  font-weight: 500;
  padding: 8px 15px;
  border-radius: 5px;
}

.nav-links a:hover {
  background-color: #4c1c77;
  transition: background-color 0.3s ease;
}

.logo-img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  margin-right: 15px;
}

.container {
  padding: 40px 20px;
  max-width: 1200px;
  margin: auto;
  background-color: white;
  border-radius: 10px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  margin-top: 30px;
}

h1 {
  font-size: 32px;
  color: #4c1c77;
  text-align: center;
  margin-bottom: 20px;
}

.card-group {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin-top: 30px;
  justify-content: space-evenly;
}

.card {
  flex: 1;
  min-width: 150px;
  background-color: #f0f8ff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  text-align: center;
}

footer {
  background-color: #5a2a94;
  color: white;
  text-align: center;
  padding: 10px 0;
  margin-top: 30px;
}

.chart-container {
  display: flex;
  justify-content: space-between;
  gap: 30px;
  margin-top: 40px;
  flex-wrap: wrap;
}

canvas {
  width: 100% !important;
  height: 350px !important; /* Adjust height to make the chart smaller */
  max-width: 400px; /* Set maximum width for smaller chart */
  margin-bottom: 30px;
}

.nav-links button {
  padding: 10px 20px;
  background-color:rgb(23, 17, 72);
  border: none;
  color: white;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
}

.nav-links button:hover {
  background-color:rgb(77, 126, 204);
}

@media (max-width: 768px) {
  .chart-container {
    flex-direction: column;
    align-items: center;
  }

  .card-group {
    flex-direction: column;
    gap: 15px;
  }

  canvas {
    height: 250px !important; 
  }
}

  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="navbar-container">
      <div style="display: flex; align-items: center;">
        <img class="logo-img" src="images/pic2.png" alt="Logo">
        <a href="#" class="logo">AgriInsights</a>
      </div>
      <ul class="nav-links">
        <li><button onclick="window.location.href='f_agriProduct_list.php'">Crop Information</button></li>
        <li><button onclick="window.location.href='f_production_list.php'">Production Data</button></li>
         <li><button onclick="window.location.href='wholeSeller_list.php'">Byer Info</button></li>
        <li><button onclick="window.location.href='f_product_shipment_details.php'">Shipment Agri_Product </button></li>
        <li><button onclick="window.location.href='weather_Table.php'">weather Info</button></li>
        <li><button onclick="window.location.href='farmers_recommendation.php'">View Recomendation</button></li>
        <li><button onclick="window.location.href='index.php?logout=1'">Logout</button></li>
      </ul>
    </div>
  </nav>

  <!-- Main Section -->
  <section class="container">
    <h1>Welcome, <?php echo htmlspecialchars($farmer_name); ?></h1>
    <p>Monitor your crops and manage sales in the market.</p>

    <!-- Dashboard Cards -->
    

    <!-- Chart Section -->
    <div class="chart-container">
 
      <div>
        <canvas id="pieChart"></canvas>
      </div>

   
      <div>
        <canvas id="barChart"></canvas>
      </div>
    </div>

  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 Farmer Dashboard | AgriInsights</p>
  </footer>

  <script>
    // Prepare Pie Chart Data
    const pieData = {
      labels: <?php echo json_encode(array_column($crops, 'name')); ?>,
      datasets: [{
        data: <?php echo json_encode(array_column($crops, 'count')); ?>,
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800']
      }]
    };

    // Create Pie Chart
    new Chart(document.getElementById('pieChart'), {
      type: 'pie',
      data: pieData,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            callbacks: {
              label: function(tooltipItem) {
                return tooltipItem.label + ': ' + tooltipItem.raw + ' products';
              }
            }
          }
        }
      }
    });

    // Prepare Bar Chart Data (Production Yield)
    const barData = {
      labels: <?php echo json_encode($production_labels); ?>,
      datasets: [{
        label: 'Total Yield (kg)',
        data: <?php echo json_encode($production_yields); ?>,
        backgroundColor: '#36A2EB',
        borderColor: '#36A2EB',
        borderWidth: 1
      }]
    };

    // Create Bar Chart
    new Chart(document.getElementById('barChart'), {
      type: 'bar',
      data: barData,
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            callbacks: {
              label: function(tooltipItem) {
                return tooltipItem.label + ': ' + tooltipItem.raw + ' kg';
              }
            }
          }
        }
      }
    });
  </script>

</body>
</html>
