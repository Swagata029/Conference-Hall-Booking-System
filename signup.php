<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Conference Hall Booking System</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-image:url("https://img.freepik.com/free-vector/abstract-background-design-dark-green_53876-59276.jpg");
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .signup-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 1.5rem;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input, select {
            margin-bottom: 1rem;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .additional-links {
            text-align: center;
            margin-top: 1rem;
        }
        .additional-links a {
            color: #3498db;
            text-decoration: none;
        }
        .additional-links a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h1>Sign Up</h1>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $conn = new mysqli("localhost", "root", '', "conferencefinal");
            
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $userType = $_POST['userType'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            if ($userType == 'admin') {
                $sql = "INSERT INTO admin (Email, Admin_pass) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $email, $password);
                
            } else if ($userType == 'client') {
                $fullName = $_POST['fullName'];
                $fname = explode(" ", $fullName)[0];
                $lname = explode(" ", $fullName)[1] ?? '';
                
                $sql = "INSERT INTO client (Fname, Lname, Email, Phone, Organization, Client_pass) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $phone = $_POST['phone'];
                $organization = $_POST['organization'];
                $stmt->bind_param("ssssss", $fname, $lname, $email, $phone, $organization, $password);
            }

            // Execute statement and check for errors
            if ($stmt->execute()) {
                header("Location: index.php");
                exit();
            } else {
                echo "<div class='error'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
            $conn->close();
        }
        ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <select name="userType" id="userType" required onchange="toggleClientFields()">
                <option value="">Select User Type</option>
                <option value="admin">Admin</option>
                <option value="client">Client</option>
            </select>
            <input type="text" name="fullName" id="fullName" placeholder="Full Name" required>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="text" name="phone" id="phone" placeholder="Phone Number" class="client-field" style="display:none" maxlength="10">
            <input type="text" name="organization" id="organization" placeholder="Organization" class="client-field" style="display:none">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <div class="additional-links">
            <a href="index.php">Already have an account? Login</a>
        </div>
    </div>

    <script>
        function toggleClientFields() {
            const userType = document.getElementById('userType').value;
            const clientFields = document.getElementsByClassName('client-field');
            
            for(let field of clientFields) {
                field.style.display = userType === 'client' ? 'block' : 'none';
                field.required = userType === 'client';
            }
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert("Passwords do not match!");
            }
        });
    </script>
</body>
</html>
