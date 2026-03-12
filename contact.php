<?php
include 'config.php';

$page_title = "Inquiry | Wain-Sensor";
$company_name = "Wain-Sensor";
$year = date('Y');

// Fetch products for the dropdown
$all_products = [];
$result = $conn->query("SELECT name FROM products ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $all_products[] = $row;
    }
}

// Handle Form Submission
$message_status = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $country = $conn->real_escape_string($_POST['country']);
    $state = $conn->real_escape_string($_POST['state']);
    $product_interest = $conn->real_escape_string($_POST['product_interest']);
    $user_message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO contact_inquiries (name, email, phone, country, state, product_interest, message) 
            VALUES ('$name', '$email', '$phone', '$country', '$state', '$product_interest', '$user_message')";

    if ($conn->query($sql)) {
        $message_status = "<div class='success-msg'>Thank you! Your inquiry has been sent.</div>";
    } else {
        $message_status = "<div class='error-msg'>Error: " . $conn->error . "</div>";
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
        }
        
        .contact-form {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
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
    <header>
        <div class="container header-content">
            <div class="logo-container">
                <img src="wain-sensor-logo.png" alt="Wain-Sensor Logo" class="logo">
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php" >Products</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php" style="background-color: rgba(255, 255, 255, 0.2);">Inquiry</a></li>
                </ul>
            </nav>
        </div>
    </header>

<section id="contact" class="container">
        <h2 class="section-title" style="text-align:center; margin-top:2rem;">Product Inquiry - product request</h2>
        <div class="contact">
        <div class="contact-form">
            <?php echo $message_status; ?>
            
            <form id="contactForm" method="post" action="contact.php">

                <div class="form-group">
                    <label for="message">Please provide brand, model, description or others *</label>
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

<script>
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
        
        // Send to PHP endpoint
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