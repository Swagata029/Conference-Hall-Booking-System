<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Care - Conference Hall Booking</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #e3f2fd;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            background-color: white;
            border-radius: 10px;
            position: relative;
            padding-bottom: 80px;
        }
        .contact-info {
            background: linear-gradient(135deg, #bbdefb 0%, #e3f2fd 100%);
            padding: 25px;
            border-radius: 8px;
            margin-top: 25px;
            border: 1px solid #90caf9;
        }
        h1 {
            color: #1976d2;
            font-size: 2.5em;
            text-align: center;
            margin-bottom: 30px;
        }
        h2 {
            color: #1976d2;
            border-bottom: 2px solid #42a5f5;
            padding-bottom: 10px;
        }
        .contact-method {
            margin: 20px 0;
            padding: 15px;
            background-color: white;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .contact-method i {
            margin-right: 15px;
            color: #42a5f5;
            font-size: 1.2em;
            width: 20px;
            text-align: center;
        }
        .contact-method strong {
            color: #1976d2;
            font-size: 1.1em;
        }
        a {
            color: #42a5f5;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #1a237e;
        }
        .home-btn {
            position: absolute;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #1976d2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .home-btn:hover {
            background-color: #1565c0;
        }
        .easter-egg {
            position: fixed;
            bottom: 10px;
            left: 10px;
            font-size: 10px;
            color: #e3f2fd;
            cursor: help;
        }
        .easter-egg:hover {
            color: #1976d2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Customer Care Center</h1>
        
        <section>
            <h2>Welcome to Premium Support</h2>
            <p>As your trusted partner in conference hall bookings, our dedicated team of professionals is committed to providing you with exceptional service and support for all your venue needs.</p>
        </section>

        <section class="contact-info">
            <h2>Contact Information</h2>
            
            <div class="contact-method">
                <i class="fas fa-phone"></i>
                <strong>Professional Support Line:</strong><br>
                Direct Line: +91 (555) 123-4567<br>
                Toll-Free: 1-800-CONF-HALL<br>
                Business Hours: Monday - Friday, 9:00 AM - 6:00 PM IST
            </div>

            <div class="contact-method">
                <i class="fas fa-envelope"></i>
                <strong>Email Departments:</strong><br>
                Client Relations: support@conferencehall.com<br>
                Venue Coordination: bookings@conferencehall.com<br>
                Technical Assistance: tech@conferencehall.com
            </div>

            <div class="contact-method">
                <i class="fas fa-clock"></i>
                <strong>Service Response Times:</strong><br>
                Phone Support: Immediate assistance during business hours<br>
                Email Correspondence: Within 24 business hours guaranteed
            </div>
        </section>

        <section>
            <h2>24/7 Emergency Support</h2>
            <p>For urgent matters requiring immediate attention outside of regular business hours, our dedicated emergency response team is available at:<br>
            <strong>+91 (555) 999-8888</strong></p>
        </section>

        <button class="home-btn" onclick="window.location.href='client_homepage.php'">Home</button>
    </div>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <!-- Japanese Easter Egg -->
    <div class="easter-egg" title="がんばって! (Ganbatte! - Do your best!)">
        頑張って
    </div>
</body>
</html>
