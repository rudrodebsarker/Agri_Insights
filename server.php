<?php
session_start();

// initializing variables
$username = "";
$email    = "";
$user_type = "Admin";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'agriculture');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
  $user_type = mysqli_real_escape_string($db, $_POST['user_type']); // New user type field

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
    array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "Email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
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
    } elseif ($user_type == 'AgrOfficer') {
        header('location: agrofficer_dashboard.php');
        
    }elseif ($user_type == 'Wholesaler') {
      header('location: wholesaler_dashboard.php');
      
  }elseif ($user_type == 'Retailer') {
    header('location: retailer_dashboard.php');
    
}elseif ($user_type == 'Consumer') {
  header('location: consumer_dashboard.php.php');
  
}  else {
        header('location: user_dashboard.php');
    }
  }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  $user_type = mysqli_real_escape_string($db, $_POST['user_type']); // Get the selected user type

  if (empty($username)) {
    array_push($errors, "Username is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
    $password = md5($password); // Encrypt password
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password' AND user_type='$user_type'";
    $results = mysqli_query($db, $query);

    if (mysqli_num_rows($results) == 1) {
      $user = mysqli_fetch_assoc($results);
      $_SESSION['username'] = $user['username'];
      $_SESSION['user_type'] = $user['user_type']; // Store user type in session
      $_SESSION['success'] = "You are now logged in";

      // Redirect to the appropriate dashboard based on user type
      if ($user['user_type'] == 'Admin') {
        header('location:admin_dashboard.php');
      } elseif ($user['user_type'] == 'Farmer') {
        header('location: farmer_dashboard.php');
      } elseif ($user['user_type'] == 'AgrOfficer') {
        header('location: agrofficer_dashboard.php');
      } elseif ($user['user_type'] == 'Retailer') {
        header('location: retailer_dashboard.php');
      } elseif ($user['user_type'] == 'Wholesaler') {
        header('location: wholesaler_dashboard.php');
      } elseif ($user['user_type'] == 'Consumer') {
        header('location: consumer_dashboard.php');
      } else {
        header('location: user_dashboard.php');
      }
    } else {
      array_push($errors, "Wrong username/password combination or invalid user type");
    }
  }
}
