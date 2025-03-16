<?php
session_start();
if (!isset($_SESSION['Admin_id'])) {
    header("Location: index.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "conferencefinal");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle delete action
if(isset($_POST['delete'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    
    $delete_query = "DELETE FROM booking WHERE Booking_id = '$booking_id'";
    if(mysqli_query($conn, $delete_query)) {
        header("Location: My_Bookings_admin.php");
        exit();
    }
}

// Handle edit form submission
if(isset($_POST['update'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']); 
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $hall_id = mysqli_real_escape_string($conn, $_POST['hall_id']);
    $client_id = mysqli_real_escape_string($conn, $_POST['client_id']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);

    // Update booking table
    $update_booking = "UPDATE booking SET 
        Start_date = '$start_date',
        Start_time = '$start_time',
        End_date = '$end_date', 
        End_time = '$end_time',
        Status = '$status',
        Hall_id = '$hall_id',
        Client_id = '$client_id'
        WHERE Booking_id = '$booking_id'";

    // Update payment table if amount changed
    $update_payment = "UPDATE payment SET
        Amount = '$amount'
        WHERE Booking_id = '$booking_id'";

    if(mysqli_query($conn, $update_booking) && mysqli_query($conn, $update_payment)) {
        header("Location: My_Bookings_admin.php");
        exit();
    }
}

// Get booking details if in edit mode
$editing = false;
$edit_data = null;
if(isset($_GET['edit'])) {
    $editing = true;
    $booking_id = mysqli_real_escape_string($conn, $_GET['edit']);
    
    $edit_query = "SELECT b.*, p.Amount, c.Fname, c.Lname 
                   FROM booking b
                   LEFT JOIN payment p ON b.Booking_id = p.Booking_id
                   LEFT JOIN client c ON b.Client_id = c.Client_id
                   WHERE b.Booking_id = '$booking_id'";
                   
    $edit_result = mysqli_query($conn, $edit_query);
    $edit_data = mysqli_fetch_assoc($edit_result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Booking Management</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-image:url("https://img.freepik.com/free-vector/abstract-background-design-dark-green_53876-59276.jpg");
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            min-height: 100vh;
            padding-bottom: 100px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .search-bar, .filter-options {
            margin-bottom: 20px;
        }
        .search-bar input, .filter-options select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .action-buttons {
            margin-bottom: 20px;
        }
        .action-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .action-btn:hover {
            opacity: 0.8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
        .pending { background-color: #ffeeba; color: #856404; }
        .confirmed { background-color: #d4edda; color: #155724; }
        .completed { background-color: #d1ecf1; color: #0c5460; }
        .home-btn {
            position: fixed;
            bottom: 30px;
            right: 80px;
            background-color: #2196F3;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 1000;
            text-decoration: none;
        }
        .home-btn:hover {
            background-color: #1976D2;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .edit-btn {
            background-color: #FFC107;
            color: #000;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 5px;
        }
        .delete-btn {
            background-color: #DC3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .edit-form input, .edit-form select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .edit-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>BOOKING MANAGEMENT</h1>
        
        <?php if($editing && $edit_data): ?>
        <div class="edit-form">
            <h2>Edit Booking #<?php echo $edit_data['Booking_id']; ?></h2>
            <form method="POST">
                <input type="hidden" name="booking_id" value="<?php echo $edit_data['Booking_id']; ?>">
                
                <label>Start Date:</label>
                <input type="date" name="start_date" value="<?php echo $edit_data['Start_date']; ?>" required>
                
                <label>Start Time:</label>
                <input type="time" name="start_time" value="<?php echo $edit_data['Start_time']; ?>" required>
                
                <label>End Date:</label>
                <input type="date" name="end_date" value="<?php echo $edit_data['End_date']; ?>" required>
                
                <label>End Time:</label>
                <input type="time" name="end_time" value="<?php echo $edit_data['End_time']; ?>" required>
                
                <label>Status:</label>
                <select name="status" required>
                    <option value="Pending" <?php echo ($edit_data['Status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Confirmed" <?php echo ($edit_data['Status'] == 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="Completed" <?php echo ($edit_data['Status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                </select>
                
                <label>Hall ID:</label>
                <input type="number" name="hall_id" value="<?php echo $edit_data['Hall_id']; ?>" required>
                
                <label>Client ID:</label>
                <input type="number" name="client_id" value="<?php echo $edit_data['Client_id']; ?>" required>
                
                <label>Amount:</label>
                <input type="number" step="0.01" name="amount" value="<?php echo $edit_data['Amount']; ?>" required>
                
                <button type="submit" name="update" class="action-btn">Update Booking</button>
                <button type="button" onclick="window.location.href='My_Bookings_admin.php'" class="action-btn">Cancel</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="search-bar">
            <input type="text" placeholder="Search bookings by ID, client name, or hall...">
        </div>
        
        <div class="filter-options">
            <select>
                <option value="">Filter by Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        
       
        
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Client Name</th>
                    <th>Booking Date</th>
                    <th>Start Date/Time</th>
                    <th>End Date/Time</th>
                    <th>Hall ID</th>
                    <th>Payment ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT b.Booking_id, b.Booking_date, b.Start_date, b.Start_time, 
                          b.End_date, b.End_time, b.Status, b.Hall_id, b.Client_id,
                          p.Payment_id, p.Amount, c.Fname, c.Lname
                          FROM booking b
                          LEFT JOIN payment p ON b.Booking_id = p.Booking_id
                          LEFT JOIN client c ON b.Client_id = c.Client_id 
                          ORDER BY b.Booking_date DESC";

                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $status_class = strtolower($row['Status']);
                        echo "<tr>";
                        echo "<td>" . $row['Booking_id'] . "</td>";
                        echo "<td>" . $row['Fname'] . " " . $row['Lname'] . "</td>";
                        echo "<td>" . $row['Booking_date'] . "</td>";
                        echo "<td>" . $row['Start_date'] . " " . $row['Start_time'] . "</td>";
                        echo "<td>" . $row['End_date'] . " " . $row['End_time'] . "</td>";
                        echo "<td>" . $row['Hall_id'] . "</td>";
                        echo "<td>" . ($row['Payment_id'] ?? 'N/A') . "</td>";
                        echo "<td>₹" . number_format($row['Amount'] ?? 0, 2) . "</td>";
                        echo "<td><span class='status " . $status_class . "'>" . $row['Status'] . "</span></td>";
                        echo "<td>";
                        echo "<a href='?edit=" . $row['Booking_id'] . "' class='edit-btn'>Edit</a>";
                        echo "<form method='POST' style='display:inline;'>";
                        echo "<input type='hidden' name='booking_id' value='" . $row['Booking_id'] . "'>";
                        echo "<button type='submit' name='delete' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this booking?')\">Delete</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No bookings found</td></tr>";
                }
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
    <a href="admin_homepage.php" class="home-btn">Home</a>
</body>
</html>
