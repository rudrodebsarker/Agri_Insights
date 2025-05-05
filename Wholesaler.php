<?php 
// Include the dp_config.php file for database configuration
include('dp_config.php'); 

// Create the wholeseller table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS wholeseller (
    wholeseller_id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(50) NOT NULL,
    house VARCHAR(100) NOT NULL,
    road VARCHAR(100) NOT NULL,
    area VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL
)";
if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}

// Handle form submission for adding or updating wholeseller
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['update_wholeseller']) && isset($_POST['edit_id'])) {
        // Update existing wholeseller
        $wholeseller_id = $conn->real_escape_string($_POST['wholesellerId']);
        $name = $conn->real_escape_string($_POST['name']);
        $contact = $conn->real_escape_string($_POST['contact']);
        $house = $conn->real_escape_string($_POST['house']);
        $road = $conn->real_escape_string($_POST['road']);
        $area = $conn->real_escape_string($_POST['area']);
        $city = $conn->real_escape_string($_POST['city']);
        $country = $conn->real_escape_string($_POST['country']);
        $edit_id = $conn->real_escape_string($_POST['edit_id']);

        $sql = "UPDATE wholeseller SET 
                wholeseller_id = '$wholeseller_id',
                name = '$name', 
                contact = '$contact', 
                house = '$house',
                road = '$road', 
                area = '$area', 
                city = '$city', 
                country = '$country' 
                WHERE wholeseller_id = '$edit_id'";

        if ($conn->query($sql)) {
            echo "<script>alert('Wholesaler updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating wholesaler: " . $conn->error . "');</script>";
        }
    } else {
        // Add new wholeseller
        $wholeseller_id = $conn->real_escape_string($_POST['wholesellerId']);
        $name = $conn->real_escape_string($_POST['name']);
        $contact = $conn->real_escape_string($_POST['contact']);
        $house = $conn->real_escape_string($_POST['house']);
        $road = $conn->real_escape_string($_POST['road']);
        $area = $conn->real_escape_string($_POST['area']);
        $city = $conn->real_escape_string($_POST['city']);
        $country = $conn->real_escape_string($_POST['country']);

        // Check if wholeseller ID already exists
        $check = $conn->query("SELECT wholeseller_id FROM wholeseller WHERE wholeseller_id = '$wholeseller_id'");
        if ($check->num_rows > 0) {
            echo "<script>alert('Wholesaler ID already exists!');</script>";
        } else {
            $sql = "INSERT INTO wholeseller (wholeseller_id, name, contact, house, road, area, city, country) 
                    VALUES ('$wholeseller_id', '$name', '$contact', '$house', '$road', '$area', '$city', '$country')";

            if ($conn->query($sql)) {
                echo "<script>alert('Wholesaler added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding wholesaler: " . $conn->error . "');</script>";
            }
        }
    }
}

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $sql = "DELETE FROM wholeseller WHERE wholeseller_id = '$delete_id'";
    if ($conn->query($sql)) {
        echo "<script>alert('Wholesaler deleted successfully!');</script>";
        echo "<script>window.location.href='Wholesaler_list.php';</script>";
    } else {
        echo "<script>alert('Error deleting wholesaler: " . $conn->error . "');</script>";
    }
}

// Set up edit mode if edit_id is provided
$editMode = false;
$editData = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $conn->real_escape_string($_GET['edit_id']);
    $result = $conn->query("SELECT * FROM wholeseller WHERE wholeseller_id = '$edit_id'");
    if ($result->num_rows > 0) {
        $editData = $result->fetch_assoc();
        $editMode = true;
    }
}

// Calculate statistics
$totalWholesellers = 0;
$citiesQuery = $conn->query("SELECT COUNT(DISTINCT city) as city_count, COUNT(*) as wholeseller_count FROM wholeseller");
if ($citiesQuery->num_rows > 0) {
    $stats = $citiesQuery->fetch_assoc();
    $totalWholesellers = $stats['wholeseller_count'];
    $activeCities = $stats['city_count'];
} else {
    $activeCities = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wholesaler Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
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

        .hero-section {
            background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(76, 175, 80, 0.8)),
                        url('https://images.unsplash.com/photo-1586528116318-ad696d3adc7b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            height: 250px;
            border-radius: 15px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section h1 {
            color: white;
            font-size: 2.8em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            z-index: 2;
        }

        .stats-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 25px 0;
            display: flex;
            align-items: center;
            gap: 20px;
            border-left: 5px solid #4CAF50;
        }

        .stats-icon {
            font-size: 28px;
            background: #4CAF50;
            color: white;
            padding: 18px;
            border-radius: 50%;
        }

        .form-container {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            background: #4CAF50;
            color: white;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .edit-btn {
            background: #2E7D32;
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .delete-btn {
            background: #e74c3c;
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        button {
            background: #4CAF50;
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

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }

        input {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.2);
        }

        .decorative-icons {
            position: absolute;
            opacity: 0.1;
            font-size: 100px;
            color: white;
        }

        .nav-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .next-page-btn {
            background: #4CAF50;
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
            text-decoration: none;
            display: inline-block;
        }

        .next-page-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero-section">
            <i class="fas fa-boxes decorative-icons icon-left"></i>
            <h1>📦 Wholesaler Management System</h1>
            <i class="fas fa-pallet decorative-icons icon-right"></i>
        </div>

        <div class="stats-card">
            <i class="fas fa-chart-bar stats-icon"></i>
            <div>
                <h3>Wholesaler Statistics</h3>
                <p>Total Wholesalers: <span id="totalWholesellers"><?php echo $totalWholesellers; ?></span></p>
                <p>Active Cities: <span id="activeCities"><?php echo $activeCities; ?></span></p>
            </div>
        </div>

        <div class="form-container">
            <form method="POST" action="">
                <?php if ($editMode): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editData['wholeseller_id']; ?>">
                <?php endif; ?>
                <div class="form-grid">
                    <div class="input-group">
                        <label><i class="fas fa-barcode"></i>Wholesaler ID</label>
                        <input type="text" name="wholesellerId" value="<?php echo $editMode ? $editData['wholeseller_id'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-building"></i>Name</label>
                        <input type="text" name="name" value="<?php echo $editMode ? $editData['name'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-phone-volume"></i>Contact</label>
                        <input type="tel" name="contact" value="<?php echo $editMode ? $editData['contact'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-home"></i>House</label>
                        <input type="text" name="house" value="<?php echo $editMode ? $editData['house'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-road"></i>Road</label>
                        <input type="text" name="road" value="<?php echo $editMode ? $editData['road'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-globe"></i>Area</label>
                        <input type="text" name="area" value="<?php echo $editMode ? $editData['area'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-map-marked"></i>District</label>
                        <input type="text" name="city" value="<?php echo $editMode ? $editData['city'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-globe-europe"></i>Country</label>
                        <input type="text" name="country" value="<?php echo $editMode ? $editData['country'] : ''; ?>" required>
                    </div>
                </div>
                <div class="btn-container">
                    <?php if ($editMode): ?>
                        <button type="submit" name="update_wholeseller"><i class="fas fa-edit"></i>Update Wholesaler</button>
                    <?php else: ?>
                        <button type="submit"><i class="fas fa-warehouse"></i>Submit Wholesaler Data</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="nav-buttons">
            <a href="Wholesaler_list.php" class="next-page-btn"><i class="fas fa-arrow-right"></i> Next Page</a>
        </div>
    </div>
</body>
</html>