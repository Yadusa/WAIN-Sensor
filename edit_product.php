<?php
// edit_product.php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$product_id = intval($_GET['id']);
$error = '';
$success = '';

// Get product data
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    header("Location: admin_dashboard.php");
    exit();
}

$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $type = $conn->real_escape_string($_POST['type']);
    $dia = $conn->real_escape_string($_POST['dia']);
    $sensing_range = $conn->real_escape_string($_POST['sensing_range']);
    $output = $conn->real_escape_string($_POST['output']);
    $voltage = $conn->real_escape_string($_POST['voltage']);
    $description = $conn->real_escape_string($_POST['description']);
    $housing_material = $conn->real_escape_string($_POST['housing_material']);
    $mounting = $conn->real_escape_string($_POST['mounting']);
    $operating_temp = $conn->real_escape_string($_POST['operating_temp']);
    $protection_rating = $conn->real_escape_string($_POST['protection_rating']);
    
    // Handle image upload
    $image_update = "";
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['product_image']['type'];
        $file_size = $_FILES['product_image']['size'];
        $max_file_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array($file_type, $allowed_types)) {
            if ($file_size <= $max_file_size) {
                $upload_dir = 'uploads/products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'product_' . $product_id . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    if ($product['image'] && file_exists($product['image'])) {
                        unlink($product['image']);
                    }
                    $image_update = ", image = '$upload_path'";
                } else {
                    $error = "Error uploading image. Please try again.";
                }
            } else {
                $error = "Image file size too large. Maximum size is 2MB.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, and GIF images are allowed.";
        }
    } elseif (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== 4) {
        // Error 4 means no file was uploaded
        $error = "Error uploading image: " . $_FILES['product_image']['error'];
    }
    
    // Handle image removal
    $remove_image = isset($_POST['remove_image']) ? $_POST['remove_image'] : '';
    if ($remove_image === 'yes') {
        // Delete the current image file
        if ($product['image'] && file_exists($product['image'])) {
            unlink($product['image']);
        }
        $image_update = ", image = ''";
    }
    
    if (empty($error)) {
        $sql = "UPDATE products SET 
                name = '$name', 
                category = '$category', 
                type = '$type', 
                dia = '$dia', 
                sensing_range = '$sensing_range', 
                output = '$output', 
                voltage = '$voltage', 
                description = '$description', 
                housing_material = '$housing_material', 
                mounting = '$mounting', 
                operating_temp = '$operating_temp', 
                protection_rating = '$protection_rating'
                $image_update 
                WHERE id = $product_id";
        
        if ($conn->query($sql)) {
            $success = "Product updated successfully!";
            // Refresh product data
            $result = $conn->query("SELECT * FROM products WHERE id = $product_id");
            $product = $result->fetch_assoc();
        } else {
            $error = "Error updating product: " . $conn->error;
        }
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Wain-Sensor</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Header Styles */
        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
        }
        
        .logo {
            height: 25px;
            width: auto;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 1.5rem;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            padding: 0.5rem;
            border-radius: 4px;
        }
        
        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 1rem;
            margin-right: 0.5rem;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-secondary {
            background: #95a5a6;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .message {
            padding: 0.8rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .image-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .current-image {
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .current-image img {
            max-width: 300px;
            max-height: 200px;
            border: 2px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            background: white;
        }
        
        .no-image {
            padding: 2rem;
            text-align: center;
            background: white;
            border: 2px dashed #ddd;
            border-radius: 4px;
            color: #7f8c8d;
        }
        
        .image-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .file-input-info {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .image-preview {
            margin-top: 0.5rem;
            max-width: 200px;
            max-height: 200px;
            border: 2px dashed #ddd;
            padding: 0.5rem;
            border-radius: 4px;
            display: none;
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 180px;
            display: block;
        }
        
        .remove-image-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .remove-image-checkbox input[type="checkbox"] {
            width: auto;
        }
        
        /* Footer Styles */
        footer {
            background: #2c3e50;
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 3rem;
        }
        
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-section {
            flex: 1;
            min-width: 250px;
        }
        
        .footer-section h3 {
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 1rem;
                justify-content: center;
            }
            
            .image-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn {
                display: block;
                width: 100%;
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo-container">
                <img src="wain-sensor-logo.png" alt="Wain-Sensor Logo" class="logo">
            </div>
            <nav>
                <ul>
                    
                    <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <li><a href="contact_monitor.php">Report</a></li>
                    
                    <li><a href="staff_profile.php">My Profile</a></li>
                    <li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <div class="form-container">
            <h2>Edit Product: <?php echo $product['name']; ?></h2>
            
            <?php if ($success): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <!-- Current Image Section -->
                <div class="image-section">
                    <h3>Product Image</h3>
                    <div class="current-image">
                        <?php if ($product['image'] && file_exists($product['image'])): ?>
                            <img src="<?php echo $product['image']; ?>" alt="Current Product Image">
                            <p>Current Image</p>
                        <?php else: ?>
                            <div class="no-image">
                                <p>No image uploaded</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="image-controls">
                        <div style="flex: 1;">
                            <label for="product_image">Upload New Image</label>
                            <input type="file" id="product_image" name="product_image" accept="image/*" onchange="previewImage(this)">
                            <span class="file-input-info">Max file size: 2MB. Allowed formats: JPG, PNG, GIF</span>
                            <div class="image-preview" id="imagePreview">
                                <img src="" alt="Image Preview">
                            </div>
                        </div>
                        
                        <?php if ($product['image'] && file_exists($product['image'])): ?>
                        <div class="remove-image-checkbox">
                            <input type="checkbox" id="remove_image" name="remove_image" value="yes">
                            <label for="remove_image">Remove current image</label>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo $product['name']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Proximity Sensor" <?php echo ($product['category'] == 'Proximity Sensor') ? 'selected' : ''; ?>>Proximity Sensor</option>
                        <option value="Photoelectric Sensor" <?php echo ($product['category'] == 'Photoelectric Sensor') ? 'selected' : ''; ?>>Photoelectric Sensor</option>
                        <option value="Ultrasonic Sensor" <?php echo ($product['category'] == 'Ultrasonic Sensor') ? 'selected' : ''; ?>>Ultrasonic Sensor</option>
                        <option value="Capacitive Sensor" <?php echo ($product['category'] == 'Capacitive Sensor') ? 'selected' : ''; ?>>Capacitive Sensor</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="type">Type *</label>
                    <input type="text" id="type" name="type" value="<?php echo $product['type']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="dia">Diameter/Size *</label>
                    <input type="text" id="dia" name="dia" value="<?php echo $product['dia']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="sensing_range">Sensing Range *</label>
                    <input type="text" id="sensing_range" name="sensing_range" value="<?php echo $product['sensing_range']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="output">Output *</label>
                    <input type="text" id="output" name="output" value="<?php echo $product['output']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="voltage">Voltage *</label>
                    <input type="text" id="voltage" name="voltage" value="<?php echo $product['voltage']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required><?php echo $product['description']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="housing_material">Housing Material</label>
                    <input type="text" id="housing_material" name="housing_material" value="<?php echo $product['housing_material']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="mounting">Mounting</label>
                    <input type="text" id="mounting" name="mounting" value="<?php echo $product['mounting']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="operating_temp">Operating Temperature</label>
                    <input type="text" id="operating_temp" name="operating_temp" value="<?php echo $product['operating_temp']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="protection_rating">Protection Rating</label>
                    <input type="text" id="protection_rating" name="protection_rating" value="<?php echo $product['protection_rating']; ?>">
                </div>
                
                <button type="submit" class="btn">Update Product</button>
                <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                <?php if ($product['image'] && file_exists($product['image'])): ?>
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('remove_image').checked = true; document.querySelector('form').submit();">Remove Image & Update</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Wain-Sensor Staff Portal</h3>
                    <p>Internal management system for authorized staff members only.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="staff_home.php">Staff Home</a></li>
                        <li><a href="admin_dashboard.php">Dashboard</a></li>
                        <li><a href="add_product.php">Add Product</a></li>
                        <li><a href="staff_profile.php">My Profile</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>IT Support</h3>
                    <p>Email: it-support@wain-sensor.com</p>
                    <p>Phone: +1 (555) 123-HELP</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Wain-Sensor. Staff portal access restricted to authorized personnel only.</p>
            </div>
        </div>
    </footer>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const img = preview.querySelector('img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
                
                // Uncheck remove image if uploading new image
                const removeCheckbox = document.getElementById('remove_image');
                if (removeCheckbox) {
                    removeCheckbox.checked = false;
                }
            } else {
                preview.style.display = 'none';
                img.src = '';
            }
        }
        
        // Show current image preview on page load if image exists
        document.addEventListener('DOMContentLoaded', function() {
            const currentImage = '<?php echo $product['image']; ?>';
            if (currentImage) {
                const preview = document.getElementById('imagePreview');
                const img = preview.querySelector('img');
                img.src = currentImage;
                preview.style.display = 'block';
            }
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#e74c3c';
                } else {
                    field.style.borderColor = '#ddd';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
        
        // Handle remove image checkbox
        const removeCheckbox = document.getElementById('remove_image');
        if (removeCheckbox) {
            removeCheckbox.addEventListener('change', function() {
                const fileInput = document.getElementById('product_image');
                if (this.checked) {
                    fileInput.disabled = true;
                    // Clear file input
                    fileInput.value = '';
                    // Hide preview
                    const preview = document.getElementById('imagePreview');
                    preview.style.display = 'none';
                } else {
                    fileInput.disabled = false;
                }
            });
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>