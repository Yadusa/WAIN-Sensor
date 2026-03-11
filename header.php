<?php
// header.php
$company_name = "Wain-Sensor";
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Wain-Sensor'; ?></title>
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
            background-color: #57d9dfff;
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
        
        /* Staff link style */
        .staff-link {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
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
                    <li><a href="index.php" <?php echo ($current_page == 'index.php') ? 'style="background-color: rgba(255, 255, 255, 0.2);"' : ''; ?>>Home</a></li>
                    <li><a href="index.php#products" <?php echo ($current_page == 'index.php') ? 'style="background-color: rgba(255, 255, 255, 0.2);"' : ''; ?>>Products</a></li>
                    <li><a href="index.php#about" <?php echo ($current_page == 'index.php') ? 'style="background-color: rgba(255, 255, 255, 0.2);"' : ''; ?>>About Us</a></li>
                    <li><a href="index.php#contact" <?php echo ($current_page == 'index.php') ? 'style="background-color: rgba(255, 255, 255, 0.2);"' : ''; ?>>Contact</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="admin_dashboard.php" <?php echo ($current_page == 'admin_dashboard.php' || $current_page == 'add_product.php' || $current_page == 'edit_product.php') ? 'style="background-color: rgba(255, 255, 255, 0.2);"' : ''; ?>>Dashboard</a></li>
                        <li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                    <?php else: ?>
                        <!-- Removed Staff Login link from customer navigation -->
                        <li><a href="staff_home.php" class="staff-link">Staff</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">