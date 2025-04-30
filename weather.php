<?php
// Database connection
$servername = "localhost"; // Database server
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "agriculture"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert weather data into database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $station = $_POST['station'];
    // If fields are empty, set them to NULL
    $jan = !empty($_POST['jan']) ? $_POST['jan'] : NULL;
    $feb = !empty($_POST['feb']) ? $_POST['feb'] : NULL;
    $mar = !empty($_POST['mar']) ? $_POST['mar'] : NULL;
    $apr = !empty($_POST['apr']) ? $_POST['apr'] : NULL;
    $may = !empty($_POST['may']) ? $_POST['may'] : NULL;
    $jun = !empty($_POST['jun']) ? $_POST['jun'] : NULL;
    $jul = !empty($_POST['jul']) ? $_POST['jul'] : NULL;
    $aug = !empty($_POST['aug']) ? $_POST['aug'] : NULL;
    $sep = !empty($_POST['sep']) ? $_POST['sep'] : NULL;
    $oct = !empty($_POST['oct']) ? $_POST['oct'] : NULL;
    $nov = !empty($_POST['nov']) ? $_POST['nov'] : NULL;
    $decm = !empty($_POST['decm']) ? $_POST['decm'] : NULL;

    $sql = "INSERT INTO weather_info (station, Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Decm) 
            VALUES ('$station', '$jan', '$feb', '$mar', '$apr', '$may', '$jun', '$jul', '$aug', '$sep', '$oct', '$nov', '$decm')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>New record created successfully</p>";
    } else {
        echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Info</title>
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

        .button-container {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .button-container button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .button-container button:hover {
            background-color: #45a049;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 30px;
            width: 500px;
        }

        form label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        form input[type="text"], form input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <!-- Button to View Weather Info -->
    <div class="button-container">
        <a href="weather_Table.php"><button>View Weather Info</button></a>
    </div>

    <h2>Admin Panel: Add Weather Information</h2>

    <form method="POST" action="">
        <label for="station">Station:</label>
        <input type="text" name="station" required><br><br>
        <label for="jan">January:</label>
        <input type="number" name="jan" step="0.01"><br><br>
        <label for="feb">February:</label>
        <input type="number" name="feb" step="0.01"><br><br>
        <label for="mar">March:</label>
        <input type="number" name="mar" step="0.01"><br><br>
        <label for="apr">April:</label>
        <input type="number" name="apr" step="0.01"><br><br>
        <label for="may">May:</label>
        <input type="number" name="may" step="0.01"><br><br>
        <label for="jun">June:</label>
        <input type="number" name="jun" step="0.01"><br><br>
        <label for="jul">July:</label>
        <input type="number" name="jul" step="0.01"><br><br>
        <label for="aug">August:</label>
        <input type="number" name="aug" step="0.01"><br><br>
        <label for="sep">September:</label>
        <input type="number" name="sep" step="0.01"><br><br>
        <label for="oct">October:</label>
        <input type="number" name="oct" step="0.01"><br><br>
        <label for="nov">November:</label>
        <input type="number" name="nov" step="0.01"><br><br>
        <label for="dec">December:</label>
        <input type="number" name="decm" step="0.01"><br><br>
        <input type="submit" value="Submit">
    </form>

</body>
</html>

<?php
$conn->close();
?>
