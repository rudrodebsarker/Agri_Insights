<?php
include('server.php');

// Check if the user is logged in and is a Wholesaler
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Wholesaler') {
    $_SESSION['msg'] = "You must log in as Wholesaler first";
    header('location: login.php');
    exit();
}

// Check if updated wholesaler info exists in session
if (isset($_SESSION['selected_wholesaler'])) {
    $wholesaler = $_SESSION['selected_wholesaler'];
} else {
    $username = $_SESSION['username'];
    $wholesaler_query = "SELECT * FROM wholesaler WHERE name = '$username' LIMIT 1"; 
    $wholesaler_result = mysqli_query($db, $wholesaler_query);
    $wholesaler = mysqli_fetch_assoc($wholesaler_result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Wholesaler Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: url('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      height: 100vh;
      overflow: hidden;
    }

    .sidebar {
      width: 250px;
      background: rgba(44, 62, 80, 0.95);
      color: #fff;
      padding: 30px 20px;
      display: flex;
      flex-direction: column;
      box-shadow: 2px 0 10px rgba(0,0,0,0.2);
    }

    .sidebar h2 {
      text-align: center;
      font-size: 1.8rem;
      margin-bottom: 2rem;
    }

    .sidebar a {
      padding: 12px 18px;
      margin: 6px 0;
      border-radius: 6px;
      text-decoration: none;
      color: #fff;
      font-size: 1rem;
      background-color: transparent;
      transition: background 0.3s;
    }

    .sidebar a:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }

    .dropdown-content {
      display: none;
      flex-direction: column;
      margin-left: 10px;
    }

    .dropdown-content a {
      background-color: rgba(255, 255, 255, 0.05);
      margin: 2px 0;
      font-size: 0.95rem;
    }

    .main {
      margin-left: 250px;
      padding: 60px 40px;
      flex-grow: 1;
      color: #fff;
      text-shadow: 1px 1px 3px #000;
      overflow-y: auto;
    }

    .info-card {
      background-color: rgba(0, 0, 0, 0.6);
      padding: 30px;
      border-radius: 12px;
      max-width: 600px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }

    .info-card h2 {
      margin-top: 0;
      font-size: 1.6rem;
      margin-bottom: 1rem;
    }

    .info-card p {
      font-size: 1.1rem;
      margin: 0.5rem 0;
    }
  </style>
</head>

<body>

<div class="sidebar">
  <h2>Wholesaler</h2>
  <a href="wholesaler_dashboard.php">Dashboard</a>
  <a href="update_wholesaler_profile.php">Update Profile</a>
  <a href="Wholesaler.php">Join Database</a>
  <a href="inventory.php">Inventory</a>

  <div class="dropdown" onclick="toggleProductsDropdown()">
    <a>Products ▾</a>
    <div class="dropdown-content" id="productsDropdown">
      <a href="received_products_from_farmer.php">Received Products</a>
      <a href="available_products_to_wholesaler.php">Available Products</a>
    </div>
  </div>

  <a href="w_shipment.php">Shipments</a>
  <a href="logistics.php">Logistics</a>
  <a href="track_sales.php">Track Sales</a>

  <div class="dropdown" onclick="toggleDropdown()">
    <a>Buyer and Seller ▾</a>
    <div class="dropdown-content" id="analyticsDropdown">
      <a href="buyer_info_in_wholesaler.php">Buyer Info</a>
      <a href="seller_info_in_wholesaler.php">Seller Info</a>
    </div>
  </div>

  <a href="index.php">Logout</a>
</div>

<div class="main">
  <h1 style="font-size: 2.3rem; margin-bottom: 30px;">Welcome to the Wholesaler Dashboard</h1>
  
  <div class="info-card">
    <h2>Wholesaler Information</h2>
    <p><strong>Wholesaler ID:</strong> <?= isset($wholesaler['wholesaler_id']) ? htmlspecialchars($wholesaler['wholesaler_id']) : 'N/A'; ?></p>
    <p><strong>Name:</strong> <?= isset($wholesaler['name']) ? htmlspecialchars($wholesaler['name']) : htmlspecialchars($_SESSION['username']); ?></p>
    <p><strong>Contact:</strong> <?= isset($wholesaler['contact']) ? htmlspecialchars($wholesaler['contact']) : 'Not Updated'; ?></p>
    <p><strong>Location:</strong> 
      <?php
      if (isset($wholesaler['road']) && isset($wholesaler['house']) && isset($wholesaler['area']) && isset($wholesaler['district']) && isset($wholesaler['country'])) {
          echo htmlspecialchars($wholesaler['road']) . ', ' .
               htmlspecialchars($wholesaler['house']) . ', ' .
               htmlspecialchars($wholesaler['area']) . ', ' .
               htmlspecialchars($wholesaler['district']) . ', ' .
               htmlspecialchars($wholesaler['country']);
      } else {
          echo "Not Updated";
      }
      ?>
    </p>
  </div>
</div>

<script>
function toggleProductsDropdown() {
  const dropdown = document.getElementById("productsDropdown");
  dropdown.style.display = dropdown.style.display === "flex" ? "none" : "flex";
}

function toggleDropdown() {
  const dropdown = document.getElementById("analyticsDropdown");
  dropdown.style.display = dropdown.style.display === "flex" ? "none" : "flex";
}
</script>

</body>
</html>
