<?php 
// Include the server.php file where the database connection is made
include('server.php');

// Initialize variables
$username = "";
$password = "";
$errors = array();

// LOGIN USER
if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $user_type = mysqli_real_escape_string($db, $_POST['user_type']); // Get the selected user type

    if (empty($username)) { array_push($errors, "Username is required"); }
    if (empty($password)) { array_push($errors, "Password is required"); }

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
                header('location: admin_dashboard.php');
            } elseif ($user['user_type'] == 'Farmer') {
                header('location: farmer_dashboard.php');
            } elseif ($user['user_type'] == 'Agricultural_Officer') {
                header('location: agriOfficer_dashboard.php');
            } elseif ($user['user_type'] == 'Retailer') {
                header('location: retailer_dashboard.php');
            } elseif ($user['user_type'] == 'Wholesaler') {
                header('location: wholesaler_dashboard.php');
            } elseif ($user['user_type'] == 'Consumer') {
                header('location: consumer_dashboard.php');
            }elseif ($user['user_type'] == 'Warehouse_manager') {
              header('location:Warehouse_manager_dashboard.php');
          } else {
                header('location: user_dashboard.php');
            }
        } else {
            array_push($errors, "Wrong username/password combination or invalid user type");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Demand & Supply Analysis</title>
    <style>
        /* Reset Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #ecefec;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('images/pic4.avif'); /* Use your background image */
            background-size: cover;
            background-position: center;
        }

        .login-box {
            background-color: rgba(223, 239, 214, 0.9); /* White with slight transparency */
            width: 350px;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .input-group input, .input-group select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .input-group input:focus, .input-group select:focus {
            border-color: #28a745;
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #218838;
        }

        p {
            text-align: center;
            font-size: 14px;
        }

        p a {
            color: #3498db;
            font-weight: bold;
        }

        p a:hover {
            text-decoration: underline;
        }

        /* Error message styling */
        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Sign In</h2>
        <form method="post" action="login.php">
            <?php
            if (count($errors) > 0) {
                echo '<div class="error">';
                foreach ($errors as $error) {
                    echo $error . "<br>";
                }
                echo '</div>';
            }
            ?>

            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="input-group">
                <label for="user_type">User Type</label>
                <select name="user_type" id="user_type" required>
                    <option value="Farmer">Farmer</option>
                    <option value="AgrOfficer">Agricultural Officer</option>
                    <option value="Admin">Admin</option>
                    <option value="Retailer">Retailer</option>
                    <option value="Wholesaler">Wholesaler</option>
                    <option value="Consumer">Consumer</option>
                    <option value="Warehouse_manager">Warehouse_manager</option>
                </select>
            </div>

            <div class="input-group">
                <button type="submit" class="btn" name="login_user">Login</button>
            </div>

            <p>
                Not yet a member? <a href="register.php">Sign up</a>
            </p>
        </form>
    </div>
</body>
</html>
