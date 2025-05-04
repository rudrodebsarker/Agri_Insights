<?php
$conn = mysqli_connect('localhost', 'root', '', 'agriculture');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $sql_delete = "DELETE FROM WAREHOUSE WHERE warehouse_id = '$delete_id'";
    if ($conn->query($sql_delete) === TRUE) {
        echo "Warehouse deleted successfully.";
    } else {
        echo "Error: " . $sql_delete . "<br>" . $conn->error;
    }
   
    header("Location: warehouse_management.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $warehouse_id = $_POST['warehouse_id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $contact_num = $_POST['contact_num'];
    $available_stock = $_POST['available_stock_of_product'];
    $last_updated = $_POST['last_updated'];

    // Update warehouse information
    $sql = "UPDATE WAREHOUSE SET name='$name', location='$location', contact_num='$contact_num', available_stock_of_product='$available_stock', last_updated='$last_updated' WHERE warehouse_id='$warehouse_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Warehouse updated successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
