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

// Get client details
$client_id = $_SESSION['Client_id'];
$sql = "SELECT * FROM client WHERE Client_id = " . intval($client_id);
$client_result = mysqli_query($conn, $sql);
$client_data = mysqli_fetch_assoc($client_result);

// Parse URL parameters
$services_json = isset($_GET['services']) ? urldecode($_GET['services']) : null;
$services_data = json_decode($services_json, true);

// Extract data from services JSON
$room_id = isset($services_data['room_id']) ? intval($services_data['room_id']) : null;
$hall_id = isset($services_data['hall_id']) ? intval($services_data['hall_id']) : null;
$services = isset($services_data['services']) ? $services_data['services'] : [];
$total_amount = isset($services_data['total_amount']) ? floatval($services_data['total_amount']) : 0;

if (!$hall_id || !$room_id) {
    die("Hall ID and Room ID are required");
}

// Get room and hall details
$sql = "SELECT cr.*, ch.Hall_name 
        FROM conference_room cr
        JOIN conference_hall ch ON cr.Hall_id = ch.Hall_id 
        WHERE cr.Room_id = " . intval($room_id) . " 
        AND cr.Hall_id = " . intval($hall_id);
$room_result = mysqli_query($conn, $sql);
$room_data = mysqli_fetch_assoc($room_result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mysqli_begin_transaction($conn);
    try {
        // Get next booking ID
        $next_booking_id_query = "SELECT MAX(Booking_id) as max_id FROM booking";
        $result = mysqli_query($conn, $next_booking_id_query);
        $row = mysqli_fetch_assoc($result);
        $next_booking_id = $row['max_id'] ? $row['max_id'] + 1 : 1;

        // Insert booking record
        $booking_date = date('Y-m-d');
        $start_date = date('Y-m-d'); 
        $end_date = date('Y-m-d');
        $start_time = '09:00:00';
        $end_time = '17:00:00';
        $status = 'Confirmed';

        $booking_sql = "INSERT INTO booking (Booking_id, Booking_date, Start_date, Start_time, End_date, End_time, Status, Hall_id, Client_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $booking_sql);
        mysqli_stmt_bind_param($stmt, "issssssii", $next_booking_id, $booking_date, $start_date, $start_time, $end_date, $end_time, $status, $hall_id, $client_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error creating booking record: " . mysqli_error($conn));
        }

        // Update room status
        $update_room_sql = "UPDATE conference_room SET room_status = 'unavailable' WHERE Room_id = ?";
        $stmt = mysqli_prepare($conn, $update_room_sql);
        mysqli_stmt_bind_param($stmt, "i", $room_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error updating room status");
        }

        // Get next payment ID
        $next_id_query = "SELECT MAX(Payment_id) as max_id FROM payment";
        $result = mysqli_query($conn, $next_id_query);
        $row = mysqli_fetch_assoc($result);
        $next_payment_id = $row['max_id'] ? $row['max_id'] + 1 : 1;

        // Insert payment record
        $payment_method = $_POST['paymentMethod'];
        $payment_date = date('Y-m-d');
        $payment_status = 'Completed';
        
        $payment_sql = "INSERT INTO payment (Payment_id, Amount, Payment_date, Payment_status, Payment_method, Booking_id) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $payment_sql);
        mysqli_stmt_bind_param($stmt, "idsssi", $next_payment_id, $total_amount, $payment_date, $payment_status, $payment_method, $next_booking_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error creating payment record");
        }

        // Insert services
        if (!empty($services)) {
            $next_service_id_query = "SELECT MAX(Service_id) as max_id FROM service";
            $result = mysqli_query($conn, $next_service_id_query);
            $row = mysqli_fetch_assoc($result);
            $next_service_id = $row['max_id'] ? $row['max_id'] + 1 : 1;

            $service_sql = "INSERT INTO service (Service_id, Service_type_id, Quantity, Booking_id) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $service_sql);

            foreach ($services as $service) {
                $service_id = intval($service['id']);
                $quantity = intval($service['quantity']);
                
                mysqli_stmt_bind_param($stmt, "iiii", $next_service_id, $service_id, $quantity, $next_booking_id);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error inserting service record");
                }
                $next_service_id++;
            }
        }

        mysqli_commit($conn);
        header("Location: Payment_Success.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error_message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
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
            max-width: 1100px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
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
        h2 {
            color: #2c3e50;
            text-align: left;
            font-size: 1.6em;
            margin-bottom: 15px;
            padding-left: 8px;
            border-left: 4px solid #e74c3c;
        }
        .section {
            margin-bottom: 25px;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .section:hover {
            transform: translateY(-3px);
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
        }
        .detail-item label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
            font-size: 1em;
        }
        .detail-item span {
            color: #34495e;
            font-size: 1em;
        }
        .bill-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 15px 0;
            border-radius: 6px;
            overflow: hidden;
        }
        .bill-table th, .bill-table td {
            padding: 12px;
            text-align: left;
        }
        .bill-table th {
            background-color: #3498db;
            color: white;
            font-weight: 500;
        }
        .bill-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .bill-table tr:hover {
            background-color: #e8f4f8;
        }
        .total-row {
            font-weight: bold;
            background-color: #2c3e50 !important;
            color: white;
        }
        .payment-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .payment-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #3498db;
            border-radius: 6px;
            font-size: 1em;
            color: #2c3e50;
            background-color: white;
        }
        .submit-btn {
            background-color: #2ecc71;
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
        }
        .submit-btn:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(46, 204, 113, 0.3);
        }
        .error-message {
            color: #e74c3c;
            text-align: center;
            padding: 10px;
            margin: 10px 0;
            background-color: #fde2e2;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Payment Portal</h1>
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="section">
            <h2>Client Details</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <label>Name:</label>
                    <span><?php echo htmlspecialchars($client_data['Fname'] . ' ' . $client_data['Lname']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Email:</label>
                    <span><?php echo htmlspecialchars($client_data['Email']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Phone:</label>
                    <span><?php echo htmlspecialchars($client_data['Phone']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Organization:</label>
                    <span><?php echo htmlspecialchars($client_data['Organization']); ?></span>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Booking Details</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <label>Hall Name:</label>
                    <span><?php echo htmlspecialchars($room_data['Hall_name']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Room ID:</label>
                    <span><?php echo htmlspecialchars($room_id); ?></span>
                </div>
                <div class="detail-item">
                    <label>Booking Date:</label>
                    <span><?php echo date('Y-m-d'); ?></span>
                </div>
                <div class="detail-item">
                    <label>Duration:</label>
                    <span>09:00 AM - 05:00 PM</span>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Bill Details</h2>
            <table class="bill-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Room Charge</td>
                        <td>1</td>
                        <td><?php echo number_format($room_data['Price'], 2); ?></td>
                    </tr>
                    <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($service['name']); ?></td>
                        <td><?php echo htmlspecialchars($service['quantity']); ?></td>
                        <td><?php echo number_format($service['total'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2">Total Amount</td>
                        <td><?php echo number_format($total_amount, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Payment Method</h2>
            <form class="payment-form" method="POST">
                <select id="paymentMethod" name="paymentMethod" required>
                    <option value="">Select Payment Method</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="UPI">UPI</option>
                </select>
                <button type="submit" class="submit-btn">Proceed to Payment</button>
            </form>
        </div>
    </div>

    <script>
        document.querySelector('.payment-form').addEventListener('submit', function(e) {
            const paymentMethod = document.getElementById('paymentMethod').value;
            if(!paymentMethod) {
                e.preventDefault();
                alert('Please select a payment method');
            }
        });
    </script>
</body>
</html>
