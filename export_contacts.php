<?php
// export_contacts.php
include 'config.php';
validateSession();

// Get filter parameters (same as monitor)
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : '30days';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$country_filter = isset($_GET['country']) ? $_GET['country'] : '';
$product_filter = isset($_GET['product']) ? $_GET['product'] : '';

// Calculate date range (same logic as monitor)
// ... [include the same date calculation logic]

// Build query
$export_sql = "SELECT * FROM contact_submissions WHERE DATE(timestamp) BETWEEN '$start_date' AND '$end_date'";

if (!empty($status_filter)) {
    $export_sql .= " AND status = '$status_filter'";
}

if (!empty($country_filter)) {
    $export_sql .= " AND country = '$country_filter'";
}

if (!empty($product_filter)) {
    $export_sql .= " AND product_interest LIKE '%$product_filter%'";
}

$export_sql .= " ORDER BY timestamp DESC";

$result = $conn->query($export_sql);

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=contact-submissions-' . date('Y-m-d') . '.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, [
    'Timestamp', 'Name', 'Email', 'Phone', 'Country', 'State', 
    'Product Interest', 'Message', 'Status', 'Source'
]);

// Add data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['timestamp'],
        $row['name'],
        $row['email'],
        $row['phone'],
        $row['country'],
        $row['state'],
        $row['product_interest'],
        $row['message'],
        $row['status'],
        $row['source']
    ]);
}

fclose($output);
exit();
?>