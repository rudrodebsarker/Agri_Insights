<?php
session_start();

// Check if the user is logged in and their user_type is 'Agricultural Officer'
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Agricultural_Officer') {
    $_SESSION['msg'] = "You must log in as Agricultural Officer first";
    header('location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agricultural Officer Dashboard</title>
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
            background-color: #2980b9;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .logo {
            font-size: 24px;
            color: white;
            font-weight: bold;
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
        }

        .nav-links a:hover {
            text-decoration: underline;
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

        p {
            font-size: 18px;
            color: #333;
        }

        /* Footer */
        footer {
            background-color: #2980b9;
            color: white;
            text-align: center;
            padding: 10px 0;
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
        }

    </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="navbar-container">
      <img class="logo-img" src="images/pic2.png" alt="Logo" style="width: 100px; height: 80px; border-radius: 50%;">
      <a href="#" class="logo">AgriInsights</a>
      <ul class="nav-links">
        <li><a href="agriOficer.php">Fill the Form</a></li>
        <li><a href="agriOficers_List.php">View AgriOfficer List</a></li>
        <li><a href="production_list.php">Crop Report</a></li>
        <li><a href="officer_recommendations.php">Recommendations</a></li>
        <li><a href="index.php?logout='1'">Logout</a></li>
      </ul>
    </div>
  </nav>

  <!-- Main Section -->
  <section class="container">
    <h1>Welcome, Agricultural Officer</h1>
    <p>Assist farmers and provide agricultural recommendations.</p>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 Agricultural Officer Dashboard | AgriInsights</p>
  </footer>

</body>
</html>
