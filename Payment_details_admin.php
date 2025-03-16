<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "conferencefinal");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission for editing payment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_payment'])) {
    $payment_id = $_POST['payment_id'];
    $payment_date = $_POST['payment_date']; 
    $payment_method = $_POST['payment_method'];
    $payment_status = $_POST['payment_status'];

    $sql = "UPDATE payment SET 
            Payment_date='$payment_date',
            Payment_method='$payment_method', 
            Payment_status='$payment_status'
            WHERE Payment_id=$payment_id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Payment updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating payment: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management</title>
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
            padding-bottom: 80px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .search-bar input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
        .Completed { background-color: #d4edda; color: #155724; }
        .Pending { background-color: #f8d7da; color: #721c24; }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .edit-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .edit-btn:hover {
            background-color: #0056b3;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Payment Management</h1>
        
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search payments by Payment ID...">
        </div>
        
        <table id="paymentTable">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Booking ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM payment";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row["Payment_id"] . "</td>";
                        echo "<td>₹" . $row["Amount"] . "</td>";
                        echo "<td>" . $row["Payment_date"] . "</td>";
                        echo "<td>" . $row["Payment_method"] . "</td>";
                        echo "<td><span class='status " . $row["Payment_status"] . "'>" . $row["Payment_status"] . "</span></td>";
                        echo "<td>" . $row["Booking_id"] . "</td>";
                        echo "<td><button class='edit-btn' onclick='editPayment(" . json_encode($row) . ")'>Edit</button></td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <button class="home-btn" onclick="window.location.href='admin_homepage.php'">Home</button>
    </div>

    <div id="editPaymentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Payment</h2>
            <form id="editPaymentForm" method="POST">
                <input type="hidden" id="payment_id" name="payment_id">
                <input type="hidden" name="edit_payment" value="1">
                
                <label for="payment_date">Payment Date:</label>
                <input type="date" id="payment_date" name="payment_date" required><br><br>
                
                <label for="payment_method">Payment Method:</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Cash">Cash</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select><br><br>
                
                <label for="payment_status">Payment Status:</label>
                <select id="payment_status" name="payment_status" required>
                    <option value="Completed">Completed</option>
                    <option value="Pending">Pending</option>
                </select><br><br>
                
                <input type="submit" value="Update Payment">
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById("editPaymentModal");
        var span = document.getElementsByClassName("close")[0];

        function editPayment(payment) {
            document.getElementById("payment_id").value = payment.Payment_id;
            document.getElementById("payment_date").value = payment.Payment_date;
            document.getElementById("payment_method").value = payment.Payment_method;
            document.getElementById("payment_status").value = payment.Payment_status;
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        document.getElementById("searchInput").onkeyup = function() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("paymentTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>
