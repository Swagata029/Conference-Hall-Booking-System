<?php
session_start();
if (!isset($_SESSION['Client_id'])) {
    header("Location: index.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "conferencefinal");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$client_id = $_SESSION['Client_id'];
$sql = "SELECT * FROM client WHERE Client_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $client_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$client = mysqli_fetch_assoc($result);

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $verify_sql = "SELECT Client_pass FROM client WHERE Client_id = ?";
    $verify_stmt = mysqli_prepare($conn, $verify_sql);
    mysqli_stmt_bind_param($verify_stmt, "i", $client_id);
    mysqli_stmt_execute($verify_stmt);
    $verify_result = mysqli_stmt_get_result($verify_stmt);
    $stored_pass = mysqli_fetch_assoc($verify_result)['Client_pass'];

    if ($stored_pass === $current_password) {
        if ($new_password === $confirm_password) {
            // Update password in database
            $update_sql = "UPDATE client SET Client_pass = ? WHERE Client_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "si", $new_password, $client_id);
            
            if (mysqli_stmt_execute($update_stmt)) {
                echo "<script>alert('Password updated successfully!');</script>";
            } else {
                echo "<script>alert('Error updating password.');</script>";
            }
        } else {
            echo "<script>alert('New password and confirm password do not match!');</script>";
        }
    } else {
        echo "<script>alert('Current password is incorrect!');</script>";
    }
}

$bookings_sql = "SELECT b.*, ch.Hall_name, p.Amount 
                 FROM booking b 
                 JOIN conference_hall ch ON b.Hall_id = ch.Hall_id 
                 LEFT JOIN payment p ON b.Booking_id = p.Booking_id 
                 WHERE b.Client_id = ?";
$stmt = mysqli_prepare($conn, $bookings_sql);
mysqli_stmt_bind_param($stmt, "i", $client_id);
mysqli_stmt_execute($stmt);
$bookings_result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 15px;
            background-image: url("https://img.freepik.com/free-vector/abstract-background-design-dark-green_53876-59276.jpg");
            background-size: cover;
            background-attachment: fixed;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            position: relative;
            padding-bottom: 80px;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            font-size: 2.2em;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 8px;
        }
        .profile-section {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .profile-field {
            margin-bottom: 15px;
            position: relative;
        }
        .profile-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .profile-field input {
            width: 100%;
            padding: 10px;
            border: 2px solid #3498db;
            border-radius: 6px;
            font-size: 1em;
            color: #2c3e50;
            background-color: #f5f6fa;
            padding-right: 40px;
            box-sizing: border-box;
        }
        .profile-field input:disabled {
            cursor: not-allowed;
            opacity: 0.8;
            background-color: #e8e8e8;
        }
        .password-section {
            display: none;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        .change-password-btn {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1em;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        .change-password-btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3);
        }
        .update-btn {
            background-color: #27ae60;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1em;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        .update-btn:hover {
            background-color: #219a52;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(46, 204, 113, 0.3);
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 35px;
            cursor: pointer;
            color: #3498db;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
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
        .logout-btn {
            position: absolute;
            bottom: 20px;
            right: 120px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Client Profile</h1>
        <form class="profile-section" method="POST">
            <div class="profile-field">
                <label>Name:</label>
                <input type="text" value="<?php echo htmlspecialchars($client['Fname'] . ' ' . $client['Lname']); ?>" disabled>
            </div>
            <div class="profile-field">
                <label>Email:</label>
                <input type="email" value="<?php echo htmlspecialchars($client['Email']); ?>" disabled>
            </div>
            <div class="profile-field">
                <label>Phone Number:</label>
                <input type="tel" value="<?php echo htmlspecialchars($client['Phone']); ?>" disabled>
            </div>
            <div class="profile-field">
                <label>Organization:</label>
                <input type="text" value="<?php echo htmlspecialchars($client['Organization']); ?>" disabled>
            </div>
            <button type="button" class="change-password-btn" onclick="togglePasswordSection()">Change Password</button>
            <div class="password-section" id="passwordSection">
                <div class="profile-field">
                    <label>Current Password:</label>
                    <input type="password" name="current_password" id="currentPassword" required>
                    <i class="fas fa-eye-slash toggle-password" onclick="togglePasswordVisibility('currentPassword', this)"></i>
                </div>
                <div class="profile-field">
                    <label>New Password:</label>
                    <input type="password" name="new_password" id="newPassword" required>
                    <i class="fas fa-eye-slash toggle-password" onclick="togglePasswordVisibility('newPassword', this)"></i>
                </div>
                <div class="profile-field">
                    <label>Confirm Password:</label>
                    <input type="password" name="confirm_password" id="confirmPassword" required>
                    <i class="fas fa-eye-slash toggle-password" onclick="togglePasswordVisibility('confirmPassword', this)"></i>
                </div>
                <button type="submit" class="update-btn">Update Password</button>
            </div>
        </form>

        <div class="profile-section">
            <h1>Booking Management</h1>
            <table>
                <thead>
                    <tr>
                        <th>Hall Name</th>
                        <th>Booking Date</th>
                        <th>Start Date/Time</th>
                        <th>End Date/Time</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = mysqli_fetch_assoc($bookings_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['Hall_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['Booking_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['Start_date'] . ' ' . $booking['Start_time']); ?></td>
                            <td><?php echo htmlspecialchars($booking['End_date'] . ' ' . $booking['End_time']); ?></td>
                            <td><?php echo htmlspecialchars($booking['Status']); ?></td>
                            <td>₹<?php echo number_format($booking['Amount'], 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
        <button class="home-btn" onclick="window.location.href='client_homepage.php'">Home</button>
    </div>
    <script>
        function togglePasswordSection() {
            const passwordSection = document.getElementById('passwordSection');
            passwordSection.style.display = passwordSection.style.display === 'none' ? 'block' : 'none';
        }

        function togglePasswordVisibility(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

    </script>
</body>
</html>
