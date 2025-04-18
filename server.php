<?php
session_start();

// Initialize variables
$username = "";
$email    = "";
$user_type = "Admin"; // Default value
$errors = array();

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'agriculture');



// REGISTER USER
if (isset($_POST['reg_user'])) {
    // Receive all input values from the form
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
    $user_type = mysqli_real_escape_string($db, $_POST['user_type']); // New user type field

    // Form validation
    if (empty($username)) { array_push($errors, "Username is required"); }
    if (empty($email)) { array_push($errors, "Email is required"); }
    if (empty($password_1)) { array_push($errors, "Password is required"); }
    if ($password_1 != $password_2) {
        array_push($errors, "The two passwords do not match");
    }

    // Check if the username or email already exists in the database
    $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) { // If user exists
        if ($user['username'] === $username) {
            array_push($errors, "Username already exists");
        }
        if ($user['email'] === $email) {
            array_push($errors, "Email already exists");
        }
    }

    // Register user if there are no errors in the form
    if (count($errors) == 0) {
        $password = md5($password_1); // Encrypt the password before saving in the database

        // Insert into the database including user type
        $query = "INSERT INTO users (username, email, password, user_type) 
                  VALUES('$username', '$email', '$password', '$user_type')";
        mysqli_query($db, $query);

        $_SESSION['username'] = $username;
        $_SESSION['user_type'] = $user_type; // Store user type in session
        $_SESSION['success'] = "You are now logged in";

        // Redirect to the appropriate dashboard based on user type
        if ($user_type == 'Admin') {
            header('location: admin_dashboard.php');
        } elseif ($user_type == 'Farmer') {
            header('location: farmer_dashboard.php');
        } elseif ($user_type == 'Agricultural_Officer') {
            header('location: agriOfficer_dashboard.php');
        } elseif ($user_type == 'Wholesaler') {
            header('location: wholesaler_dashboard.php');
        } elseif ($user_type == 'Retailer') {
            header('location: retailer_dashboard.php');
        } elseif ($user_type == 'Consumer') {
            header('location: consumer_dashboard.php');
        }elseif ($user_type == 'Warehouse_manager') {
          header('location: Warehouse_manager_dashboard.php');
      }
         else {
            header('location: user_dashboard.php');
        }
    }
}
?>
