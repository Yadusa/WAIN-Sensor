<?php
// admin_dashboard.php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql)) {
        $success = "Product deleted successfully";
    } else {
        $error = "Error deleting product: " . $conn->error;
    }
}

// Get all products
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Wain-Sensor</title>
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
        
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .welcome-message {
            background: #e8f4fc;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }
        
        .welcome-message h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        
        .products-table th, .products-table td {
            padding: 0.8rem;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .products-table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }
        
        .action-btn {
            padding: 0.3rem 0.8rem;
            margin: 0.2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .edit-btn {
            background: #3498db;
            color: white;
        }
        
        .edit-btn:hover {
            background: #2980b9;
        }
        
        .delete-btn {
            background: #e74c3c;
            color: white;
        }
        
        .delete-btn:hover {
            background: #c0392b;
        }
        
        .add-btn {
            background: #2ecc71;
            color: white;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
        }
        
        .add-btn:hover {
            background: #27ae60;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
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
            
            .admin-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .products-table {
                font-size: 0.9rem;
            }
            
            .action-btn {
                display: block;
                margin: 0.2rem 0;
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
                    
                    <li><a href="admin_dashboard.php" style="background-color: rgba(255, 255, 255, 0.2);">Dashboard</a></li>
                    <li><a href="contact_monitor.php">Report</a></li>
                    <li><a href="staff_profile.php">My Profile</a></li>
                    <li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                      
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <div class="admin-container">
            <div class="admin-header">
                <h2>Product Management Dashboard</h2>
                <a href="add_product.php" class="action-btn add-btn">Add New Product</a>
            </div>
            
            <div class="welcome-message">
                <h3>Welcome, <?php echo $_SESSION['username']; ?>!</h3>
                <p>You are logged in as <?php echo $_SESSION['role']; ?>. Here you can manage all products in the system.</p>
            </div>
            
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($products); ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count(array_filter($products, function($p) { return $p['category'] === 'Proximity Sensor'; })); ?></div>
                    <div class="stat-label">Proximity Sensors</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count(array_filter($products, function($p) { return $p['category'] === 'Photoelectric Sensor'; })); ?></div>
                    <div class="stat-label">Photoelectric Sensors</div>
                </div>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <table class="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Sensing Range</th>
                        <th>Voltage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['category']; ?></td>
                            <td><?php echo $product['type']; ?></td>
                            <td><?php echo $product['sensing_range']; ?></td>
                            <td><?php echo $product['voltage']; ?></td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <a href="admin_dashboard.php?delete=<?php echo $product['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No products found. <a href="add_product.php">Add your first product</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
</body>
</html>
<?php
$conn->close();
?>