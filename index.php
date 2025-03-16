<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Conference Hall Booking System</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-image: url("https://img.freepik.com/free-vector/abstract-background-design-dark-green_53876-59276.jpg");
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
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
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn = mysqli_connect("localhost", "root", "", "conferencefinal");
        
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $userType = $_POST['userType'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($userType === 'admin') {
            $sql = "SELECT * FROM admin WHERE Email = ? AND Admin_pass = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $email, $password);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                session_start();
                $_SESSION['Admin_id'] = $row['Admin_id'];
                header("Location: admin_homepage.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else if ($userType === 'client') {
            $sql = "SELECT * FROM client WHERE Email = ? AND Client_pass = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $email, $password);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                session_start();
                $_SESSION['Client_id'] = $row['Client_id'];
                $_SESSION['Client_name'] = $row['Fname'] . ' ' . $row['Lname'];
                header("Location: client_homepage.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        }
        mysqli_close($conn);
    }
    ?>
    <div class="login-container">
        <h1>Login</h1>
        <?php if(isset($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <select id="userType" name="userType" required>
                <option value="">Select User Type</option>
                <option value="admin">Admin</option>
                <option value="client">Client</option>
            </select>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="additional-links">
            <a href="signup.php">Don't have an account? Sign up</a>
        </div>
    </div>
</body>
</html>