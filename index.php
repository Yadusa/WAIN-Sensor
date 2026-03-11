<?php
// index.php
include 'config.php';

// Initialize variables
$page_title = "Wain-Sensor | Precision Sensors for Industrial Applications";
$company_name = "Wain-Sensor";
$year = date('Y');

// Get only 3 featured products for homepage
$featured_sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT 3";
$featured_result = $conn->query($featured_sql);
$featured_products = [];
if ($featured_result->num_rows > 0) {
    while($row = $featured_result->fetch_assoc()) {
        $featured_products[] = $row;
    }
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
        /* Reset and Base Styles */
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
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 5rem 0;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }
        
        .btn {
            display: inline-block;
            background: #00adef;
            color: white;
            padding: 0.8rem 1.8rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #00adef;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #00adef;
            color: #00adef;
            margin-left: 1rem;
        }
        
        .btn-outline:hover {
            background: #00adef;
            color: white;
        }
        
        /* Products Section - Simplified on Homepage */
        .section-title {
            text-align: center;
            margin: 3rem 0 2rem;
            font-size: 2.2rem;
            color: #2c3e50;
        }
        
        .section-subtitle {
            text-align: center;
            font-size: 1.2rem;
            margin-bottom: 3rem;
            color: #7f8c8d;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
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
            background: #00adef;
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
        
        .cta-section {
            text-align: center;
            margin: 3rem 0;
            padding: 3rem 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .cta-section p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-white {
            background: white;
            color: #667eea;
            font-weight: bold;
        }
        
        .btn-white:hover {
            background: #f8f9fa;
            color: #764ba2;
        }
        
        .btn-transparent {
            background: transparent;
            border: 2px solid white;
            color: white;
        }
        
        .btn-transparent:hover {
            background: white;
            color: #667eea;
        }
        
        /* Features Section */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        
        .feature-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #3498db;
        }
        
        .feature-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        /* About Section */
        .about {
            background: white;
            padding: 4rem 0;
            margin: 3rem 0;
        }
        
        .about-content {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 2rem;
        }
        
        .about-text {
            flex: 1;
            min-width: 300px;
        }
        
        .about-image {
            flex: 1;
            min-width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        /* Contact Section */
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
        
        /* Contact Form Styles */
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
        
        /* Footer */
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
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1rem;
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
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-outline {
                margin-left: 0;
                margin-top: 1rem;
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
                    <li><a href="index.php" style="background-color: rgba(255, 255, 255, 0.2);">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="#contact">Inquiry</a></li>
                    
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <h1>Precision Sensors for Industrial Applications</h1>
            <p>Discover our range of high-quality proximity and photoelectric sensors designed for industrial automation and control systems.</p>
            <a href="#products" class="btn">Explore Products</a>
            <a href="#contact" class="btn btn-outline">Get In Touch</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container">
        <h2 class="section-title">Why Choose Wain-Sensor?</h2>
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>High Precision</h3>
                <p>Accurate sensing technology for reliable industrial automation</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3>Durable Design</h3>
                <p>Robust construction built to withstand harsh industrial environments</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>Global Support</h3>
                <p>Comprehensive technical support and worldwide distribution</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔧</div>
                <h3>Easy Integration</h3>
                <p>Simple installation and compatibility with existing systems</p>
            </div>
        </div>
    </section>

    <!-- Products Section - Simplified on Homepage -->
    <section id="products" class="container">
        <h2 class="section-title">Featured Products</h2>
        <p class="section-subtitle">Explore our most popular precision sensors trusted by industries worldwide</p>
        
        <div class="products">
            <?php if (count($featured_products) > 0): ?>
                <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-img">
                        <?php if ($product['image'] && file_exists($product['image'])): ?>
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <?php else: ?>
                            Sensor Image
                        <?php endif; ?>
                        <div class="product-category"><?php echo $product['category']; ?></div>
                    </div>
                    <div class="product-info">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo substr($product['description'], 0, 100) . '...'; ?></p>
                        <div class="product-specs">
                            <div><span class="spec-label">Type:</span> <?php echo $product['type']; ?></div>
                            <div><span class="spec-label">Sensing Range:</span> <?php echo $product['sensing_range']; ?></div>
                            <div><span class="spec-label">Voltage:</span> <?php echo $product['voltage']; ?></div>
                        </div>
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No featured products available at the moment.</p>
            <?php endif; ?>
        </div>
        
        <div class="cta-section">
            <h2>Ready to Find Your Perfect Sensor?</h2>
            <p>Browse our complete catalog with advanced filtering and search capabilities</p>
            <div class="cta-buttons">
                <a href="products.php" class="btn btn-white">View All Products</a>
                <a href="#contact" class="btn btn-transparent">Contact Sales</a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">Product Inquiry</h2>
            
            <div class="contact-form">
                <form id="contactForm" method="post">

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
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="#contact">Inquiry</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p>Email: enquiry@mccis.com.my</p>
                    <p>Phone: +606-317 7555</p>
                    <p>Fax: +606-317 7666
                    <p>Address: 36C, Jalan PB1, Taman Padang Balang, Batu Berendam, 75350 Melaka, Malaysia.</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo $year; ?> Wain-Sensor. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // JavaScript for smooth scrolling
        document.addEventListener('DOMContentLoaded', function() {
            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            
            anchorLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
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