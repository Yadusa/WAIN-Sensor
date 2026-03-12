<?php
include 'config.php';
$page_title = "About Us | Wain-Sensor";
$company_name = "Wain-Sensor";
$year = date('Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $page_title; ?></title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
}

body{
    background:#f8f9fa;
    color:#333;
    line-height:1.6;
}

.container{
    width:90%;
    max-width:1200px;
    margin:auto;
}

header{
    background:#00adef;
    color:white;
    padding:1rem 0;
    position:sticky;
    top:0;
}

.header-content{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    height:25px;
}

nav ul{
    display:flex;
    list-style:none;
}

nav ul li{
    margin-left:1.5rem;
}

nav ul li a{
    color:white;
    text-decoration:none;
    font-weight:500;
    padding:0.5rem;
    border-radius:4px;
}

nav ul li a:hover{
    background:rgba(255,255,255,0.2);
}

.section-title{
    text-align:center;
    margin:3rem 0 2rem;
    font-size:2.2rem;
    color:#2c3e50;
}

/* About section */
.about{
    background:white;
    padding:4rem 0;
    margin-top:3rem;
}

.about-content{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:2rem;
}

.about-text{
    flex:1;
    min-width:300px;
}

.about-image{
    flex:1;
    min-width:300px;
    height:300px;
    background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:8px;
    color:white;
    font-size:1.2rem;
    font-weight:bold;
}

/* Footer */
footer{
    background:#2c3e50;
    color:white;
    padding:3rem 0 1rem;
    margin-top:3rem;
}

.footer-content{
    display:flex;
    flex-wrap:wrap;
    justify-content:space-between;
    gap:2rem;
    margin-bottom:2rem;
}

.footer-section{
    flex:1;
    min-width:250px;
}

.footer-section h3{
    margin-bottom:1rem;
}

.footer-bottom{
    text-align:center;
    padding-top:1rem;
    border-top:1px solid rgba(255,255,255,0.1);
}
</style>
</head>

<body>

<header>
<div class="container header-content">
    <img src="wain-sensor-logo.png" class="logo">
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="about.php" style="background:rgba(255,255,255,0.2);">About Us</a></li>
            <li><a href="index.php#contact">Inquiry</a></li>
        </ul>
    </nav>
</div>
</header>

<section class="about">
<div class="container">

    <h2 class="section-title">About Wain-Sensor</h2>

    <div class="about-content">

        <div class="about-text">
            <p>
                Wain-Sensor has been a leading provider of precision sensor technology since 2010. 
                We specialize in developing and manufacturing high-quality proximity and 
                photoelectric sensors for a wide range of industrial applications.
            </p>

            <p>
                Our team of experienced engineers and technicians ensures that every product 
                meets the highest standards of accuracy, reliability, and durability.
            </p>

            <p>
                With customers in over 30 countries, we're committed to delivering innovative 
                sensing solutions that help businesses and individuals make better decisions 
                based on accurate data.
            </p>
        </div>

        <div class="about-image">
            Industry Innovation
        </div>

    </div>
</div>
</section>

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
                <li><a href="index.php#contact">Inquiry</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Contact Info</h3>
            <p>Email: enquiry@mccis.com.my</p>
            <p>Phone: +606-317 7555</p>
            <p>Fax: +606-317 7666</p>
            <p>Address: 36C, Jalan PB1, Taman Padang Balang, Batu Berendam, 75350 Melaka, Malaysia.</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo $year; ?> Wain-Sensor. All rights reserved.</p>
    </div>
</div>
</footer>

</body>
</html>

<?php
$conn->close();
?>