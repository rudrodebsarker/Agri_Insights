<?php
// Start session (only if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$db = mysqli_connect('localhost', 'root', '', 'agriculture');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get products for display and feedback dropdown
$products_query = "SELECT * FROM agri_product";
$products_result = mysqli_query($db, $products_query);
$products = mysqli_fetch_all($products_result, MYSQLI_ASSOC);

// Initialize variables
$consumer_data = null;
$current_section = isset($_GET['section']) ? $_GET['section'] : 'products';

// Handle consumer ID search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consumer_id'])) {
    $current_section = 'profile';
    $consumer_id = (int)$_POST['consumer_id'];
    $consumer_query = "SELECT * FROM consumer WHERE consumer_id = $consumer_id";
    $consumer_result = mysqli_query($db, $consumer_query);
    $consumer_data = mysqli_fetch_assoc($consumer_result);
    
    if (!$consumer_data) {
        $_SESSION['profile_error'] = "No consumer found with ID: $consumer_id";
    }
}

// Feedback Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_text'])) {
    $current_section = 'feedback';
    $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $feedback_text = trim($_POST['feedback_text']);
    
    // Validation
    if (empty($feedback_text)) {
        $_SESSION['feedback_error'] = "Feedback text cannot be empty";
    } elseif (strlen($feedback_text) > 1000) {
        $_SESSION['feedback_error'] = "Feedback is too long (max 1000 characters)";
    } else {
        // Get product name if product_id is selected
        $product_name = 'General Feedback';
        if ($product_id) {
            foreach ($products as $product) {
                if ($product['product_id'] == $product_id) {
                    $product_name = $product['name'];
                    break;
                }
            }
        }
        
        // Store feedback in JSON file
        $feedback_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'product_id' => $product_id,
            'product_name' => $product_name,
            'feedback' => $feedback_text
        ];
        
        $file = 'feedback_data.json';
        $all_feedback = [];
        
        if (file_exists($file)) {
            $all_feedback = json_decode(file_get_contents($file), true);
            if (!is_array($all_feedback)) {
                $all_feedback = [];
            }
        }
        
        $all_feedback[] = $feedback_data;
        file_put_contents($file, json_encode($all_feedback, JSON_PRETTY_PRINT));
        
        $_SESSION['feedback_success'] = "Thank you for your feedback!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agricultural Products Marketplace</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
        }

        .sidenav {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
        }

        .sidenav h2 {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        .sidenav-menu {
            display: flex;
            flex-direction: column;
        }

        .sidenav-menu button {
            background: none;
            border: none;
            color: white;
            text-align: left;
            padding: 15px 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .sidenav-menu button:hover {
            background-color: #34495e;
        }

        .sidenav-menu button.active {
            background-color: #4CAF50;
        }

        .main-content {
            flex-grow: 1;
            overflow-y: auto;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        #search-input {
            width: 60%;
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        }

        #search-input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.3);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .product-image {
            height: 180px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 1.2rem;
        }

        .product-info {
            padding: 15px;
        }

        .product-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .product-type {
            display: inline-block;
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        .product-season {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .no-results {
            text-align: center;
            grid-column: 1 / -1;
            padding: 40px;
            color: #666;
            font-size: 1.2rem;
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        .feedback-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(16, 16, 16, 0.1);
            margin-bottom: 30px;
        }

        .feedback-form textarea {
            width: 100%;
            height: 120px;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .feedback-form button {
            background-color:rgb(28, 32, 28);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .success-message {
            color: green;
            padding: 10px;
            background-color: #e8f5e9;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .error-message {
            color: #d9534f;
            padding: 10px;
            background-color: #f2dede;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .consumer-search {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .consumer-search input {
            padding: 8px;
            margin-right: 10px;
            width: 200px;
        }

        .consumer-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .profile-error {
            color: #d9534f;
            padding: 10px;
            background-color: #f2dede;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .sidenav {
                width: 100%;
                padding: 10px 0;
            }
            
            .sidenav-menu {
                flex-direction: row;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .sidenav-menu button {
                padding: 10px 15px;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
            
            #search-input {
                width: 80%;
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Side Navigation -->
    <div class="sidenav">
        <h2>Consumer Menu</h2>
        <div class="sidenav-menu">
            <button onclick="showSection('products')" class="<?= $current_section === 'products' ? 'active' : '' ?>">Products</button>
            <button onclick="showSection('profile')" class="<?= $current_section === 'profile' ? 'active' : '' ?>">Profile</button>
            <button onclick="showSection('feedback')" class="<?= $current_section === 'feedback' ? 'active' : '' ?>">Feedback</button>
            <button onclick="location.href='logout.php'">Logout</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Products Section -->
        <div id="products" class="section <?= $current_section === 'products' ? 'active' : '' ?>">
            <header>
                <div class="container">
                    <h1>Agricultural Products</h1>
                    <p>Find fresh produce from local farmers</p>
                </div>
            </header>

            <div class="container">
                <div class="search-container">
                    <input type="text" id="search-input" placeholder="Search for products (e.g., Tomato, Rice, Mango)...">
                </div>

                <div class="products-grid" id="products-container">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php 
                                    $emoji = '🌱';
                                    if ($product['type'] === 'Fruit') $emoji = '🍎';
                                    elseif ($product['type'] === 'Vegetable') $emoji = '🥦';
                                    elseif ($product['type'] === 'Cereal') $emoji = '🌾';
                                    echo $emoji . ' ' . htmlspecialchars($product['name']);
                                ?>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                                <span class="product-type"><?= htmlspecialchars($product['type']) ?></span>
                                <p class="product-season">Season: <?= htmlspecialchars($product['seasonality']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div id="profile" class="section <?= $current_section === 'profile' ? 'active' : '' ?>">
            <header>
                <div class="container">
                    <h1>Consumer Profile</h1>
                    <p>View consumer details by entering consumer ID</p>
                </div>
            </header>

            <div class="container">
                <div class="consumer-search">
                    <h2>Find Consumer</h2>
                    <?php if (isset($_SESSION['profile_error'])): ?>
                        <div class="profile-error">
                            <?= $_SESSION['profile_error'] ?>
                            <?php unset($_SESSION['profile_error']); ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="?section=profile">
                        <label for="consumer-id">Enter Consumer ID:</label>
                        <input type="number" id="consumer-id" name="consumer_id" required>
                        <button type="submit" class="btn">Search</button>
                    </form>
                </div>

                <?php if ($consumer_data): ?>
                <div class="consumer-details">
                    <h2>Consumer Details</h2>
                    <p><strong>Consumer ID:</strong> <?= htmlspecialchars($consumer_data['consumer_id']) ?></p>
                    <p><strong>Name:</strong> <?= htmlspecialchars($consumer_data['name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($consumer_data['email']) ?></p>
                    <p><strong>Contact:</strong> <?= htmlspecialchars($consumer_data['contact']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Feedback Section -->
        <div id="feedback" class="section <?= $current_section === 'feedback' ? 'active' : '' ?>">
            <header>
                <div class="container">
                    <h1>Provide Feedback</h1>
                    <p>Share your thoughts about our products</p>
                </div>
            </header>

            <div class="container">
                <div class="feedback-form">
                    <?php if (isset($_SESSION['feedback_success'])): ?>
                        <div class="success-message">
                            <?= $_SESSION['feedback_success'] ?>
                            <?php unset($_SESSION['feedback_success']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['feedback_error'])): ?>
                        <div class="error-message">
                            <?= $_SESSION['feedback_error'] ?>
                            <?php unset($_SESSION['feedback_error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <h2>Feedback Form</h2>
                    <form method="post" action="?section=feedback">
                        <div style="margin-bottom: 15px;">
                            <label for="product-select">Select Product (optional):</label>
                            <select name="product_id" id="product-select" style="width: 100%; padding: 8px; margin-top: 5px;">
                                <option value="">-- Select a product --</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['product_id'] ?>">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="feedback-text">Your Feedback:</label>
                            <textarea name="feedback_text" id="feedback-text" placeholder="Please share your feedback here..." required></textarea>
                        </div>
                        <button type="submit">Submit Feedback</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Convert PHP products array to JavaScript
        const products = <?= json_encode($products) ?>;
        const productsContainer = document.getElementById('products-container');
        const searchInput = document.getElementById('search-input');

        // Add event listener for search input
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filteredProducts = products.filter(product => 
                product.name.toLowerCase().includes(searchTerm)
            );
            
            // Clear current products
            productsContainer.innerHTML = '';
            
            if (filteredProducts.length === 0) {
                productsContainer.innerHTML = `
                    <div class="no-results">
                        <p>No products found matching your search.</p>
                    </div>
                `;
                return;
            }
            
            // Display filtered products
            filteredProducts.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                
                // Get emoji based on product type
                let emoji = '🌱';
                if (product.type === 'Fruit') emoji = '🍎';
                else if (product.type === 'Vegetable') emoji = '🥦';
                else if (product.type === 'Cereal') emoji = '🌾';
                
                productCard.innerHTML = `
                    <div class="product-image">${emoji} ${product.name}</div>
                    <div class="product-info">
                        <h3 class="product-name">${product.name}</h3>
                        <span class="product-type">${product.type}</span>
                        <p class="product-season">Season: ${product.seasonality}</p>
                    </div>
                `;
                
                productsContainer.appendChild(productCard);
            });
        });

        // Section navigation
        function showSection(sectionId) {
            // Update URL without reloading
            history.pushState(null, null, `?section=${sectionId}`);
            
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionId).classList.add('active');
            
            // Update active button in nav
            document.querySelectorAll('.sidenav-menu button').forEach(button => {
                button.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Show section based on URL parameter
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const sectionParam = urlParams.get('section');
            
            if (sectionParam && ['products', 'profile', 'feedback'].includes(sectionParam)) {
                showSection(sectionParam);
            }
        });
    </script>
</body>
</html>
<?php
// Close database connection
mysqli_close($db);
?>