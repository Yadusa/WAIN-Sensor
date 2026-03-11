<?php
// products.php
include 'config.php';

// Initialize variables
$page_title = "Our Products | Wain-Sensor";
$company_name = "Wain-Sensor";
$year = date('Y');

// Get filters, sorting, and search
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Enhanced filters with more options
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$output_filter = isset($_GET['output']) ? $_GET['output'] : '';
$voltage_filter = isset($_GET['voltage']) ? $_GET['voltage'] : '';
$sensing_range_filter = isset($_GET['sensing_range']) ? $_GET['sensing_range'] : '';
$housing_material_filter = isset($_GET['housing_material']) ? $_GET['housing_material'] : '';
$mounting_filter = isset($_GET['mounting']) ? $_GET['mounting'] : '';
$thread_size_filter = isset($_GET['thread_size']) ? $_GET['thread_size'] : '';
$protection_filter = isset($_GET['protection']) ? $_GET['protection'] : '';
$connection_type_filter = isset($_GET['connection_type']) ? $_GET['connection_type'] : '';
$output_config_filter = isset($_GET['output_config']) ? $_GET['output_config'] : '';
$voltage_min_filter = isset($_GET['voltage_min']) ? $_GET['voltage_min'] : '';
$voltage_max_filter = isset($_GET['voltage_max']) ? $_GET['voltage_max'] : '';
$range_min_filter = isset($_GET['range_min']) ? $_GET['range_min'] : '';
$range_max_filter = isset($_GET['range_max']) ? $_GET['range_max'] : '';

// Validate sort parameters - Expanded with more sorting options
$allowed_sorts = ['name', 'category', 'type', 'dia', 'sensing_range', 'voltage', 'output', 'housing_material', 'mounting', 'operating_temp', 'created_at', 'protection_rating', 'thread_size', 'connection_type'];
if (!in_array($sort_by, $allowed_sorts)) {
    $sort_by = 'name';
}
$sort_order = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';

// Build query with enhanced filtering
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($category_filter)) {
    $category_filter = $conn->real_escape_string($category_filter);
    $sql .= " AND category = '$category_filter'";
}

if (!empty($search_query)) {
    $search_query = $conn->real_escape_string($search_query);
    $sql .= " AND (name LIKE '%$search_query%' OR description LIKE '%$search_query%' OR type LIKE '%$search_query%')";
}

if (!empty($type_filter)) {
    $type_filter = $conn->real_escape_string($type_filter);
    $sql .= " AND type = '$type_filter'";
}

if (!empty($output_filter)) {
    $output_filter = $conn->real_escape_string($output_filter);
    $sql .= " AND output = '$output_filter'";
}

if (!empty($voltage_filter)) {
    $voltage_filter = $conn->real_escape_string($voltage_filter);
    $sql .= " AND voltage = '$voltage_filter'";
}

if (!empty($sensing_range_filter)) {
    $sensing_range_filter = $conn->real_escape_string($sensing_range_filter);
    $sql .= " AND sensing_range = '$sensing_range_filter'";
}

if (!empty($housing_material_filter)) {
    $housing_material_filter = $conn->real_escape_string($housing_material_filter);
    $sql .= " AND housing_material = '$housing_material_filter'";
}

if (!empty($mounting_filter)) {
    $mounting_filter = $conn->real_escape_string($mounting_filter);
    $sql .= " AND mounting = '$mounting_filter'";
}

if (!empty($thread_size_filter)) {
    $thread_size_filter = $conn->real_escape_string($thread_size_filter);
    $sql .= " AND (thread_size = '$thread_size_filter' OR dia LIKE '%$thread_size_filter%')";
}

if (!empty($protection_filter)) {
    $protection_filter = $conn->real_escape_string($protection_filter);
    $sql .= " AND protection_rating = '$protection_filter'";
}

if (!empty($connection_type_filter)) {
    $connection_type_filter = $conn->real_escape_string($connection_type_filter);
    $sql .= " AND connection_type = '$connection_type_filter'";
}

if (!empty($output_config_filter)) {
    $output_config_filter = $conn->real_escape_string($output_config_filter);
    $sql .= " AND output_config = '$output_config_filter'";
}

