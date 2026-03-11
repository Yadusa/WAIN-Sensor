<?php
// staff_home.php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal | Wain-Sensor</title>
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
        
        /* Staff Portal Styles */
        .staff-portal {
            max-width: 800px;
            margin: 5rem auto;
            padding: 3rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .staff-portal h1 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }
        
        .staff-portal p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            color: #7f8c8d;
        }
        
        .portal-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        
        .feature-card {
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .feature-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .login-btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: bold;
            transition: background 0.3s;
            margin-top: 1rem;
        }
        
        .login-btn:hover {
            background: #2980b9;
        }
        
        .back-to-site {
            display: inline-block;
            margin-top: 1rem;
            color: #7f8c8d;
            text-decoration: none;
        }
        
        .back-to-site:hover {
            color: #3498db;
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
            
            .portal-features {
                grid-template-columns: 1fr;
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
                    <li><a href="index.php">Customer Site</a></li>
                    <li><a href="staff_home.php" style="background-color: rgba(255, 255, 255, 0.2);">Staff Portal</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <div class="staff-portal">
            <h1>Staff Portal</h1>
            <p>Welcome to the Wain-Sensor Staff Management System</p>
            
            <div class="portal-features">
                <div class="feature-card">
                    <h3>Product Management</h3>
                    <p>Add, edit, and manage product listings in the database</p>
                </div>
                <div class="feature-card">
                    <h3>Contact Management</h3>
                    <p>View and manage customer inquiries and contact form submissions</p>
                </div>
                <div class="feature-card">
                    <h3>Admin Dashboard</h3>
                    <p>Access comprehensive management tools and analytics</p>
                </div>
            </div>
            
            <a href="admin_login.php" class="login-btn">Staff Login</a>
            <br>
            <a href="index.php" class="back-to-site">← Back to Customer Website</a>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Wain-Sensor Staff Portal</h3>
                    <p>Internal management system for staff members only.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="staff_home.php">Staff Home</a></li>
                        <li><a href="admin_login.php">Staff Login</a></li>
                        <li><a href="index.php">Customer Site</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact IT Support</h3>
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