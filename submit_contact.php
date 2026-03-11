<?php
// submit_contact.php - Backup method for Google Sheets integration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $country = $_POST['country'] ?? '';
    $state = $_POST['state'] ?? '';
    $product_interest = $_POST['product_interest'] ?? '';
    $message = $_POST['message'] ?? '';
    $timestamp = $_POST['timestamp'] ?? date('Y-m-d H:i:s');
    $source = 'Wain-Sensor Website (Backup)';
    
    // Validate required fields
    $required_fields = ['name', 'email', 'phone', 'country', 'state', 'message'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        http_response_code(400);
        echo "Please fill in all required fields: " . implode(', ', $missing_fields);
        exit();
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Please enter a valid email address.";
        exit();
    }
    
    // SIMPLIFIED phone validation - just check if it's not empty
    if (empty(trim($phone))) {
        http_response_code(400);
        echo "Please enter your phone number.";
        exit();
    }
    
    // Send to Google Sheets via server-side
    $result = sendToGoogleSheets($name, $email, $phone, $country, $state, $product_interest, $message, $timestamp, $source);
    
    if ($result) {
        // Also send email notification
        sendEmailNotification($name, $email, $phone, $country, $state, $product_interest, $message);
        
        http_response_code(200);
        echo "Thank you for your message! We'll get back to you soon.";
    } else {
        http_response_code(500);
        echo "Sorry, there was an error. Please try again or contact us directly at enquiry@mccis.com.my";
    }
} else {
    http_response_code(405);
    echo "Method not allowed.";
}

function sendToGoogleSheets($name, $email, $phone, $country, $state, $product_interest, $message, $timestamp, $source) {
    // Replace with your Google Apps Script Web App URL
    $webAppUrl = "https://script.google.com/macros/s/AKfycbx_xC7FyEtuwuzf1eYztq_HNC5v4UQzclnMwn8NydUX4FF1tmKKbTJnJRrRJtOFaPfJ/exec";
    
    $data = array(
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'country' => $country,
        'state' => $state,
        'product_interest' => $product_interest,
        'message' => $message,
        'timestamp' => $timestamp,
        'source' => $source
    );
    
    // Use cURL to send data
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webAppUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ($httpCode == 200);
}

function sendEmailNotification($name, $email, $phone, $country, $state, $product_interest, $message) {
    $to = "enquiry@mccis.com.my";
    $subject = "New Contact Form Submission - Wain-Sensor";
    $headers = "From: website@wain-sensor.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $email_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #f4f4f4; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            .content { padding: 20px 0; }
            .field { margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-radius: 3px; }
            .label { font-weight: bold; color: #333; display: inline-block; width: 120px; }
            .message { background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 3px; margin-top: 10px; }
            .contact-info { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; }
            @media (max-width: 600px) {
                .contact-info { grid-template-columns: 1fr; }
                .label { width: 100px; }
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
                <p>Website: Wain-Sensor | Time: " . date('Y-m-d H:i:s') . "</p>
            </div>
            <div class='content'>
                <div class='contact-info'>
                    <div class='field'><span class='label'>Name:</span> $name</div>
                    <div class='field'><span class='label'>Email:</span> <a href='mailto:$email'>$email</a></div>
                    <div class='field'><span class='label'>Phone:</span> $phone</div>
                    <div class='field'><span class='label'>Country:</span> $country</div>
                    <div class='field'><span class='label'>State:</span> $state</div>
                    <div class='field'><span class='label'>Product Interest:</span> " . ($product_interest ?: 'Not specified') . "</div>
                </div>
                <div class='field'>
                    <span class='label'>Message:</span>
                    <div class='message'>" . nl2br(htmlspecialchars($message)) . "</div>
                </div>
            </div>
        </div>
    </body>
    </html>";
    
    // Send email
    @mail($to, $subject, $email_body, $headers);
}
?>