// Range-based filtering for voltage and sensing range
if (!empty($voltage_min_filter) && is_numeric($voltage_min_filter)) {
    $voltage_min_filter = floatval($voltage_min_filter);
    $sql .= " AND voltage_min >= $voltage_min_filter";
}

if (!empty($voltage_max_filter) && is_numeric($voltage_max_filter)) {
    $voltage_max_filter = floatval($voltage_max_filter);
    $sql .= " AND voltage_max <= $voltage_max_filter";
}

if (!empty($range_min_filter) && is_numeric($range_min_filter)) {
    $range_min_filter = floatval($range_min_filter);
    $sql .= " AND sensing_range_min >= $range_min_filter";
}

if (!empty($range_max_filter) && is_numeric($range_max_filter)) {
    $range_max_filter = floatval($range_max_filter);
    $sql .= " AND sensing_range_max <= $range_max_filter";
}

$sql .= " ORDER BY $sort_by $sort_order";

$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Get unique values for filters with better organization
$categories_result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category");
$categories = [];
while($row = $categories_result->fetch_assoc()) {
    $categories[] = $row['category'];
}

$types_result = $conn->query("SELECT DISTINCT type FROM products WHERE type IS NOT NULL ORDER BY type");
$types = [];
while($row = $types_result->fetch_assoc()) {
    $types[] = $row['type'];
}

// Enhanced output filters
$outputs_result = $conn->query("SELECT DISTINCT output FROM products WHERE output IS NOT NULL ORDER BY output");
$outputs = [];
while($row = $outputs_result->fetch_assoc()) {
    $outputs[] = $row['output'];
}

// Get output configurations
$output_configs_result = $conn->query("SELECT DISTINCT output_config FROM products WHERE output_config IS NOT NULL AND output_config != '' ORDER BY output_config");
$output_configs = [];
while($row = $output_configs_result->fetch_assoc()) {
    $output_configs[] = $row['output_config'];
}

// Get connection types
$connection_types_result = $conn->query("SELECT DISTINCT connection_type FROM products WHERE connection_type IS NOT NULL AND connection_type != '' ORDER BY connection_type");
$connection_types = [];
while($row = $connection_types_result->fetch_assoc()) {
    $connection_types[] = $row['connection_type'];
}

$voltages_result = $conn->query("SELECT DISTINCT voltage FROM products WHERE voltage IS NOT NULL ORDER BY voltage");
$voltages = [];
while($row = $voltages_result->fetch_assoc()) {
    $voltages[] = $row['voltage'];
}

// Get voltage ranges for slider
$voltage_range_result = $conn->query("SELECT MIN(voltage_min) as min_volt, MAX(voltage_max) as max_volt FROM products WHERE voltage_min IS NOT NULL AND voltage_max IS NOT NULL");
$voltage_range = $voltage_range_result->fetch_assoc();
$min_voltage = $voltage_range['min_volt'] ?: 0;
$max_voltage = $voltage_range['max_volt'] ?: 250;

$sensing_ranges_result = $conn->query("SELECT DISTINCT sensing_range FROM products WHERE sensing_range IS NOT NULL ORDER BY sensing_range");
$sensing_ranges = [];
while($row = $sensing_ranges_result->fetch_assoc()) {
    $sensing_ranges[] = $row['sensing_range'];
}

// Get sensing range ranges for slider
$range_range_result = $conn->query("SELECT MIN(sensing_range_min) as min_range, MAX(sensing_range_max) as max_range FROM products WHERE sensing_range_min IS NOT NULL AND sensing_range_max IS NOT NULL");
$range_range = $range_range_result->fetch_assoc();
$min_range = $range_range['min_range'] ?: 0;
$max_range = $range_range['max_range'] ?: 15;

$housing_materials_result = $conn->query("SELECT DISTINCT housing_material FROM products WHERE housing_material IS NOT NULL AND housing_material != '' ORDER BY housing_material");
$housing_materials = [];
while($row = $housing_materials_result->fetch_assoc()) {
    $housing_materials[] = $row['housing_material'];
}

$mountings_result = $conn->query("SELECT DISTINCT mounting FROM products WHERE mounting IS NOT NULL AND mounting != '' ORDER BY mounting");
$mountings = [];
while($row = $mountings_result->fetch_assoc()) {
    $mountings[] = $row['mounting'];
}

