<?php
// product_detail.php
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['id']);
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    header("Location: products.php");
    exit();
}

$product = $result->fetch_assoc();

// Get voltage and sensing range in readable format
$voltage_display = $product['voltage'];
if (!empty($product['voltage_min']) && !empty($product['voltage_max'])) {
    if ($product['voltage_min'] == $product['voltage_max']) {
        $voltage_display = $product['voltage_min'] . $product['voltage_unit'];
    } else {
        $voltage_display = $product['voltage_min'] . '-' . $product['voltage_max'] . $product['voltage_unit'];
    }
}

$sensing_range_display = $product['sensing_range'];
if (!empty($product['sensing_range_min']) && !empty($product['sensing_range_max'])) {
    if ($product['sensing_range_min'] == $product['sensing_range_max']) {
        $sensing_range_display = $product['sensing_range_min'] . $product['sensing_range_unit'];
    } else {
        $sensing_range_display = $product['sensing_range_min'] . '-' . $product['sensing_range_max'] . $product['sensing_range_unit'];
    }
}

$page_title = $product['name'] . " | Wain-Sensor";
$year = date('Y');
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
        
        /* Header Styles */
        header {
            background-color: #00adef;
            background-color: ;
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

        /* Product Detail Styles */
        .product-detail {
            max-width: 1000px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .product-header {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .product-image {
            flex: 1;
            min-width: 300px;
            height: 300px;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .product-info {
            flex: 2;
        }
        
        .product-info h1 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }
        
        .product-category {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .product-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            margin-bottom: 1.5rem;
        }
        
        .btn-group {
            margin-top: 1.5rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
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
        
        .btn-outline {
            background: transparent;
            border: 2px solid #3498db;
            color: #3498db;
        }
        
        .btn-outline:hover {
            background: #3498db;
            color: white;
        }
        
        .specs-section {
            margin-top: 2rem;
        }
        
        .specs-section h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .specs-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        
        .specs-table th, .specs-table td {
            padding: 1rem;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .specs-table th {
            background-color: #f5f5f5;
            width: 30%;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .specs-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .specs-table tr:hover {
            background-color: #f0f0f0;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        .back-btn:hover {
            background: #7f8c8d;
        }
        
        .product-meta {
            margin-top: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #7f8c8d;
        }
        
        .product-meta div {
            margin-bottom: 0.5rem;
        }
        
        /* Enhanced Technical Specs */
        .technical-details {
            margin-top: 2rem;
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .technical-details h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .spec-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .spec-item {
            background: white;
            padding: 1rem;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        
        .spec-item .label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        
        .spec-item .value {
            color: #555;
            font-size: 1.1rem;
        }
        
        /* Related Products */
        .related-products {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid #f0f0f0;
        }
        
        .related-products h3 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .related-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .related-card:hover {
            transform: translateY(-3px);
        }
        
        .related-card h4 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .related-card .category {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 0.2rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        
        .related-card .specs {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
        }
        
        .related-card .specs div {
            margin-bottom: 0.3rem;
        }
        
        .related-card .btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
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
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 1rem;
                justify-content: center;
            }
            
            .product-header {
                flex-direction: column;
            }
            
            .product-image {
                min-width: 100%;
                height: 250px;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn {
                text-align: center;
            }
            
            .specs-table {
                font-size: 0.9rem;
            }
            
            .specs-table th,
            .specs-table td {
                padding: 0.8rem 0.5rem;
            }
            
            .spec-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .product-detail {
                padding: 1rem;
                margin: 1rem auto;
            }
            
            .specs-table {
                display: block;
                overflow-x: auto;
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
                    <li><a href="products.php">Products</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <a href="products.php" class="back-btn">
            <span>←</span> Back to Products
        </a>
        
        <div class="product-detail">
            <div class="product-header">
                <div class="product-image">
                    <?php if ($product['image'] && file_exists($product['image'])): ?>
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <?php else: ?>
                        <div style="text-align: center; color: #777; font-weight: bold;">
                            Sensor Image<br>
                            <small>No image available</small>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h1><?php echo $product['name']; ?></h1>
                    <span class="product-category"><?php echo $product['category']; ?></span>
                    <p class="product-description"><?php echo $product['description']; ?></p>
                    
                    <div class="btn-group">
                        <a href="index.php#contact" class="btn">Request Information</a>
                        <a href="products.php" class="btn btn-outline">View All Products</a>
                        <a href="product_detail.php?id=<?php echo $product_id; ?>" class="btn btn-secondary" onclick="window.print(); return false;">Print Details</a>
                    </div>
                </div>
            </div>
            
            <!-- Enhanced Technical Details -->
            <div class="technical-details">
                <h3>📊 Technical Specifications</h3>
                <div class="spec-grid">
                    <div class="spec-item">
                        <div class="label">Category</div>
                        <div class="value"><?php echo $product['category']; ?></div>
                    </div>
                    <div class="spec-item">
                        <div class="label">Sensor Type</div>
                        <div class="value"><?php echo $product['type']; ?></div>
                    </div>
                    <div class="spec-item">
                        <div class="label">Thread Size</div>
                        <div class="value"><?php echo !empty($product['thread_size']) && $product['thread_size'] != 'N/A' ? $product['thread_size'] : 'Not specified'; ?></div>
                    </div>
                    <div class="spec-item">
                        <div class="label">Body Size</div>
                        <div class="value"><?php echo $product['dia']; ?></div>
                    </div>
                    <div class="spec-item">
                        <div class="label">Sensing Range</div>
                        <div class="value"><?php echo $sensing_range_display; ?></div>
                    </div>
                    <div class="spec-item">
                        <div class="label">Output Configuration</div>
                        <div class="value"><?php echo !empty($product['output_config']) ? $product['output_config'] : $product['output']; ?></div>
                    </div>
                    <div class="spec-item">
                        <div class="label">Connection Type</div>
                        <div class="value"><?php echo !empty($product['connection_type']) ? $product['connection_type'] : 'Not specified'; ?></div>
                    </div>
                    <div class="spec-item">
                        <div class="label">Voltage</div>
                        <div class="value"><?php echo $voltage_display; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="specs-section">
                <h2>Complete Specifications</h2>
                <table class="specs-table">
                    <tr>
                        <th>Type</th>
                        <td><?php echo $product['type']; ?></td>
                    </tr>
                    <tr>
                        <th>Diameter/Size</th>
                        <td><?php echo $product['dia']; ?></td>
                    </tr>
                    <tr>
                        <th>Thread Size</th>
                        <td><?php echo !empty($product['thread_size']) && $product['thread_size'] != 'N/A' ? $product['thread_size'] : 'Not specified'; ?></td>
                    </tr>
                    <tr>
                        <th>Sensing Range</th>
                        <td><?php echo $sensing_range_display; ?></td>
                    </tr>
                    <tr>
                        <th>Output</th>
                        <td><?php echo $product['output']; ?></td>
                    </tr>
                    <tr>
                        <th>Output Configuration</th>
                        <td><?php echo !empty($product['output_config']) ? $product['output_config'] : 'Not specified'; ?></td>
                    </tr>
                    <tr>
                        <th>Connection Type</th>
                        <td><?php echo !empty($product['connection_type']) ? $product['connection_type'] : 'Not specified'; ?></td>
                    </tr>
                    <tr>
                        <th>Voltage</th>
                        <td><?php echo $voltage_display; ?></td>
                    </tr>
                    <?php if (!empty($product['voltage_min']) || !empty($product['voltage_max'])): ?>
                    <tr>
                        <th>Voltage Range</th>
                        <td>
                            <?php 
                            if (!empty($product['voltage_min']) && !empty($product['voltage_max'])) {
                                echo $product['voltage_min'] . ' - ' . $product['voltage_max'] . ' ' . $product['voltage_unit'];
                            } elseif (!empty($product['voltage_min'])) {
                                echo 'Min: ' . $product['voltage_min'] . ' ' . $product['voltage_unit'];
                            } elseif (!empty($product['voltage_max'])) {
                                echo 'Max: ' . $product['voltage_max'] . ' ' . $product['voltage_unit'];
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Housing Material</th>
                        <td><?php echo $product['housing_material'] ? $product['housing_material'] : 'Not specified'; ?></td>
                    </tr>
                    <tr>
                        <th>Mounting</th>
                        <td><?php echo $product['mounting'] ? $product['mounting'] : 'Not specified'; ?></td>
                    </tr>
                    <tr>
                        <th>Operating Temperature</th>
                        <td><?php echo $product['operating_temp'] ? $product['operating_temp'] : 'Not specified'; ?></td>
                    </tr>
                    <tr>
                        <th>Protection Rating</th>
                        <td><?php echo $product['protection_rating'] ? $product['protection_rating'] : 'Not specified'; ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="product-meta">
                <div><strong>Product ID:</strong> WS-<?php echo str_pad($product['id'], 4, '0', STR_PAD_LEFT); ?></div>
                <div><strong>Last Updated:</strong> <?php echo date('F j, Y', strtotime($product['updated_at'])); ?></div>
            </div>
            
            <!-- Related Products Section -->
            <?php
            // Get related products (same category) with enhanced filters
            $related_sql = "SELECT * FROM products WHERE category = '" . $conn->real_escape_string($product['category']) . "' AND id != $product_id ORDER BY created_at DESC LIMIT 3";
            $related_result = $conn->query($related_sql);
            $related_products = [];
            if ($related_result->num_rows > 0) {
                while($row = $related_result->fetch_assoc()) {
                    $related_products[] = $row;
                }
            }
            ?>
            
            <?php if (count($related_products) > 0): ?>
            <div class="related-products">
                <h3>Related Products</h3>
                <div class="related-grid">
                    <?php foreach ($related_products as $related): ?>
                    <div class="related-card">
                        <span class="category"><?php echo $related['category']; ?></span>
                        <h4><?php echo $related['name']; ?></h4>
                        <div class="specs">
                            <div><strong>Type:</strong> <?php echo $related['type']; ?></div>
                            <div><strong>Size:</strong> <?php echo $related['dia']; ?></div>
                            <div><strong>Range:</strong> <?php echo $related['sensing_range']; ?></div>
                            <div><strong>Voltage:</strong> <?php echo $related['voltage']; ?></div>
                            <?php if (!empty($related['thread_size']) && $related['thread_size'] != 'N/A'): ?>
                                <div><strong>Thread:</strong> <?php echo $related['thread_size']; ?></div>
                            <?php endif; ?>
                        </div>
                        <a href="product_detail.php?id=<?php echo $related['id']; ?>" class="btn">View Details</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
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
                        <li><a href="index.php#contact">Contact</a></li>
                        <li><a href="staff_home.php">Staff</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p>Email: info@wain-sensor.com</p>
                    <p>Phone: +1 (555) 123-4567</p>
                    <p>Address: 123 Sensor Street, Tech City</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo $year; ?> Wain-Sensor. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Add smooth scrolling for anchor links
        document.addEventListener('DOMContentLoaded', function() {
            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            
            anchorLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });

        // Add to favorites functionality (example)
        function addToFavorites(productId) {
            let favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
            
            if (!favorites.includes(productId)) {
                favorites.push(productId);
                localStorage.setItem('favorites', JSON.stringify(favorites));
                alert('Product added to favorites!');
            } else {
                alert('Product is already in favorites!');
            }
        }

        // Print styles
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                header, footer, .back-btn, .btn-group {
                    display: none !important;
                }
                .product-detail {
                    box-shadow: none !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                body {
                    background: white !important;
                }
                .technical-details {
                    break-inside: avoid;
                }
                .specs-table {
                    break-inside: avoid;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Copy product details to clipboard
        function copyProductDetails() {
            const productName = document.querySelector('.product-info h1').textContent;
            const productCategory = document.querySelector('.product-category').textContent;
            const productDescription = document.querySelector('.product-description').textContent;
            
            let specsText = "Product Specifications:\n";
            const specItems = document.querySelectorAll('.specs-table tr');
            specItems.forEach(item => {
                const th = item.querySelector('th');
                const td = item.querySelector('td');
                if (th && td) {
                    specsText += `${th.textContent}: ${td.textContent}\n`;
                }
            });
            
            const textToCopy = `${productName}\n${productCategory}\n\n${productDescription}\n\n${specsText}`;
            
            navigator.clipboard.writeText(textToCopy).then(() => {
                alert('Product details copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
        
        // Add copy button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const btnGroup = document.querySelector('.btn-group');
            if (btnGroup) {
                const copyBtn = document.createElement('button');
                copyBtn.className = 'btn btn-secondary';
                copyBtn.textContent = 'Copy Details';
                copyBtn.onclick = copyProductDetails;
                btnGroup.appendChild(copyBtn);
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>