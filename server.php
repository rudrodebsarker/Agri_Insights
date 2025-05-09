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

        // Store success message
        $_SESSION['success'] = "Registration successful! You can now log in.";

        // Redirect to the login page
        header('location: login.php'); // Redirect to login page
        exit(); // Ensure no further code is executed
    }
}
?>
