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

// Check if the old table name exists
$tableCheckOld = $conn->query("SHOW TABLES LIKE 'wholeseller'");
$tableCheckNew = $conn->query("SHOW TABLES LIKE 'wholesaler'");

if ($tableCheckOld->num_rows > 0 && $tableCheckNew->num_rows > 0) {
    // Both tables exist, we need to drop the old one
    $drop = $conn->query("DROP TABLE wholeseller");
    if (!$drop) {
        // Just log error, don't die
        echo "<script>console.error('Warning: Could not drop old table: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Old wholeseller table dropped successfully');</script>";
    }
} else if ($tableCheckOld->num_rows > 0) {
    // Only old table exists, rename it
    $rename = $conn->query("RENAME TABLE wholeseller TO wholesaler");
    if (!$rename) {
        echo "<script>console.error('Warning: Could not rename table: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Table renamed successfully from wholeseller to wholesaler');</script>";
    }
}

// Check if the city column exists and rename it to district
$columnCheck = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'city'");
$districtCheck = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'district'");
$houseCheck = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'house'");

if ($columnCheck->num_rows > 0) {
    // Only proceed if district column doesn't already exist
    if ($districtCheck->num_rows == 0) {
        // The city column exists, rename it to district
        $alterTable = $conn->query("ALTER TABLE wholesaler CHANGE COLUMN city district VARCHAR(100) NOT NULL");
        if (!$alterTable) {
            echo "<script>console.error('Warning: Could not rename column: " . $conn->error . "');</script>";
        } else {
            echo "<script>console.log('Column renamed successfully from city to district');</script>";
        }
    } else {
        // Both columns exist, just drop city column
        $dropColumn = $conn->query("ALTER TABLE wholesaler DROP COLUMN city");
        if (!$dropColumn) {
            echo "<script>console.error('Warning: Could not drop city column: " . $conn->error . "');</script>";
        } else {
            echo "<script>console.log('Dropped redundant city column');</script>";
        }
    }
}

// Check for all required columns and add them if missing
$roadCheck = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'road'");
$areaCheck = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'area'");
$countryCheck = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'country'");

// Check if house column exists and add it if not
if ($houseCheck->num_rows == 0) {
    $addHouseColumn = $conn->query("ALTER TABLE wholesaler ADD COLUMN house VARCHAR(100) NOT NULL DEFAULT '' AFTER contact");
    if (!$addHouseColumn) {
        echo "<script>console.error('Warning: Could not add house column: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Added missing house column');</script>";
    }
}

// Check if road column exists and add it if not
if ($roadCheck->num_rows == 0) {
    $addRoadColumn = $conn->query("ALTER TABLE wholesaler ADD COLUMN road VARCHAR(100) NOT NULL DEFAULT '' AFTER house");
    if (!$addRoadColumn) {
        echo "<script>console.error('Warning: Could not add road column: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Added missing road column');</script>";
    }
}

// Check if area column exists and add it if not
if ($areaCheck->num_rows == 0) {
    $addAreaColumn = $conn->query("ALTER TABLE wholesaler ADD COLUMN area VARCHAR(100) NOT NULL DEFAULT '' AFTER road");
    if (!$addAreaColumn) {
        echo "<script>console.error('Warning: Could not add area column: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Added missing area column');</script>";
    }
}

// Check if country column exists and add it if not
if ($countryCheck->num_rows == 0) {
    $addCountryColumn = $conn->query("ALTER TABLE wholesaler ADD COLUMN country VARCHAR(100) NOT NULL DEFAULT '' AFTER district");
    if (!$addCountryColumn) {
        echo "<script>console.error('Warning: Could not add country column: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Added missing country column');</script>";
    }
}

// Create the wholesaler table if it doesn't exist
if ($tableCheckNew->num_rows == 0) {
    $sql = "CREATE TABLE IF NOT EXISTS wholesaler (
        wholesaler_id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        contact VARCHAR(50) NOT NULL,
        house VARCHAR(100) NOT NULL,
        road VARCHAR(100) NOT NULL,
        area VARCHAR(100) NOT NULL,
        district VARCHAR(100) NOT NULL,
        country VARCHAR(100) NOT NULL
    )";
    if (!$conn->query($sql)) {
        die("Error creating table: " . $conn->error);
    }
}