// Get thread sizes from new field or extract from dia
$thread_sizes_result = $conn->query("
    SELECT DISTINCT thread_size FROM products 
    WHERE thread_size IS NOT NULL AND thread_size != '' 
    UNION
    SELECT DISTINCT CASE 
        WHEN dia LIKE '%M8%' THEN 'M8'
        WHEN dia LIKE '%M12%' THEN 'M12'
        WHEN dia LIKE '%M18%' THEN 'M18'
        WHEN dia LIKE '%M30%' THEN 'M30'
        WHEN dia LIKE '%M4%' OR dia LIKE '%M5%' THEN 'M4/M5'
        ELSE 'N/A'
    END as thread_size
    FROM products
    ORDER BY thread_size
");
$thread_sizes = [];
while($row = $thread_sizes_result->fetch_assoc()) {
    $thread_sizes[] = $row['thread_size'];
}

$protections_result = $conn->query("SELECT DISTINCT protection_rating FROM products WHERE protection_rating IS NOT NULL AND protection_rating != '' ORDER BY protection_rating");
$protections = [];
while($row = $protections_result->fetch_assoc()) {
    $protections[] = $row['protection_rating'];
}

// Get body sizes for filters
$body_sizes_result = $conn->query("SELECT DISTINCT dia FROM products WHERE dia IS NOT NULL ORDER BY dia");
$body_sizes = [];
while($row = $body_sizes_result->fetch_assoc()) {
    $body_sizes[] = $row['dia'];
}

// Get ALL products for contact form dropdown
$all_products_sql = "SELECT DISTINCT name FROM products ORDER BY name";
$all_products_result = $conn->query($all_products_sql);
$all_products = [];
if ($all_products_result->num_rows > 0) {
    while($row = $all_products_result->fetch_assoc()) {
        $all_products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
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
        
        /* Header Styles - Same as index.php */
        header {
            background-color: #00adef;
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
        
        .staff-link {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Products Section */
        .section-title {
            text-align: center;
            margin: 3rem 0 2rem;
            font-size: 2.2rem;
            color: #2c3e50;
        }

        /* Enhanced Filter Section Styles - Pepperl-Fuchs Inspired */
        .search-filter-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .filter-header h3 {
            color: #2c3e50;
            font-size: 1.5rem;
            margin: 0;
        }

        .filter-count {
            background: #3498db;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Search Box */
        .search-box {
            display: flex;
            margin-bottom: 1.5rem;
        }

        .search-box input {
            flex: 1;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
            font-size: 1rem;
        }

        .search-box button {
            padding: 0.8rem 1.5rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }

        .search-box button:hover {
            background: #2980b9;
        }

        .filter-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-group {
            background: #f8f9fa;
            padding: 1.2rem;
            border-radius: 8px;
            border-left: 4px solid #3498db;
            transition: all 0.3s ease;
        }

        .filter-group:hover {
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .filter-group label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group select {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
            background: white;
            transition: all 0.3s;
            cursor: pointer;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .range-filter {
            padding: 0.5rem 0;
        }

        .range-inputs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .range-inputs input {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .range-values {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #7f8c8d;
        }

        .sorting-container {
            background: #f8f9fa;
            padding: 1.2rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            align-items: center;
            border-left: 4px solid #2ecc71;
        }

        .sort-group {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .sort-group label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.95rem;
            min-width: 100px;
        }

        .sort-group select {
            padding: 0.7rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
            font-size: 0.9rem;
            min-width: 200px;
            cursor: pointer;
        }

        .sort-group select:focus {
            outline: none;
            border-color: #2ecc71;
            box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.1);
        }

        .filter-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }

        .results-info {
            color: #2c3e50;
            font-size: 1rem;
            background: #e8f4fc;
            padding: 0.8rem 1.2rem;
            border-radius: 6px;
            border: 1px solid #b3d9ff;
        }

        .results-count {
            font-weight: 700;
            color: #3498db;
            font-size: 1.1rem;
        }

        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1rem 0;
            padding: 0.8rem;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .active-filters strong {
            color: #2c3e50;
            margin-right: 0.5rem;
        }

        .active-filter-tag {
            background: #3498db;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .active-filter-tag:hover {
            background: #2980b9;
        }

        .active-filter-tag .remove {
            color: white;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.1rem;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .active-filter-tag .remove:hover {
            opacity: 1;
        }

        .filter-section-title {
            color: #2c3e50;
            font-size: 1.1rem;
            margin: 1.5rem 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f0f0f0;
            font-weight: 600;
        }

        /* Products Grid */
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-img {
            height: 200px;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-weight: bold;
            position: relative;
            overflow: hidden;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-category {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #3498db;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-info h3 {
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .product-specs {
            margin: 1rem 0;
            font-size: 0.9rem;
        }

        .product-specs div {
            margin-bottom: 0.3rem;
            display: flex;
        }

        .spec-label {
            font-weight: bold;
            min-width: 120px;
        }

        .no-products {
            text-align: center;
            padding: 3rem;
            color: #7f8c8d;
        }

        .no-products h3 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        /* Contact Form Styles */
        .contact {
            padding: 3rem 0;
            background: white;
            margin-top: 3rem;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .contact-form {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group textarea {
            min-height: 150px;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-group.half {
            flex: 1;
            min-width: 0;
        }

        .form-group.half label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group.half input,
        .form-group.half select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .phone-format-hint {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 0.8rem;
        }

        /* Footer - Same as index.php */
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

        /* Buttons */
        .btn {
            padding: 0.8rem 1.5rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #2980b9;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #3498db;
            color: #3498db;
        }

        .btn-outline:hover {
            background: #3498db;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .filter-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 992px) {
            .filter-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .sorting-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .sort-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .sort-group label {
                min-width: auto;
            }
            
            .sort-group select {
                min-width: auto;
                width: 100%;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 1rem;
                justify-content: center;
            }
            
            .spec-label {
                min-width: 100px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .form-group.half {
                margin-bottom: 1.5rem;
            }
            
            .form-group.half:last-child {
                margin-bottom: 0;
            }
        }

        @media (max-width: 768px) {
            .filter-container {
                grid-template-columns: 1fr;
            }
            
            .filter-actions {
                flex-direction: column;
                gap: 1rem;
            }
            
            .results-info {
                text-align: center;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .search-filter-section {
                padding: 1rem;
            }
            
            .range-inputs {
                flex-direction: column;
            }
            
            .product-detail {
                padding: 1rem;
                margin: 1rem auto;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-content">
            <div class="logo-container">
                <img src="wain-sensor-logo.png" alt="Wain-Sensor Logo" class="logo">
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php" style="background-color: rgba(255, 255, 255, 0.2);">Products</a></li>
                    <li><a href="index.php#about">About Us</a></li>
                    <li><a href="#contact">Inquiry</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Products Section -->
    <section id="products" class="container">
        <h2 class="section-title">Our Sensor Products</h2>
        
        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <!-- Search Box -->
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search products by name, description, or type..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit">Search</button>
            </form>

            <!-- Filter Header -->
            <div class="filter-header">
                <h3>Filter by Technical Attributes <span class="filter-count"><?php echo count($products); ?></span></h3>
            </div>

            <!-- Advanced Filters -->
            <form method="GET" id="filterForm">
                <!-- Hidden fields to preserve search -->
                <?php if (!empty($search_query)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                <?php endif; ?>

                <div class="filter-container">
                    <!-- Category Filter -->
                    <div class="filter-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category; ?>" <?php echo $category_filter == $category ? 'selected' : ''; ?>>
                                    <?php echo $category; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div class="filter-group">
                        <label for="type">Sensor Type</label>
                        <select id="type" name="type" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <?php foreach ($types as $type): ?>
                                <option value="<?php echo $type; ?>" <?php echo $type_filter == $type ? 'selected' : ''; ?>>
                                    <?php echo $type; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Thread Size Filter -->
                    <div class="filter-group">
                        <label for="thread_size">Thread Size</label>
                        <select id="thread_size" name="thread_size" onchange="this.form.submit()">
                            <option value="">All Sizes</option>
                            <?php foreach ($thread_sizes as $size): ?>
                                <option value="<?php echo $size; ?>" <?php echo $thread_size_filter == $size ? 'selected' : ''; ?>>
                                    <?php echo $size; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Connection Type Filter -->
                    <div class="filter-group">
                        <label for="connection_type">Connection Type</label>
                        <select id="connection_type" name="connection_type" onchange="this.form.submit()">
                            <option value="">All Connection Types</option>
                            <?php foreach ($connection_types as $conn_type): ?>
                                <option value="<?php echo $conn_type; ?>" <?php echo $connection_type_filter == $conn_type ? 'selected' : ''; ?>>
                                    <?php echo $conn_type; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Output Configuration Filter -->
                    <div class="filter-group">
                        <label for="output_config">Output Configuration</label>
                        <select id="output_config" name="output_config" onchange="this.form.submit()">
                            <option value="">All Output Configs</option>
                            <?php foreach ($output_configs as $config): ?>
                                <option value="<?php echo $config; ?>" <?php echo $output_config_filter == $config ? 'selected' : ''; ?>>
                                    <?php echo $config; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Housing Material Filter -->
                    <?php if (!empty($housing_materials)): ?>
                    <div class="filter-group">
                        <label for="housing_material">Housing Material</label>
                        <select id="housing_material" name="housing_material" onchange="this.form.submit()">
                            <option value="">All Materials</option>
                            <?php foreach ($housing_materials as $material): ?>
                                <option value="<?php echo $material; ?>" <?php echo $housing_material_filter == $material ? 'selected' : ''; ?>>
                                    <?php echo $material; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <!-- Mounting Filter -->
                    <?php if (!empty($mountings)): ?>
                    <div class="filter-group">
                        <label for="mounting">Mounting Type</label>
                        <select id="mounting" name="mounting" onchange="this.form.submit()">
                            <option value="">All Mounting Types</option>
                            <?php foreach ($mountings as $mounting): ?>
                                <option value="<?php echo $mounting; ?>" <?php echo $mounting_filter == $mounting ? 'selected' : ''; ?>>
                                    <?php echo $mounting; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <!-- Protection Rating Filter -->
                    <?php if (!empty($protections)): ?>
                    <div class="filter-group">
                        <label for="protection">Protection Rating</label>
                        <select id="protection" name="protection" onchange="this.form.submit()">
                            <option value="">All Protection Ratings</option>
                            <?php foreach ($protections as $protection): ?>
                                <option value="<?php echo $protection; ?>" <?php echo $protection_filter == $protection ? 'selected' : ''; ?>>
                                    <?php echo $protection; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <!-- Voltage Range Filter -->
                    <div class="filter-group">
                        <label for="voltage_range">Voltage Range</label>
                        <div class="range-filter">
                            <div class="range-inputs">
                                <input type="number" id="voltage_min" name="voltage_min" placeholder="Min" 
                                    value="<?php echo $voltage_min_filter; ?>" step="0.1" min="<?php echo $min_voltage; ?>" max="<?php echo $max_voltage; ?>">
                                <input type="number" id="voltage_max" name="voltage_max" placeholder="Max" 
                                    value="<?php echo $voltage_max_filter; ?>" step="0.1" min="<?php echo $min_voltage; ?>" max="<?php echo $max_voltage; ?>">
                            </div>
                            <div class="range-values">
                                <span><?php echo $min_voltage; ?>V</span>
                                <span><?php echo $max_voltage; ?>V</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sensing Range Filter -->
                    <div class="filter-group">
                        <label for="sensing_range">Sensing Range</label>
                        <div class="range-filter">
                            <div class="range-inputs">
                                <input type="number" id="range_min" name="range_min" placeholder="Min" 
                                    value="<?php echo $range_min_filter; ?>" step="0.1" min="<?php echo $min_range; ?>" max="<?php echo $max_range; ?>">
                                <input type="number" id="range_max" name="range_max" placeholder="Max" 
                                    value="<?php echo $range_max_filter; ?>" step="0.1" min="<?php echo $min_range; ?>" max="<?php echo $max_range; ?>">
                            </div>
                            <div class="range-values">
                                <span><?php echo $min_range; ?>mm</span>
                                <span><?php echo $max_range; ?>mm</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Enhanced Sorting Options -->
                <div class="filter-section-title">Sorting Options</div>
                <div class="sorting-container">
                    <div class="sort-group">
                        <label for="sort">Sort by:</label>
                        <select id="sort" name="sort" onchange="this.form.submit()">
                            <option value="name" <?php echo $sort_by == 'name' ? 'selected' : ''; ?>>Product Name</option>
                            <option value="category" <?php echo $sort_by == 'category' ? 'selected' : ''; ?>>Category</option>
                            <option value="type" <?php echo $sort_by == 'type' ? 'selected' : ''; ?>>Sensor Type</option>
                            <option value="thread_size" <?php echo $sort_by == 'thread_size' ? 'selected' : ''; ?>>Thread Size</option>
                            <option value="dia" <?php echo $sort_by == 'dia' ? 'selected' : ''; ?>>Body Size</option>
                            <option value="sensing_range" <?php echo $sort_by == 'sensing_range' ? 'selected' : ''; ?>>Sensing Range</option>
                            <option value="voltage" <?php echo $sort_by == 'voltage' ? 'selected' : ''; ?>>Voltage</option>
                            <option value="output" <?php echo $sort_by == 'output' ? 'selected' : ''; ?>>Output Type</option>
                            <option value="connection_type" <?php echo $sort_by == 'connection_type' ? 'selected' : ''; ?>>Connection Type</option>
                            <option value="housing_material" <?php echo $sort_by == 'housing_material' ? 'selected' : ''; ?>>Housing Material</option>
                            <option value="mounting" <?php echo $sort_by == 'mounting' ? 'selected' : ''; ?>>Mounting Type</option>
                            <option value="protection_rating" <?php echo $sort_by == 'protection_rating' ? 'selected' : ''; ?>>Protection Rating</option>
                            <option value="created_at" <?php echo $sort_by == 'created_at' ? 'selected' : ''; ?>>Date Added</option>
                        </select>
                    </div>

                    <div class="sort-group">
                        <label for="order">Order:</label>
                        <select id="order" name="order" onchange="this.form.submit()">
                            <option value="ASC" <?php echo $sort_order == 'ASC' ? 'selected' : ''; ?>>Ascending (A-Z, Low-High)</option>
                            <option value="DESC" <?php echo $sort_order == 'DESC' ? 'selected' : ''; ?>>Descending (Z-A, High-Low)</option>
                        </select>
                    </div>
                </div>

                <!-- Active Filters Display -->
                <?php 
                $active_filters = [];
                if (!empty($category_filter)) $active_filters[] = ['Category', $category_filter, 'category'];
                if (!empty($type_filter)) $active_filters[] = ['Type', $type_filter, 'type'];
                if (!empty($thread_size_filter)) $active_filters[] = ['Thread Size', $thread_size_filter, 'thread_size'];
                if (!empty($connection_type_filter)) $active_filters[] = ['Connection', $connection_type_filter, 'connection_type'];
                if (!empty($output_config_filter)) $active_filters[] = ['Output Config', $output_config_filter, 'output_config'];
                if (!empty($housing_material_filter)) $active_filters[] = ['Material', $housing_material_filter, 'housing_material'];
                if (!empty($mounting_filter)) $active_filters[] = ['Mounting', $mounting_filter, 'mounting'];
                if (!empty($protection_filter)) $active_filters[] = ['Protection', $protection_filter, 'protection'];
                if (!empty($voltage_min_filter) || !empty($voltage_max_filter)) {
                    $volt_range = '';
                    if (!empty($voltage_min_filter)) $volt_range .= "≥{$voltage_min_filter}V ";
                    if (!empty($voltage_max_filter)) $volt_range .= "≤{$voltage_max_filter}V";
                    $active_filters[] = ['Voltage Range', trim($volt_range), 'voltage_min,voltage_max'];
                }
                if (!empty($range_min_filter) || !empty($range_max_filter)) {
                    $range_range = '';
                    if (!empty($range_min_filter)) $range_range .= "≥{$range_min_filter}mm ";
                    if (!empty($range_max_filter)) $range_range .= "≤{$range_max_filter}mm";
                    $active_filters[] = ['Sensing Range', trim($range_range), 'range_min,range_max'];
                }
                ?>
                
                <?php if (!empty($active_filters)): ?>
                <div class="active-filters">
                    <strong>Active Filters:</strong>
                    <?php foreach ($active_filters as $filter): ?>
                        <span class="active-filter-tag">
                            <?php echo $filter[0] . ': ' . $filter[1]; ?>
                            <span class="remove" onclick="removeFilter('<?php echo $filter[2]; ?>')">&times;</span>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Filter Actions -->
                <div class="filter-actions">
                    <div class="results-info">
                        Showing <span class="results-count"><?php echo count($products); ?></span> product(s)
                        <?php if (!empty($search_query)): ?>
                            for "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                        <?php endif; ?>
                    </div>
                    <div>
                        <button type="submit" class="btn" style="margin-right: 0.5rem;">Apply All Filters</button>
                        <a href="products.php" class="btn btn-outline">Clear All Filters</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="products">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-img">
                        <?php if ($product['image'] && file_exists($product['image'])): ?>
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            Sensor Image
                        <?php endif; ?>
                        <div class="product-category"><?php echo $product['category']; ?></div>
                    </div>
                    <div class="product-info">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <div class="product-specs">
                            <div><span class="spec-label">Type:</span> <?php echo $product['type']; ?></div>
                            <div><span class="spec-label">Size:</span> <?php echo $product['dia']; ?></div>
                            <div><span class="spec-label">Range:</span> <?php echo $product['sensing_range']; ?></div>
                            <div><span class="spec-label">Output:</span> <?php echo $product['output']; ?></div>
                            <div><span class="spec-label">Voltage:</span> <?php echo $product['voltage']; ?></div>
                            <?php if (!empty($product['thread_size']) && $product['thread_size'] != 'N/A'): ?>
                                <div><span class="spec-label">Thread:</span> <?php echo $product['thread_size']; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($product['connection_type'])): ?>
                                <div><span class="spec-label">Connection:</span> <?php echo $product['connection_type']; ?></div>
                            <?php endif; ?>
                        </div>
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                        <a href="#contact" class="btn" style="background: #95a5a6;">Request Info</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <h3>No products found</h3>
                    <p>Try adjusting your search criteria or filters.</p>
                    <a href="products.php" class="btn">View All Products</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">Product Inquiry - product request</h2>
            <div class="contact-form">
                <form id="contactForm" method="post">

                    <div class="form-group">
                        <label for="message">Please provide brand model, description and others *</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="product_interest">Product Interest</label>
                        <select id="product_interest" name="product_interest" class="form-control">
                            <option value="">Select a product</option>
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Proximity Sensors">Proximity Sensors</option>
                            <option value="Photoelectric Sensors">Photoelectric Sensors</option>
                            <option value="Ultrasonic Sensors">Ultrasonic Sensors</option>
                            <option value="Capacitive Sensors">Capacitive Sensors</option>
                            <?php foreach ($all_products as $product): ?>
                            <option value="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name">Your Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Your Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="country">Country *</label>
                            <select id="country" name="country" required>
                                <option value="">Select Country</option>
                                <option value="United States">United States</option>
                                <option value="Canada">Canada</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Australia">Australia</option>
                                <option value="Germany">Germany</option>
                                <option value="France">France</option>
                                <option value="Japan">Japan</option>
                                <option value="China">China</option>
                                <option value="India">India</option>
                                <option value="Brazil">Brazil</option>
                                <!-- Southeast Asian Countries -->
                                <option value="Malaysia">Malaysia</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Vietnam">Vietnam</option>
                                <option value="Philippines">Philippines</option>
                                <option value="Brunei">Brunei</option>
                                <option value="Cambodia">Cambodia</option>
                                <option value="Laos">Laos</option>
                                <option value="Myanmar">Myanmar</option>
                                <option value="East Timor">East Timor</option>
                                <!-- End Southeast Asian Countries -->
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group half">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number">
                            <small class="phone-format-hint">Please include country code (e.g., +1 555 123 4567)</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="state">State/Province *</label>
                        <input type="text" id="state" name="state" required>
                    </div>
                    
                    
                    
                    
                    
                    <button type="submit" class="btn" id="submitBtn">Send Message</button>
                    <div id="formMessage" style="display: none; margin-top: 1rem; padding: 0.8rem; border-radius: 4px;"></div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Wain-Sensor</h3>
                    <p>Providing precision sensing solutions for industrial, commercial, and residential applications.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="index.php#about">About Us</a></li>
                        <li><a href="#contact">Inquiry</a></li>
                        <li><a href="staff_home.php">Staff</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p>Email: enquiry@mccis.com.my</p>
                    <p>Phone: +606-317 7555</p>
                    <p>Fax: +606-317 7666
                    <p>Address: 36C, Jalan PB1, Taman Padang Balang, Batu Brendam, 75350 Melaka, Malaysia.</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo $year; ?> Wain-Sensor. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Enhanced JavaScript for filters
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form when filters change
            const filterForm = document.getElementById('filterForm');
            const filterSelects = filterForm.querySelectorAll('select');
            
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    // Show loading indicator
                    const submitBtn = filterForm.querySelector('.btn');
                    const originalText = submitBtn.textContent;
                    submitBtn.textContent = 'Applying...';
                    submitBtn.disabled = true;
                    
                    // Submit form with delay for better UX
                    setTimeout(() => {
                        filterForm.submit();
                    }, 300);
                });
            });

            // Auto-submit range filters when both values are entered
            const rangeInputs = document.querySelectorAll('.range-inputs input');
            rangeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Check if both min and max have values
                    const minInput = this.parentElement.querySelector('input[placeholder="Min"]');
                    const maxInput = this.parentElement.querySelector('input[placeholder="Max"]');
                    
                    if ((minInput.value && maxInput.value) || 
                        (this === minInput && maxInput.value) || 
                        (this === maxInput && minInput.value)) {
                        // Small delay to allow user to finish typing
                        setTimeout(() => {
                            filterForm.submit();
                        }, 800);
                    }
                });
            });

            // Validate range inputs
            rangeInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    const minVal = parseFloat(this.value);
                    const maxVal = parseFloat(this.parentElement.querySelector('input[placeholder="Max"]').value);
                    
                    if (!isNaN(minVal) && !isNaN(maxVal) && minVal > maxVal) {
                        alert('Minimum value cannot be greater than maximum value');
                        this.value = '';
                    }
                });
            });

            // Preserve search when using filters
            updateUrlWithSearch();
        });

        function removeFilter(filterNames) {
            // Create a new URL without the specified filters
            const url = new URL(window.location.href);
            const filters = filterNames.split(',');
            
            filters.forEach(filter => {
                url.searchParams.delete(filter.trim());
            });
            
            window.location.href = url.toString();
        }

        function updateUrlWithSearch() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('search');
            
            if (searchQuery) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'search';
                hiddenInput.value = searchQuery;
                document.getElementById('filterForm').appendChild(hiddenInput);
            }
        }

        // Contact form handling - Updated to save to database
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const formMessage = document.getElementById('formMessage');
            const originalBtnText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;
            formMessage.style.display = 'none';
            
            // Basic phone validation
            const phone = document.getElementById('phone').value.trim();
            if (!phone) {
                showErrorMessage('Please enter your phone number');
                submitBtn.textContent = originalBtnText;
                submitBtn.disabled = false;
                return;
            }
            
            // Get form data
            const formData = new FormData();
            formData.append('name', document.getElementById('name').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('phone', phone);
            formData.append('country', document.getElementById('country').value);
            formData.append('state', document.getElementById('state').value);
            formData.append('product_interest', document.getElementById('product_interest').value);
            formData.append('message', document.getElementById('message').value);
            formData.append('timestamp', new Date().toISOString());
            formData.append('source', 'Wain-Sensor Website');
            
            // Send to our PHP endpoint that saves to database AND Google Sheets
            fetch('submit_contact_database.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                showSuccessMessage(data);
                document.getElementById('contactForm').reset();
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('Sorry, there was an error. Please try again or email us directly at info@wain-sensor.com');
            })
            .finally(() => {
                submitBtn.textContent = originalBtnText;
                submitBtn.disabled = false;
            });
        });

        function showSuccessMessage(message) {
            const formMessage = document.getElementById('formMessage');
            formMessage.textContent = message;
            formMessage.style.background = '#d4edda';
            formMessage.style.color = '#155724';
            formMessage.style.border = '1px solid #c3e6cb';
            formMessage.style.display = 'block';
            
            formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function showErrorMessage(message) {
            const formMessage = document.getElementById('formMessage');
            formMessage.textContent = message;
            formMessage.style.background = '#f8d7da';
            formMessage.style.color = '#721c24';
            formMessage.style.border = '1px solid #f5c6cb';
            formMessage.style.display = 'block';
            
            formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>