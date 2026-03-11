<?php
// contact_monitor.php
include 'config.php';
validateSession();

// Get filter parameters
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : '30days';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$country_filter = isset($_GET['country']) ? $_GET['country'] : '';
$product_filter = isset($_GET['product']) ? $_GET['product'] : '';

// Calculate date range
$start_date = '';
$end_date = date('Y-m-d');
switch ($date_range) {
    case '7days':
        $start_date = date('Y-m-d', strtotime('-7 days'));
        break;
    case '30days':
        $start_date = date('Y-m-d', strtotime('-30 days'));
        break;
    case '90days':
        $start_date = date('Y-m-d', strtotime('-90 days'));
        break;
    case '1year':
        $start_date = date('Y-m-d', strtotime('-1 year'));
        break;
    default:
        $start_date = date('Y-m-d', strtotime('-30 days'));
}

// Build base query for statistics
$base_sql = "SELECT * FROM contact_submissions WHERE DATE(timestamp) BETWEEN '$start_date' AND '$end_date'";
$count_sql = "SELECT COUNT(*) as total FROM contact_submissions WHERE DATE(timestamp) BETWEEN '$start_date' AND '$end_date'";

if (!empty($status_filter)) {
    $base_sql .= " AND status = '$status_filter'";
    $count_sql .= " AND status = '$status_filter'";
}

if (!empty($country_filter)) {
    $base_sql .= " AND country = '$country_filter'";
    $count_sql .= " AND country = '$country_filter'";
}

if (!empty($product_filter)) {
    $base_sql .= " AND product_interest LIKE '%$product_filter%'";
    $count_sql .= " AND product_interest LIKE '%$product_filter%'";
}

// Get total submissions
$total_result = $conn->query($count_sql);
$total_submissions = $total_result->fetch_assoc()['total'];

// Get status statistics
$status_sql = "SELECT status, COUNT(*) as count FROM contact_submissions WHERE DATE(timestamp) BETWEEN '$start_date' AND '$end_date' GROUP BY status";
$status_result = $conn->query($status_sql);
$status_stats = [];
while ($row = $status_result->fetch_assoc()) {
    $status_stats[$row['status']] = $row['count'];
}

// Get country statistics
$country_sql = "SELECT country, COUNT(*) as count FROM contact_submissions WHERE DATE(timestamp) BETWEEN '$start_date' AND '$end_date' AND country != '' GROUP BY country ORDER BY count DESC LIMIT 10";
$country_result = $conn->query($country_sql);
$country_stats = [];
while ($row = $country_result->fetch_assoc()) {
    $country_stats[$row['country']] = $row['count'];
}

// Get product interest statistics
$product_sql = "SELECT product_interest, COUNT(*) as count FROM contact_submissions WHERE DATE(timestamp) BETWEEN '$start_date' AND '$end_date' AND product_interest != '' GROUP BY product_interest ORDER BY count DESC LIMIT 10";
$product_result = $conn->query($product_sql);
$product_stats = [];
while ($row = $product_result->fetch_assoc()) {
    $product_stats[$row['product_interest']] = $row['count'];
}

// Get monthly trend
$monthly_sql = "SELECT DATE_FORMAT(timestamp, '%Y-%m') as month, COUNT(*) as count 
                FROM contact_submissions 
                WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
                GROUP BY DATE_FORMAT(timestamp, '%Y-%m') 
                ORDER BY month";
$monthly_result = $conn->query($monthly_sql);
$monthly_stats = [];
while ($row = $monthly_result->fetch_assoc()) {
    $monthly_stats[$row['month']] = $row['count'];
}

