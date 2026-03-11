<?php
// database_setup.php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS wain_sensor_db";
if ($conn->query($sql)) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select database
$conn->select_db("wain_sensor_db");

// Create products table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    type VARCHAR(100) NOT NULL,
    dia VARCHAR(100) NOT NULL,
    sensing_range VARCHAR(100) NOT NULL,
    output VARCHAR(100) NOT NULL,
    voltage VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    housing_material VARCHAR(100),
    mounting VARCHAR(100),
    operating_temp VARCHAR(100),
    protection_rating VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "Products table created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

// Create users table for admin
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

// Insert default admin user (username: admin, password: admin123)
$passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT IGNORE INTO users (username, password, role) VALUES ('admin', '$passwordHash', 'admin')";

if ($conn->query($sql)) {
    echo "Default admin user created<br>";
} else {
    echo "Error creating admin user: " . $conn->error;
}

// Insert sample products
$sampleProducts = [
    // Proximity Sensors
    [
        "name" => "DC Ultra-Mini Inductive Proximity Sensor",
        "category" => "Proximity Sensor",
        "type" => "Inductive",
        "dia" => "Φ3, M4, Φ4, M5",
        "sensing_range" => "1.5mm",
        "output" => "NPN/PNP NO/NC",
        "voltage" => "10-30VDC",
        "description" => "Ultra-miniature inductive proximity sensors with stainless steel housing.",
        "housing_material" => "Stainless Steel",
        "mounting" => "Flush",
        "operating_temp" => "-25°C to 70°C",
        "protection_rating" => "IP67"
    ],
    [
        "name" => "DC Mini-Shorter Inductive Proximity Sensor",
        "category" => "Proximity Sensor",
        "type" => "Inductive",
        "dia" => "Φ6.5, M8",
        "sensing_range" => "2.0mm",
        "output" => "NPN/PNP NO/NC",
        "voltage" => "10-30VDC",
        "description" => "Compact inductive proximity sensors with shorter housing.",
        "housing_material" => "Stainless Steel",
        "mounting" => "Flush",
        "operating_temp" => "-25°C to 70°C",
        "protection_rating" => "IP67"
    ],
    // Add more sample products as needed
];

foreach ($sampleProducts as $product) {
    $name = $conn->real_escape_string($product['name']);
    $category = $conn->real_escape_string($product['category']);
    $type = $conn->real_escape_string($product['type']);
    $dia = $conn->real_escape_string($product['dia']);
    $sensing_range = $conn->real_escape_string($product['sensing_range']);
    $output = $conn->real_escape_string($product['output']);
    $voltage = $conn->real_escape_string($product['voltage']);
    $description = $conn->real_escape_string($product['description']);
    $housing_material = $conn->real_escape_string($product['housing_material']);
    $mounting = $conn->real_escape_string($product['mounting']);
    $operating_temp = $conn->real_escape_string($product['operating_temp']);
    $protection_rating = $conn->real_escape_string($product['protection_rating']);
    
    $sql = "INSERT IGNORE INTO products (name, category, type, dia, sensing_range, output, voltage, description, housing_material, mounting, operating_temp, protection_rating) 
            VALUES ('$name', '$category', '$type', '$dia', '$sensing_range', '$output', '$voltage', '$description', '$housing_material', '$mounting', '$operating_temp', '$protection_rating')";
    
    if ($conn->query($sql)) {
        echo "Product '{$product['name']}' inserted successfully<br>";
    } else {
        echo "Error inserting product: " . $conn->error . "<br>";
    }
}

$conn->close();
echo "Database setup completed!";
?>