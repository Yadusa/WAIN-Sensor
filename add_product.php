<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

$error = '';
$success = '';

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
    
    // New fields for enhanced filtering
    $voltage_min = $conn->real_escape_string($_POST['voltage_min']);
    $voltage_max = $conn->real_escape_string($_POST['voltage_max']);
    $voltage_unit = $conn->real_escape_string($_POST['voltage_unit']);
    $sensing_range_min = $conn->real_escape_string($_POST['sensing_range_min']);
    $sensing_range_max = $conn->real_escape_string($_POST['sensing_range_max']);
    $sensing_range_unit = $conn->real_escape_string($_POST['sensing_range_unit']);
    $connection_type = $conn->real_escape_string($_POST['connection_type']);
    $output_config = $conn->real_escape_string($_POST['output_config']);
    
    // Extract thread size from dia
    $thread_size = 'N/A';
    if (preg_match('/M\d+/', $dia, $matches)) {
        $thread_size = $matches[0];
    } elseif (preg_match('/ø\d+\.?\d*/', $dia)) {
        $thread_size = 'Metric Thread';
    }
    
    // Handle image upload
    $image_path = '';
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
                $new_filename = 'product_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                    $image_path = $upload_path;
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
    
    if (empty($error)) {
        $sql = "INSERT INTO products (
            name, category, type, dia, sensing_range, output, voltage, description, image, 
            housing_material, mounting, operating_temp, protection_rating,
            voltage_min, voltage_max, voltage_unit, 
            sensing_range_min, sensing_range_max, sensing_range_unit,
            connection_type, output_config, thread_size
        ) VALUES (
            '$name', '$category', '$type', '$dia', '$sensing_range', '$output', '$voltage', '$description', '$image_path',
            '$housing_material', '$mounting', '$operating_temp', '$protection_rating',
            '$voltage_min', '$voltage_max', '$voltage_unit',
            '$sensing_range_min', '$sensing_range_max', '$sensing_range_unit',
            '$connection_type', '$output_config', '$thread_size'
        )";
        
        if ($conn->query($sql)) {
            $success = "Product added successfully!";
            // Clear form fields
            $_POST = array();
        } else {
            $error = "Error adding product: " . $conn->error;
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
    <title>Add Product | Wain-Sensor</title>
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
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .form-section {
            margin-bottom: 2.5rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .form-section h3 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
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
        
        .range-group {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .range-group .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        
        .range-separator {
            font-weight: bold;
            color: #7f8c8d;
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
        
        .btn-success {
            background: #2ecc71;
        }
        
        .btn-success:hover {
            background: #27ae60;
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
        
        .required::after {
            content: " *";
            color: #e74c3c;
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
        
        .file-input-info {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .hint {
            display: block;
            margin-top: 0.3rem;
            font-size: 0.85rem;
            color: #7f8c8d;
            font-style: italic;
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
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .range-group {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .range-separator {
                display: none;
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
            <div class="form-header">
                <h2>Add New Product</h2>
                <p>Fill in the details below to add a new product to the catalog.</p>
            </div>
            
            <?php if ($success): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                
                <!-- Basic Information Section -->
                <div class="form-section">
                    <h3>📋 Basic Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name" class="required">Product Name</label>
                            <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="category" class="required">Category</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Proximity Sensor" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Proximity Sensor') ? 'selected' : ''; ?>>Proximity Sensor</option>
                                <option value="Photoelectric Sensor" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Photoelectric Sensor') ? 'selected' : ''; ?>>Photoelectric Sensor</option>
                                <option value="Ultrasonic Sensor" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Ultrasonic Sensor') ? 'selected' : ''; ?>>Ultrasonic Sensor</option>
                                <option value="Capacitive Sensor" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Capacitive Sensor') ? 'selected' : ''; ?>>Capacitive Sensor</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="type" class="required">Sensor Type</label>
                            <input type="text" id="type" name="type" value="<?php echo isset($_POST['type']) ? $_POST['type'] : ''; ?>" required placeholder="e.g., Inductive, Through-beam">
                        </div>
                        
                        <div class="form-group">
                            <label for="dia" class="required">Diameter/Size</label>
                            <input type="text" id="dia" name="dia" value="<?php echo isset($_POST['dia']) ? $_POST['dia'] : ''; ?>" required placeholder="e.g., M12, ø6.5, 50x50x18mm">
                            <span class="hint">Include thread size if applicable (M4, M8, M12, etc.)</span>
                        </div>
                    </div>
                </div>
                
                <!-- Technical Specifications Section -->
                <div class="form-section">
                    <h3>⚙️ Technical Specifications</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="sensing_range" class="required">Sensing Range</label>
                            <input type="text" id="sensing_range" name="sensing_range" value="<?php echo isset($_POST['sensing_range']) ? $_POST['sensing_range'] : ''; ?>" required placeholder="e.g., 2.0mm, 4-15mm, 0.1-10m">
                        </div>
                        
                        <div class="form-group">
                            <label for="sensing_range_min">Sensing Range Min</label>
                            <input type="number" step="0.1" id="sensing_range_min" name="sensing_range_min" value="<?php echo isset($_POST['sensing_range_min']) ? $_POST['sensing_range_min'] : ''; ?>" placeholder="Min value">
                        </div>
                        
                        <div class="form-group">
                            <label for="sensing_range_max">Sensing Range Max</label>
                            <input type="number" step="0.1" id="sensing_range_max" name="sensing_range_max" value="<?php echo isset($_POST['sensing_range_max']) ? $_POST['sensing_range_max'] : ''; ?>" placeholder="Max value">
                        </div>
                        
                        <div class="form-group">
                            <label for="sensing_range_unit">Range Unit</label>
                            <select id="sensing_range_unit" name="sensing_range_unit">
                                <option value="">Select Unit</option>
                                <option value="mm" <?php echo (isset($_POST['sensing_range_unit']) && $_POST['sensing_range_unit'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                                <option value="m" <?php echo (isset($_POST['sensing_range_unit']) && $_POST['sensing_range_unit'] == 'm') ? 'selected' : ''; ?>>m</option>
                                <option value="cm" <?php echo (isset($_POST['sensing_range_unit']) && $_POST['sensing_range_unit'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="output" class="required">Output</label>
                            <input type="text" id="output" name="output" value="<?php echo isset($_POST['output']) ? $_POST['output'] : ''; ?>" required placeholder="e.g., NPN/PNP NO/NC, Namur">
                        </div>
                        
                        <div class="form-group">
                            <label for="output_config">Output Configuration</label>
                            <select id="output_config" name="output_config">
                                <option value="">Select Configuration</option>
                                <option value="NPN/PNP" <?php echo (isset($_POST['output_config']) && $_POST['output_config'] == 'NPN/PNP') ? 'selected' : ''; ?>>NPN/PNP</option>
                                <option value="NO/NC" <?php echo (isset($_POST['output_config']) && $_POST['output_config'] == 'NO/NC') ? 'selected' : ''; ?>>NO/NC</option>
                                <option value="Light/Dark On" <?php echo (isset($_POST['output_config']) && $_POST['output_config'] == 'Light/Dark On') ? 'selected' : ''; ?>>Light/Dark On</option>
                                <option value="Namur" <?php echo (isset($_POST['output_config']) && $_POST['output_config'] == 'Namur') ? 'selected' : ''; ?>>Namur</option>
                                <option value="Changeover" <?php echo (isset($_POST['output_config']) && $_POST['output_config'] == 'Changeover') ? 'selected' : ''; ?>>Changeover</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="connection_type">Connection Type</label>
                            <select id="connection_type" name="connection_type">
                                <option value="">Select Connection Type</option>
                                <option value="2-wire" <?php echo (isset($_POST['connection_type']) && $_POST['connection_type'] == '2-wire') ? 'selected' : ''; ?>>2-wire</option>
                                <option value="3-wire" <?php echo (isset($_POST['connection_type']) && $_POST['connection_type'] == '3-wire') ? 'selected' : ''; ?>>3-wire</option>
                                <option value="4-wire" <?php echo (isset($_POST['connection_type']) && $_POST['connection_type'] == '4-wire') ? 'selected' : ''; ?>>4-wire</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Voltage Specifications -->
                <div class="form-section">
                    <h3>⚡ Voltage Specifications</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="voltage" class="required">Voltage</label>
                            <input type="text" id="voltage" name="voltage" value="<?php echo isset($_POST['voltage']) ? $_POST['voltage'] : ''; ?>" required placeholder="e.g., 10-30VDC, 20-250VAC">
                        </div>
                        
                        <div class="form-group">
                            <label for="voltage_min">Voltage Min</label>
                            <input type="number" step="0.1" id="voltage_min" name="voltage_min" value="<?php echo isset($_POST['voltage_min']) ? $_POST['voltage_min'] : ''; ?>" placeholder="Min voltage">
                        </div>
                        
                        <div class="form-group">
                            <label for="voltage_max">Voltage Max</label>
                            <input type="number" step="0.1" id="voltage_max" name="voltage_max" value="<?php echo isset($_POST['voltage_max']) ? $_POST['voltage_max'] : ''; ?>" placeholder="Max voltage">
                        </div>
                        
                        <div class="form-group">
                            <label for="voltage_unit">Voltage Unit</label>
                            <select id="voltage_unit" name="voltage_unit">
                                <option value="">Select Unit</option>
                                <option value="VDC" <?php echo (isset($_POST['voltage_unit']) && $_POST['voltage_unit'] == 'VDC') ? 'selected' : ''; ?>>VDC</option>
                                <option value="VAC" <?php echo (isset($_POST['voltage_unit']) && $_POST['voltage_unit'] == 'VAC') ? 'selected' : ''; ?>>VAC</option>
                                <option value="VAC/DC" <?php echo (isset($_POST['voltage_unit']) && $_POST['voltage_unit'] == 'VAC/DC') ? 'selected' : ''; ?>>VAC/DC</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Construction Details -->
                <div class="form-section">
                    <h3>🏗️ Construction Details</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="housing_material">Housing Material</label>
                            <input type="text" id="housing_material" name="housing_material" value="<?php echo isset($_POST['housing_material']) ? $_POST['housing_material'] : ''; ?>" placeholder="e.g., Stainless Steel, Plastic">
                        </div>
                        
                        <div class="form-group">
                            <label for="mounting">Mounting Type</label>
                            <input type="text" id="mounting" name="mounting" value="<?php echo isset($_POST['mounting']) ? $_POST['mounting'] : ''; ?>" placeholder="e.g., Flush, Bracket, Adhesive">
                        </div>
                        
                        <div class="form-group">
                            <label for="operating_temp">Operating Temperature</label>
                            <input type="text" id="operating_temp" name="operating_temp" value="<?php echo isset($_POST['operating_temp']) ? $_POST['operating_temp'] : ''; ?>" placeholder="e.g., -25°C to 70°C">
                        </div>
                        
                        <div class="form-group">
                            <label for="protection_rating">Protection Rating</label>
                            <input type="text" id="protection_rating" name="protection_rating" value="<?php echo isset($_POST['protection_rating']) ? $_POST['protection_rating'] : ''; ?>" placeholder="e.g., IP67, IP68">
                        </div>
                    </div>
                </div>
                
                <!-- Description & Image -->
                <div class="form-section">
                    <h3>📝 Description & Media</h3>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="description" class="required">Product Description</label>
                            <textarea id="description" name="description" required rows="4"><?php echo isset($_POST['description']) ? $_POST['description'] : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="product_image">Product Image</label>
                            <input type="file" id="product_image" name="product_image" accept="image/*" onchange="previewImage(this)">
                            <span class="file-input-info">Max file size: 2MB. Allowed formats: JPG, PNG, GIF</span>
                            <div class="image-preview" id="imagePreview">
                                <img src="" alt="Image Preview">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Add Product</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                    <button type="reset" class="btn">Reset Form</button>
                </div>
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
            } else {
                preview.style.display = 'none';
                img.src = '';
            }
        }
        
        // Auto-extract range values from text input
        document.getElementById('sensing_range').addEventListener('blur', function() {
            const value = this.value;
            const rangeMatch = value.match(/(\d+\.?\d*)\s*[-–]\s*(\d+\.?\d*)/);
            const singleMatch = value.match(/(\d+\.?\d*)/);
            
            if (rangeMatch) {
                document.getElementById('sensing_range_min').value = rangeMatch[1];
                document.getElementById('sensing_range_max').value = rangeMatch[2];
                if (value.includes('mm')) {
                    document.getElementById('sensing_range_unit').value = 'mm';
                } else if (value.includes('m') && !value.includes('mm')) {
                    document.getElementById('sensing_range_unit').value = 'm';
                }
            } else if (singleMatch) {
                document.getElementById('sensing_range_min').value = singleMatch[1];
                document.getElementById('sensing_range_max').value = singleMatch[1];
                if (value.includes('mm')) {
                    document.getElementById('sensing_range_unit').value = 'mm';
                } else if (value.includes('m') && !value.includes('mm')) {
                    document.getElementById('sensing_range_unit').value = 'm';
                }
            }
        });
        
        // Auto-extract voltage values from text input
        document.getElementById('voltage').addEventListener('blur', function() {
            const value = this.value;
            const rangeMatch = value.match(/(\d+\.?\d*)\s*[-–]\s*(\d+\.?\d*)/);
            const singleMatch = value.match(/(\d+\.?\d*)/);
            
            if (rangeMatch) {
                document.getElementById('voltage_min').value = rangeMatch[1];
                document.getElementById('voltage_max').value = rangeMatch[2];
                if (value.includes('VDC')) {
                    document.getElementById('voltage_unit').value = 'VDC';
                } else if (value.includes('VAC')) {
                    document.getElementById('voltage_unit').value = 'VAC';
                }
            } else if (singleMatch) {
                document.getElementById('voltage_min').value = singleMatch[1];
                document.getElementById('voltage_max').value = singleMatch[1];
                if (value.includes('VDC')) {
                    document.getElementById('voltage_unit').value = 'VDC';
                } else if (value.includes('VAC')) {
                    document.getElementById('voltage_unit').value = 'VAC';
                }
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
            
            // Validate range values
            const rangeMin = document.getElementById('sensing_range_min');
            const rangeMax = document.getElementById('sensing_range_max');
            if (rangeMin.value && rangeMax.value && parseFloat(rangeMin.value) > parseFloat(rangeMax.value)) {
                valid = false;
                rangeMin.style.borderColor = '#e74c3c';
                rangeMax.style.borderColor = '#e74c3c';
                alert('Minimum sensing range cannot be greater than maximum range');
            }
            
            const voltageMin = document.getElementById('voltage_min');
            const voltageMax = document.getElementById('voltage_max');
            if (voltageMin.value && voltageMax.value && parseFloat(voltageMin.value) > parseFloat(voltageMax.value)) {
                valid = false;
                voltageMin.style.borderColor = '#e74c3c';
                voltageMax.style.borderColor = '#e74c3c';
                alert('Minimum voltage cannot be greater than maximum voltage');
            }
            
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly.');
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>