// Get unique values for filters
$countries = $conn->query("SELECT DISTINCT country FROM contact_submissions WHERE country != '' ORDER BY country");
$products = $conn->query("SELECT DISTINCT product_interest FROM contact_submissions WHERE product_interest != '' ORDER BY product_interest");

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Monitor | Wain-Sensor</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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
            max-width: 1400px;
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
        
        .monitor-container {
            margin: 2rem auto;
            padding: 0;
        }
        
        .monitor-header {
            background: white;
            padding: 2rem;
            border-radius: 8px 8px 0 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 0;
        }
        
        .header-actions {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .filters-section {
            background: white;
            padding: 1.5rem 2rem;
            margin-bottom: 0;
            border-bottom: 1px solid #eee;
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .filter-group {
            margin-bottom: 0;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2c3e50;
            font-size: 0.9rem;
        }
        
        .filter-group select {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .filter-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        
        .btn {
            padding: 0.6rem 1.2rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: #27ae60;
        }
        
        .btn-success:hover {
            background: #219653;
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding: 2rem;
            background: white;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #f46b45 0%, #eea849 100%);
        }
        
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .charts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 2rem;
            padding: 2rem;
            background: white;
        }
        
        .chart-container {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #eee;
        }
        
        .chart-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .chart-title {
            color: #2c3e50;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .chart-wrapper {
            position: relative;
            height: 300px;
        }
        
        .data-table {
            background: white;
            padding: 2rem;
            margin-top: 0;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .data-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .data-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-new {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .status-contacted {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .status-completed {
            background: #e8f5e8;
            color: #388e3c;
        }
        
        .no-data {
            text-align: center;
            padding: 3rem;
            color: #7f8c8d;
        }
        
        .no-data h3 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        
        .export-section {
            background: white;
            padding: 1.5rem 2rem;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
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
            
            .charts-section {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                min-width: 100%;
            }
            
            .filter-grid {
                grid-template-columns: 1fr;
            }
            
            .header-actions {
                flex-direction: column;
                gap: 1rem;
            }
            
            .filter-actions {
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
                   
                    <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <li><a href="contact_monitor.php" style="background-color: rgba(255, 255, 255, 0.2);">Report</a></li>
                    <li><a href="staff_profile.php">My Profile</a></li>
                    <li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <div class="monitor-container">
            <div class="monitor-header">
                <div class="header-actions">
                    <h1>Contact Form Analytics</h1>
                    <div>
                        <button class="btn btn-success" onclick="generatePDF()">
                            📊 Generate PDF Report
                        </button>
                        <button class="btn btn-outline" onclick="refreshData()">
                            🔄 Refresh Data
                        </button>
                    </div>
                </div>
                <p>Monitor and analyze customer inquiries from contact form submissions</p>
            </div>
            
            <div class="filters-section">
                <form method="GET" id="filterForm">
                    <div class="filter-grid">
                        <div class="filter-group">
                            <label for="date_range">Date Range</label>
                            <select id="date_range" name="date_range" onchange="this.form.submit()">
                                <option value="7days" <?php echo $date_range == '7days' ? 'selected' : ''; ?>>Last 7 Days</option>
                                <option value="30days" <?php echo $date_range == '30days' ? 'selected' : ''; ?>>Last 30 Days</option>
                                <option value="90days" <?php echo $date_range == '90days' ? 'selected' : ''; ?>>Last 90 Days</option>
                                <option value="1year" <?php echo $date_range == '1year' ? 'selected' : ''; ?>>Last Year</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="new" <?php echo $status_filter == 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="contacted" <?php echo $status_filter == 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="country">Country</label>
                            <select id="country" name="country" onchange="this.form.submit()">
                                <option value="">All Countries</option>
                                <?php while ($country = $countries->fetch_assoc()): ?>
                                    <option value="<?php echo $country['country']; ?>" <?php echo $country_filter == $country['country'] ? 'selected' : ''; ?>>
                                        <?php echo $country['country']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="product">Product Interest</label>
                            <select id="product" name="product" onchange="this.form.submit()">
                                <option value="">All Products</option>
                                <?php while ($product = $products->fetch_assoc()): ?>
                                    <option value="<?php echo $product['product_interest']; ?>" <?php echo $product_filter == $product['product_interest'] ? 'selected' : ''; ?>>
                                        <?php echo $product['product_interest']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <a href="contact_monitor.php" class="btn btn-outline">Clear Filters</a>
                        <span class="results-info">Showing data from <?php echo date('M j, Y', strtotime($start_date)); ?> to <?php echo date('M j, Y', strtotime($end_date)); ?></span>
                    </div>
                </form>
            </div>
            
            <?php if ($total_submissions > 0): ?>
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_submissions; ?></div>
                        <div class="stat-label">Total Submissions</div>
                    </div>
                    
                    <div class="stat-card success">
                        <div class="stat-number"><?php echo $status_stats['completed'] ?? 0; ?></div>
                        <div class="stat-label">Completed</div>
                    </div>
                    
                    <div class="stat-card warning">
                        <div class="stat-number"><?php echo $status_stats['contacted'] ?? 0; ?></div>
                        <div class="stat-label">Contacted</div>
                    </div>
                    
                    <div class="stat-card info">
                        <div class="stat-number"><?php echo $status_stats['new'] ?? 0; ?></div>
                        <div class="stat-label">New Inquiries</div>
                    </div>
                </div>
                
                <!-- Charts Section -->
                <div class="charts-section" id="chartsSection">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3 class="chart-title">Submission Trends</h3>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3 class="chart-title">Status Distribution</h3>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3 class="chart-title">Top Countries</h3>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="countryChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3 class="chart-title">Product Interests</h3>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="productChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Data Table -->
                <div class="data-table">
                    <h3>Recent Submissions</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Country</th>
                                    <th>Product Interest</th>
                                    <th>Status</th>
                                    <th>Message Preview</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_sql = $base_sql . " ORDER BY timestamp DESC LIMIT 10";
                                $recent_result = $conn->query($recent_sql);
                                while ($submission = $recent_result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo date('M j, Y', strtotime($submission['timestamp'])); ?></td>
                                    <td><?php echo htmlspecialchars($submission['name']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['email']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['country']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['product_interest']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $submission['status']; ?>">
                                            <?php echo ucfirst($submission['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo substr(htmlspecialchars($submission['message']), 0, 50) . '...'; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <h3>No Data Available</h3>
                    <p>No contact form submissions found for the selected criteria.</p>
                    <a href="contact_monitor.php" class="btn">View All Data</a>
                </div>
            <?php endif; ?>
            
            <div class="export-section">
                <button class="btn btn-success" onclick="generatePDF()">
                    📊 Download PDF Report
                </button>
                <a href="export_contacts.php?<?php echo http_build_query($_GET); ?>" class="btn btn-outline">
                    📥 Export CSV
                </a>
            </div>
        </div>
    </div>

    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($total_submissions > 0): ?>
                // Trend Chart
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                const trendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode(array_keys($monthly_stats)); ?>,
                        datasets: [{
                            label: 'Submissions',
                            data: <?php echo json_encode(array_values($monthly_stats)); ?>,
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
                
                // Status Chart
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                const statusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['New', 'Contacted', 'Completed'],
                        datasets: [{
                            data: [
                                <?php echo $status_stats['new'] ?? 0; ?>,
                                <?php echo $status_stats['contacted'] ?? 0; ?>,
                                <?php echo $status_stats['completed'] ?? 0; ?>
                            ],
                            backgroundColor: [
                                '#3498db',
                                '#f39c12',
                                '#27ae60'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
                
                // Country Chart
                const countryCtx = document.getElementById('countryChart').getContext('2d');
                const countryChart = new Chart(countryCtx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode(array_keys($country_stats)); ?>,
                        datasets: [{
                            label: 'Submissions',
                            data: <?php echo json_encode(array_values($country_stats)); ?>,
                            backgroundColor: '#9b59b6',
                            borderColor: '#8e44ad',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
                
                // Product Chart
                const productCtx = document.getElementById('productChart').getContext('2d');
                const productChart = new Chart(productCtx, {
                    type: 'polarArea',
                    data: {
                        labels: <?php echo json_encode(array_keys($product_stats)); ?>,
                        datasets: [{
                            data: <?php echo json_encode(array_values($product_stats)); ?>,
                            backgroundColor: [
                                '#e74c3c', '#3498db', '#2ecc71', '#f39c12',
                                '#9b59b6', '#1abc9c', '#d35400', '#34495e'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            <?php endif; ?>
        });
        
        // Generate PDF Report
        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');
            const dateRange = document.getElementById('date_range').options[document.getElementById('date_range').selectedIndex].text;
            
            // Add title
            doc.setFontSize(20);
            doc.setTextColor(44, 62, 80);
            doc.text('Contact Form Analytics Report', 20, 30);
            
            // Add date range
            doc.setFontSize(12);
            doc.setTextColor(128, 128, 128);
            doc.text(`Date Range: ${dateRange}`, 20, 40);
            doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 20, 47);
            
            // Add statistics
            doc.setFontSize(16);
            doc.setTextColor(44, 62, 80);
            doc.text('Key Statistics', 20, 65);
            
            doc.setFontSize(12);
            doc.setTextColor(0, 0, 0);
            doc.text(`Total Submissions: ${<?php echo $total_submissions; ?>}`, 20, 80);
            doc.text(`New Inquiries: ${<?php echo $status_stats['new'] ?? 0; ?>}`, 20, 90);
            doc.text(`Contacted: ${<?php echo $status_stats['contacted'] ?? 0; ?>}`, 20, 100);
            doc.text(`Completed: ${<?php echo $status_stats['completed'] ?? 0; ?>}`, 20, 110);
            
            // Add charts (simplified representation)
            doc.setFontSize(16);
            doc.text('Status Distribution', 20, 130);
            doc.setFontSize(12);
            doc.text(`New: ${<?php echo $status_stats['new'] ?? 0; ?>} (${((<?php echo $status_stats['new'] ?? 0; ?> / <?php echo $total_submissions; ?>) * 100).toFixed(1)}%)`, 20, 145);
            doc.text(`Contacted: ${<?php echo $status_stats['contacted'] ?? 0; ?>} (${((<?php echo $status_stats['contacted'] ?? 0; ?> / <?php echo $total_submissions; ?>) * 100).toFixed(1)}%)`, 20, 155);
            doc.text(`Completed: ${<?php echo $status_stats['completed'] ?? 0; ?>} (${((<?php echo $status_stats['completed'] ?? 0; ?> / <?php echo $total_submissions; ?>) * 100).toFixed(1)}%)`, 20, 165);
            
            // Add top countries
            doc.setFontSize(16);
            doc.text('Top Countries', 20, 185);
            doc.setFontSize(12);
            let yPos = 200;
            <?php 
            $count = 0;
            foreach ($country_stats as $country => $count_val): 
                if ($count < 5):
            ?>
                doc.text(`• ${<?php echo json_encode($country); ?>}: ${<?php echo $count_val; ?>}`, 20, <?php echo $yPos; ?>);
                <?php 
                $yPos += 10;
                $count++;
                endif;
            endforeach; 
            ?>
            
            // Save the PDF
            doc.save(`contact-analytics-${dateRange.toLowerCase().replace(' ', '-')}-${new Date().toISOString().split('T')[0]}.pdf`);
        }
        
        // Refresh data
        function refreshData() {
            window.location.reload();
        }
        
        // Auto-refresh every 5 minutes
        setInterval(refreshData, 300000);
    </script>
</body>
</html>
<?php
$conn->close();
?>