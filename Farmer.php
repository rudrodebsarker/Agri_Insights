<?php include('dp_config.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Farmer Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-section {
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        .action-links a {
            color: #2196F3;
            text-decoration: none;
            margin-right: 10px;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
        .nav-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .next-page-btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .next-page-btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-section">
            <h2>Add New Farmer</h2>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Full Name" required>
                </div>
                <div class="form-group">
                    <input type="text" name="road" placeholder="Road/Street">
                </div>
                <div class="form-group">
                    <input type="text" name="house" placeholder="House No">
                </div>
                <div class="form-group">
                    <input type="text" name="district" placeholder="District">
                </div>
                <div class="form-group">
                    <input type="text" name="area" placeholder="Area">
                </div>
                <div class="form-group">
                    <input type="text" name="country" placeholder="Country" value="Bangladesh">
                </div>
                <div class="form-group">
                    <input type="number" name="experience" placeholder="Years of Experience">
                </div>
                <button type="submit" name="add_farmer">Add Farmer</button>
            </form>
        </div>

        <?php
        // Add Farmer Logic
        if(isset($_POST['add_farmer'])) {
            $name = $conn->real_escape_string($_POST['name']);
            $road = $conn->real_escape_string($_POST['road']);
            $house = $conn->real_escape_string($_POST['house']);
            $district = $conn->real_escape_string($_POST['district']);
            $area = $conn->real_escape_string($_POST['area']);
            $country = $conn->real_escape_string($_POST['country']);
            $experience = intval($_POST['experience']);

            $sql = "INSERT INTO farmer (name, road, house, district, area, country, years_of_experience)
                    VALUES ('$name', '$road', '$house', '$district', '$area', '$country', $experience)";
            
            if($conn->query($sql)) {
                echo "<script>alert('Farmer added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding farmer!');</script>";
            }
        }

        // Delete Farmer Logic
        if(isset($_GET['delete_id'])) {
            $delete_id = intval($_GET['delete_id']);
            $conn->query("DELETE FROM farmer WHERE farmer_id = $delete_id");
            header("Location: Farmer_list.php");
        }
        ?>

        <h2>Registered Farmers</h2>
        
        <div class="nav-buttons">
            <a href="Farmer_list.php" class="next-page-btn"><i class="fas fa-arrow-right"></i> Next Page</a>
        </div>
    </div>
</body>
</html>