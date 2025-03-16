<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['Client_id']) && !isset($_SESSION['Admin_id'])) {
    header("Location: index.php");
    exit();
}

// Determine redirect URL based on user type
$redirectUrl = isset($_SESSION['Client_id']) ? "client_homepage.php" : "admin_homepage.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url("https://img.freepik.com/free-vector/abstract-background-design-dark-green_53876-59276.jpg");
            background-size: cover;
            background-attachment: fixed;
        }

        .success-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
            width: 90%;
            animation: fadeIn 1s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success-icon {
            color: #4CAF50;
            font-size: 80px;
            margin-bottom: 20px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2.5em;
            background: linear-gradient(45deg, #2c3e50, #3498db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            color: #666;
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        .home-button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 1.1em;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .home-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1>Payment Successful!</h1>
        <p>Your transaction has been completed successfully. Thank you for your payment.</p>
        <a href="<?php echo $redirectUrl; ?>" class="home-button">Proceed to Homepage</a>
    </div>
</body>
</html>
