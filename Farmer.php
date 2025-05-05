<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Farmer Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 40px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #9333ea;
            box-shadow: 0 0 8px rgba(147, 51, 234, 0.2);
        }
        button {
            background: #9333ea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .next-page-btn {
            background: #9333ea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .next-page-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
                <button type="submit" name="add_farmer"><i class="fas fa-plus"></i> Add Farmer</button>
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
                echo "<script>alert('Error adding farmer: " . $conn->error . "');</script>";
            }
        }
        ?>

        <h2>Registered Farmers</h2>
        
        <div class="nav-buttons">
            <a href="index.php" class="next-page-btn"><i class="fas fa-home"></i> Home</a>
            <a href="Farmer_list.php" class="next-page-btn"><i class="fas fa-list"></i> View All Farmers</a>
        </div>
    </div>
</body>
</html>
