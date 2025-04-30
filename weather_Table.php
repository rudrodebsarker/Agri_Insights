<?php
// Database connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "agriculture"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search functionality
$search = '';
if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $sql = "SELECT * FROM weather_info WHERE station LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM weather_info";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #333;
            margin-top: 20px;
        }

        .search-form {
            margin-top: 20px;
        }

        .search-form input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-form button {
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #45a049;
        }

        table {
            width: 80%;
            margin-top: 20px;
            border-collapse: collapse;
            text-align: center;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

    <h2>Weather Data Table</h2>

    <div class="search-form">
        <form method="POST" action="">
            <label for="search">Search by Station:</label>
            <input type="text" name="search" value="<?php echo $search; ?>" required>
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Station</th>
            <th>Jan</th>
            <th>Feb</th>
            <th>Mar</th>
            <th>Apr</th>
            <th>May</th>
            <th>Jun</th>
            <th>Jul</th>
            <th>Aug</th>
            <th>Sep</th>
            <th>Oct</th>
            <th>Nov</th>
            <th>Dec</th>
            <th>Total</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Calculate total
                $total = $row['Jan'] + $row['Feb'] + $row['Mar'] + $row['Apr'] + $row['May'] + $row['Jun'] + $row['Jul'] + $row['Aug'] + $row['Sep'] + $row['Oct'] + $row['Nov'] + $row['Decm'];
                echo "<tr>
                        <td>".$row['station']."</td>
                        <td>".$row['Jan']."</td>
                        <td>".$row['Feb']."</td>
                        <td>".$row['Mar']."</td>
                        <td>".$row['Apr']."</td>
                        <td>".$row['May']."</td>
                        <td>".$row['Jun']."</td>
                        <td>".$row['Jul']."</td>
                        <td>".$row['Aug']."</td>
                        <td>".$row['Sep']."</td>
                        <td>".$row['Oct']."</td>
                        <td>".$row['Nov']."</td>
                        <td>".$row['Decm']."</td>
                        <td>$total</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='14' class='no-data'>No data available</td></tr>";
        }
        ?>
    </table>

</body>
</html>

<?php
$conn->close();
?>
