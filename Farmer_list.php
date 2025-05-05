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
// Delete Farmer Logic
if(isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM farmer WHERE farmer_id = $delete_id");
    header("Location: Farmer_list.php");
}

// Filter functionality
$filterQuery = "";
if(isset($_GET['filter_id']) && !empty($_GET['filter_id'])) {
    $filter_id = intval($_GET['filter_id']);
    $filterQuery = " WHERE farmer_id = $filter_id";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Farmer Table</title>
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
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        button:hover {
            background: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
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
            justify-content: space-between;
            margin-top: 20px;
        }
        .nav-buttons a {
            text-decoration: none;
        }
        .filter-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
        }
        .filter-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filter-btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .clear-filter {
            background: #f5f5f5;
            color: #333;
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .clear-filter:hover {
            background: #e5e5e5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registered Farmers</h2>

        <form class="filter-form" method="GET" action="">
            <input type="text" name="filter_id" placeholder="Filter by Farmer ID" class="filter-input" value="<?php echo isset($_GET['filter_id']) ? htmlspecialchars($_GET['filter_id']) : ''; ?>">
            <button type="submit" class="filter-btn">Filter</button>
            <?php if(isset($_GET['filter_id']) && !empty($_GET['filter_id'])): ?>
                <a href="Farmer_list.php" class="clear-filter">Clear</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Experience</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM farmer" . $filterQuery . " ORDER BY farmer_id DESC";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['farmer_id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td>
                        <?= htmlspecialchars($row['road']) ?>, 
                        <?= htmlspecialchars($row['house']) ?><br>
                        <?= htmlspecialchars($row['area']) ?>, 
                        <?= htmlspecialchars($row['district']) ?><br>
                        <?= htmlspecialchars($row['country']) ?>
                    </td>
                    <td><?= $row['years_of_experience'] ?> years</td>
                    <td class="action-links">
                        <a href="edit_farmer.php?id=<?= $row['farmer_id'] ?>">Edit</a>
                        <a href="?delete_id=<?= $row['farmer_id'] ?>" 
                           onclick="return confirm('Are you sure you want to delete this farmer?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="nav-buttons">
            <a href="Farmer.php"><button>Back to Form</button></a>
        </div>
    </div>
</body>
</html>
