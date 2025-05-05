<?php
  session_start(); 

  if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
  }
  if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: login.php");
  }

  // Include database connection
  include 'db_config.php';

  // SQL query to join production_data and agri_product table using product_id
  $sql = "SELECT ap.name AS product_name, pd.yield, pd.acreage, pd.cost, ap.seasonality, ap.type
          FROM production_data pd
          INNER JOIN agri_product ap ON pd.product_id = ap.product_id";
  $result = $conn->query($sql);
  $products = [];
  while($row = $result->fetch_assoc()) {
    $products[] = $row;
  }

  // For chart data
  $product_names = [];
  $product_prices = [];
  foreach ($products as $product) {
    $product_names[] = $product['product_name'];
    $product_prices[] = $product['cost'];
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Demand & Supply Analysis for Agricultural Products</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
  <link rel="stylesheet" href="admin_dashboard.css"> <!-- Your updated CSS file -->
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="navbar-container">
      <img style="width: 100px ; height:80px;  border-radius:50%" class="logo-img" src="images/pic2.png" alt="Logo">
      <a href="#" class="logo">AgriInsights</a>
      <ul class="nav-links">
      <li class="dropdown">
          <a href="#">Admin Pannel</a>
          <ul class="dropdown-menu">
            <li><a href="weather.php">Weather Data Input</a></li>
            
            <li><a href="production_form.php">Agriofficer's Recomendation</a></li> 
          </ul>
        </li>


        <li class="dropdown">
          <a href="#">Product</a>
          <ul class="dropdown-menu">
            <li><a href="agriProduct_list.php">Agri-Product Info</a></li>
            <li><a href="production_list.php">Production Info</a></li> 
          </ul>
        </li>
        <li class="dropdown">
            <a href="#">Sales & Market Price</a>
            <ul class="dropdown-menu">
              <li><a href="sale_information.php">Sale Info</a></li>
              <li><a href="price_trends.php">Price Trends</a></li>
              <li><a href="track_agri_traders.php"></a>Track Agri_traders</li>
              <li><a href="cd_pe.php">Demand & Price Elasticity</a></li>
              

              track_agri_traders.php
            </ul>
        </li>
        <li class="dropdown">
            <a href="#">Buyer Seller Directories</a>
            <ul class="dropdown-menu">
              <li><a href="Farmer.html">Farmer</a></li>
              <li><a href="WholeSeller.html">Wholesaler</a></li>
              <li><a href="Retailer.html">Retailer</a></li>
              <li><a href="Consumer.html">Consumer</a></li>
            </ul>
        </li>

        <li class="dropdown">
            <a href="#">Supply Level</a>
            <ul class="dropdown-menu">
              <li><a href="inventory.php">Inventory</a></li>
              <li><a href="storage.php">Storage Status</a></li>
              <li><a href="shipment.php">Shipment</a></li>
              <li><a href="warehouse_management.php">Warehouse</a></li>
              <li><a href="logistics.php">Logistics Tracking</a></li>
            </ul>
        </li>
         <li><a href="agriOficers_List.php">Agri_officer</a></li>
        <li id="Logout"><a href="index.php?logout='1'">Logout</a></li>
      </ul>
    </div>
  </nav>

  <!-- Homepage Content -->
  <section class="home-section">
    <div class="container">
      <h1>Welcome to Demand & Supply Analysis for Agricultural Products</h1>
      <p>Analyze the dynamics of agricultural products and make informed decisions!</p>
    </div>
  </section>

   <!-- Bar Chart -->
   <div class="chart-container">
    <canvas id="productChart"></canvas>
  </div>

   <!-- Product Table -->
   <h2>Product Information</h2>
   <table id="productTable">
     <thead>
       <tr>
         <th>Product Name</th>
         <th>Yield</th>
         <th>Acreage</th>
         <th>Price</th>
         <th>Seasonality</th>
         <th>Type</th>
       </tr>
     </thead>
     <tbody>
       <?php
       foreach ($products as $product) {
         echo "<tr>
                 <td>{$product['product_name']}</td>
                 <td>{$product['yield']}</td>
                 <td>{$product['acreage']}</td>
                 <td>{$product['cost']}</td>
                 <td>{$product['seasonality']}</td>
                 <td>{$product['type']}</td>
               </tr>";
       }
       ?>
     </tbody>
   </table>

  <footer>
    <p>&copy; 2025 Demand & Supply Analysis for Agricultural Products</p>
  </footer>

  <script>
    // Data for the chart
    const labels = <?php echo json_encode($product_names); ?>;
    const data = <?php echo json_encode($product_prices); ?>;

    // Create chart
    const ctx = document.getElementById('productChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Product Prices',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
  </script>

</body>
</html>
