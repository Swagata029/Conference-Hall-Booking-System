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

// Handle booking cancellation
if(isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    // First check if booking exists and belongs to client
    $check_query = "SELECT Status FROM booking WHERE Booking_id = $booking_id AND Client_id = $client_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) > 0) {
        $booking = mysqli_fetch_assoc($check_result);
        if($booking['Status'] == 'Pending' || $booking['Status'] == 'Confirmed') {
            $delete_query = "DELETE FROM booking WHERE Booking_id = $booking_id AND Client_id = $client_id";
            if(mysqli_query($conn, $delete_query)) {
                echo "<script>alert('Booking cancelled successfully!'); window.location.href='My_Bookings_client.php';</script>";
            } else {
                echo "<script>alert('Error cancelling booking.');</script>";
            }
        } else {
            echo "<script>alert('Only pending or confirmed bookings can be cancelled.');</script>";
        }
    } else {
        echo "<script>alert('Invalid booking.');</script>";
    }
}

// Get status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Modify query based on status filter
$query = "SELECT b.Booking_id, b.Booking_date, b.Start_date, b.Start_time, 
          b.End_date, b.End_time, b.Status, p.Amount
          FROM booking b
          LEFT JOIN payment p ON b.Booking_id = p.Booking_id 
          WHERE b.Client_id = $client_id";

if($status_filter != 'all') {
    $query .= " AND b.Status = '$status_filter'";
}
$query .= " ORDER BY b.Booking_date DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-image: url("https://img.freepik.com/free-vector/abstract-background-design-dark-green_53876-59276.jpg");
            background-size: cover;
            background-attachment: fixed;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            position: relative;
            min-height: 100vh;
            padding-bottom: 100px;
            backdrop-filter: blur(5px);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .action-btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: inline-block;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .filter-section {
            margin-bottom: 20px;
            text-align: right;
        }
        .filter-section select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            background-color: white;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #2c3e50;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        .status {
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .pending { 
            background-color: #fff3cd; 
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .confirmed { 
            background-color: #d4edda; 
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .completed { 
            background-color: #cce5ff; 
            color: #004085;
            border: 1px solid #b8daff;
        }
        .home-btn {
            position: fixed;
            bottom: 30px;
            right: 80px;
            background-color: #2196F3;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 1000;
            text-decoration: none;
        }
        .home-btn:hover {
            background-color: #1976D2;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>MY BOOKINGS</h1>
        
        <div class="filter-section">
            <form method="get" id="filterForm">
                <select name="status" onchange="this.form.submit()">
                    <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Bookings</option>
                    <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Confirmed" <?php echo $status_filter == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Booking Date</th>
                    <th>Start Date/Time</th>
                    <th>End Date/Time</th>
                    <th>Total Cost</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $status_class = strtolower($row['Status']);
                        echo "<tr>";
                        echo "<td>" . $row['Booking_id'] . "</td>";
                        echo "<td>" . $row['Booking_date'] . "</td>";
                        echo "<td>" . $row['Start_date'] . " " . $row['Start_time'] . "</td>";
                        echo "<td>" . $row['End_date'] . " " . $row['End_time'] . "</td>";
                        echo "<td>₹" . number_format($row['Amount'], 2) . "</td>";
                        echo "<td><span class='status " . $status_class . "'>" . $row['Status'] . "</span></td>";
                        echo "<td>";
                        if($row['Status'] == 'Pending' || $row['Status'] == 'Confirmed') {
                            echo "<form method='post' style='display:inline;'>";
                            echo "<input type='hidden' name='booking_id' value='" . $row['Booking_id'] . "'>";
                            echo "<button type='submit' name='cancel_booking' class='action-btn' style='background-color: #dc3545;' onclick='return confirm(\"Are you sure you want to cancel this booking?\")'>Cancel</button>";
                            echo "</form>";
                        } else if($row['Status'] == 'Completed') {
                            echo "<span class='status completed'>Booking completed</span>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No bookings found</td></tr>";
                }
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>

    <a href="client_homepage.php" class="home-btn">Home</a>
</body>
</html>