// Handle form submission for adding or updating wholesaler
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['update_wholesaler']) && isset($_POST['edit_id'])) {
        // Update existing wholesaler
        $wholesaler_id = $conn->real_escape_string($_POST['wholesalerId']);
        $name = $conn->real_escape_string($_POST['name']);
        $contact = $conn->real_escape_string($_POST['contact']);
        $house = $conn->real_escape_string($_POST['house']);
        $road = $conn->real_escape_string($_POST['road']);
        $area = $conn->real_escape_string($_POST['area']);
        $district = $conn->real_escape_string($_POST['city']);
        $country = $conn->real_escape_string($_POST['country']);
        $edit_id = $conn->real_escape_string($_POST['edit_id']);

        // Build dynamic UPDATE query based on existing columns
        $updates = array();
        
        $updates[] = "wholesaler_id = '$wholesaler_id'";
        $updates[] = "name = '$name'";
        $updates[] = "contact = '$contact'";
        
        // Check if other columns exist before including them
        $houseExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'house'")->num_rows > 0;
        if ($houseExists) {
            $updates[] = "house = '$house'";
        }
        
        $roadExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'road'")->num_rows > 0;
        if ($roadExists) {
            $updates[] = "road = '$road'";
        }
        
        $areaExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'area'")->num_rows > 0;
        if ($areaExists) {
            $updates[] = "area = '$area'";
        }
        
        $districtExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'district'")->num_rows > 0;
        if ($districtExists) {
            $updates[] = "district = '$district'";
        } else {
            // For backward compatibility, use city if district doesn't exist
            $cityExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'city'")->num_rows > 0;
            if ($cityExists) {
                $updates[] = "city = '$district'"; // Store district value in city column
            }
        }
        
        $countryExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'country'")->num_rows > 0;
        if ($countryExists) {
            $updates[] = "country = '$country'";
        }
        
        $sql = "UPDATE wholesaler SET " . implode(", ", $updates) . " WHERE wholesaler_id = '$edit_id'";

        if ($conn->query($sql)) {
            echo "<script>alert('Wholesaler updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating wholesaler: " . $conn->error . "');</script>";
        }
    } else {
        // Add new wholesaler
        $wholesaler_id = $conn->real_escape_string($_POST['wholesalerId']);
        $name = $conn->real_escape_string($_POST['name']);
        $contact = $conn->real_escape_string($_POST['contact']);
        $house = $conn->real_escape_string($_POST['house']);
        $road = $conn->real_escape_string($_POST['road']);
        $area = $conn->real_escape_string($_POST['area']);
        $district = $conn->real_escape_string($_POST['city']);
        $country = $conn->real_escape_string($_POST['country']);

        // Check if wholesaler ID already exists
        $check = $conn->query("SELECT wholesaler_id FROM wholesaler WHERE wholesaler_id = '$wholesaler_id'");
        if ($check->num_rows > 0) {
            echo "<script>alert('Wholesaler ID already exists!');</script>";
        } else {
            // Construct a dynamic INSERT query based on existing columns
            $columns = array();
            $values = array();
            
            // Always include these required fields
            $columns[] = "wholesaler_id";
            $values[] = "'$wholesaler_id'";
            
            $columns[] = "name";
            $values[] = "'$name'";
            
            $columns[] = "contact";
            $values[] = "'$contact'";
            
            // Check if other columns exist before including them
            $houseExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'house'")->num_rows > 0;
            if ($houseExists) {
                $columns[] = "house";
                $values[] = "'$house'";
            }
            
            $roadExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'road'")->num_rows > 0;
            if ($roadExists) {
                $columns[] = "road";
                $values[] = "'$road'";
            }
            
            $areaExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'area'")->num_rows > 0;
            if ($areaExists) {
                $columns[] = "area";
                $values[] = "'$area'";
            }
            
            $districtExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'district'")->num_rows > 0;
            if ($districtExists) {
                $columns[] = "district";
                $values[] = "'$district'";
            } else {
                // For backward compatibility, try with city if district doesn't exist
                $cityExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'city'")->num_rows > 0;
                if ($cityExists) {
                    $columns[] = "city";
                    $values[] = "'$district'"; // We're still storing the district value here
                }
            }
            
            $countryExists = $conn->query("SHOW COLUMNS FROM wholesaler LIKE 'country'")->num_rows > 0;
            if ($countryExists) {
                $columns[] = "country";
                $values[] = "'$country'";
            }
            
            $sql = "INSERT INTO wholesaler (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";

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
    $sql = "DELETE FROM wholesaler WHERE wholesaler_id = '$delete_id'";
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
    $result = $conn->query("SELECT * FROM wholesaler WHERE wholesaler_id = '$edit_id'");
    if ($result->num_rows > 0) {
        $editData = $result->fetch_assoc();
        $editMode = true;
    }
}

// Calculate statistics
$totalWholesalers = 0;
// First check if wholesaler table exists and count total records
$result = $conn->query("SELECT COUNT(*) as wholesaler_count FROM wholesaler");
if ($result && $result->num_rows > 0) {
    $stats = $result->fetch_assoc();
    $totalWholesalers = $stats['wholesaler_count'];
    
    // Get the count of distinct districts
    $districtResult = $conn->query("SELECT COUNT(DISTINCT district) as district_count FROM wholesaler");
    if ($districtResult && $districtResult->num_rows > 0) {
        $districtStats = $districtResult->fetch_assoc();
        $activeCities = $districtStats['district_count'];
    } else {
        $activeCities = 0;
    }
} else {
    $totalWholesalers = 0;
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
                <p>Total Wholesalers: <span id="totalWholesalers"><?php echo $totalWholesalers; ?></span></p>
                <p>Active Districts: <span id="activeCities"><?php echo $activeCities; ?></span></p>
            </div>
        </div>

        <div class="form-container">
            <form method="POST" action="">
                <?php if ($editMode): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editData['wholesaler_id']; ?>">
                <?php endif; ?>
                <div class="form-grid">
                    <div class="input-group">
                        <label><i class="fas fa-barcode"></i>Wholesaler ID</label>
                        <input type="text" name="wholesalerId" value="<?php echo $editMode ? $editData['wholesaler_id'] : ''; ?>" required>
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
                        <input type="text" name="city" value="<?php echo $editMode ? $editData['district'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-globe-europe"></i>Country</label>
                        <input type="text" name="country" value="<?php echo $editMode ? $editData['country'] : ''; ?>" required>
                    </div>
                </div>
                <div class="btn-container">
                    <?php if ($editMode): ?>
                        <button type="submit" name="update_wholesaler"><i class="fas fa-edit"></i>Update Wholesaler</button>
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
