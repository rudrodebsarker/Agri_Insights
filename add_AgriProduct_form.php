<?php
// Start the session
session_start();

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'agriculture');

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form is submitted
if (isset($_POST['add_product'])) {
    // Sanitize and retrieve form data
    $product_id = mysqli_real_escape_string($db, $_POST['product_id']);
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $seasonality = mysqli_real_escape_string($db, $_POST['seasonality']);
    $type = mysqli_real_escape_string($db, $_POST['type']);

    // Insert the product into the agri_product table
    $sql_product = "INSERT INTO agri_product (product_id, name, seasonality, type) 
                    VALUES ('$product_id', '$name', '$seasonality', '$type')";

    if (mysqli_query($db, $sql_product)) {
        // Ensure product_id exists in agri_product table (check foreign key relationship)
        $check_product = "SELECT product_id FROM agri_product WHERE product_id = '$product_id'";
        $result = mysqli_query($db, $check_product);
        
        if (mysqli_num_rows($result) > 0) {
            // Insert the varieties into the agri_product_variety table
            if (!empty($_POST['varieties'])) {
                foreach ($_POST['varieties'] as $variety) {
                    // Sanitize each variety input
                    $variety = mysqli_real_escape_string($db, $variety);

                    // Insert each variety into the agri_product_variety table
                    $sql_variety = "INSERT INTO agri_product_variety (product_id, variety) 
                                    VALUES ('$product_id', '$variety')";

                    if (!mysqli_query($db, $sql_variety)) {
                        echo "Error inserting variety: " . mysqli_error($db) . "<br>";
                        exit;  // Stop execution on error
                    }
                }
            } else {
                echo "No varieties provided.<br>";
            }

            // Redirect to the product list page after successful insertion
            header("Location: agriProduct_list.php");
            exit;
        } else {
            echo "Product ID does not exist in agri_product table. Please check the product details.<br>";
        }
    } else {
        echo "Error inserting product: " . mysqli_error($db) . "<br>";
    }
}

// Close the database connection
mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Agri Product</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 600px;
    }

    h1 {
      margin-bottom: 20px;
      text-align: center;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input {
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }

    button {
      padding: 10px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 10px;
    }

    button.back {
      background-color: #007bff;
    }

    .variety-input {
      margin-bottom: 10px;
    }

    #add-variety {
      padding: 12px 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
      font-size: 14px;
      text-align: center;
      width: auto;
      transition: background-color 0.3s ease, transform 0.3s ease;
      align-self: flex-start;
    }

    #add-variety:hover {
      background-color: #0056b3;
      transform: scale(1.05);
    }

    #add-variety:focus {
      outline: none;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    #add-variety span {
      font-size: 18px;
      margin-right: 5px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Add Agri Product</h1>
    <form method="POST" action="">
      <input type="text" name="product_id" placeholder="Product ID" required />
      <input type="text" name="name" placeholder="Product Name" required />
      <input type="text" name="seasonality" placeholder="Seasonality" required />
      <input type="text" name="type" placeholder="Type" required />

      <!-- Multiple varieties -->
      <div id="variety-fields">
        <div class="variety-input">
          <input type="text" name="varieties[]" placeholder="Variety 1" required />
        </div>
      </div>

      <!-- Button to add more variety inputs -->
      <button type="button" id="add-variety">
        <span>+</span> Add More Varieties
      </button>

      <button type="submit" name="add_product">Add Product</button>
      <a href="agriProduct_list.php"><button class="back" type="button">Back</button></a>
    </form>
  </div>

  <script>
    // JavaScript to add more variety fields dynamically
    document.getElementById('add-variety').addEventListener('click', function() {
      var newVarietyField = document.createElement('div');
      newVarietyField.classList.add('variety-input');
      newVarietyField.innerHTML = '<input type="text" name="varieties[]" placeholder="Variety" />'; 
      document.getElementById('variety-fields').appendChild(newVarietyField);
    });
  </script>
</body>
</html>
