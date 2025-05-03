<?php
// Connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'agriculture');

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch agri officer data with their contacts and varieties
$sql = "SELECT ao.officer_id, ao.name, ao.email, ao.road, ao.area, ao.district, ao.country,
               GROUP_CONCAT(aoc.contact SEPARATOR ', ') AS contacts,
               GROUP_CONCAT(apv.variety SEPARATOR ', ') AS varieties
        FROM agri_officer ao
        LEFT JOIN agri_officer_contact aoc ON ao.officer_id = aoc.officer_id
        LEFT JOIN agri_product_variety apv ON ao.officer_id = apv.product_id
        GROUP BY ao.officer_id";

$result = mysqli_query($conn, $sql);

// Close the connection later after HTML
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Agri Officer List</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background-color: #f4f7fc;
      padding: 20px;
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      overflow: hidden;
    }
    th, td {
      padding: 15px 20px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #4CAF50;
      color: white;
      font-weight: 600;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    .table-container {
      margin-top: 30px;
      overflow-x: auto;
    }
  </style>
</head>

<body>

  <h1>Agri Officer List</h1>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Officer ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Road</th>
          <th>Area</th>
          <th>District</th>
          <th>Country</th>
          <th>Contact Numbers</th>
          <th>Varieties</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['officer_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['road']) . "</td>";
                echo "<td>" . htmlspecialchars($row['area']) . "</td>";
                echo "<td>" . htmlspecialchars($row['district']) . "</td>";
                echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                echo "<td>" . htmlspecialchars($row['contacts']) . "</td>";
                echo "<td>" . htmlspecialchars($row['varieties']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9' style='text-align:center;'>No Officers Found</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

</body>
</html>

<?php
mysqli_close($conn);
?>
