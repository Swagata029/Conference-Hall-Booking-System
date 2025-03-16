<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Hall Booking Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image:url("https://img.freepik.com/free-vector/abstract-background-design-dark-green_53876-59276.jpg");
            color: #333;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            justify-content: space-between;
        }
        nav {
            background-color: #2c3e50;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: space-around;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            transition: all 0.3s;
            border-radius: 5px;
        }
        nav ul li a:hover {
            background-color: #34495e;
        }
        .left-content {
            width: 65%;
        }
        .search-section {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            margin-top: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .search-section select, .search-section input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .search-results {
            margin-top: 20px;
            padding: 15px;
            background-color: white;
            border-radius: 5px;
        }
        .hall-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }
        .reviews {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .review-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .review-card img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .stars {
            color: #ffd700;
            margin: 10px 0;
        }
        .right-content {
            width: 30%;
        }
        .hall-images {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
        }
        .hall-image {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .hall-image:hover {
            transform: scale(1.05);
        }
        .search-criteria {
            display: none;
        }
        .search-criteria.active {
            display: block;
        }
    </style>
</head>
<body>
    <?php
    $conn = mysqli_connect("localhost", "root", "", "conferencefinal");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    ?>

    <nav>
        <ul>
            <li><a href="My_Bookings_admin.php">Bookings</a></li>
            <li><a href="conference_hall_admin.php">Conference Halls</a></li>
            <li><a href="Services_admin.php">Services</a></li>
            <li><a href="payment_details_admin.php">Payments</a></li>
            <li><a href="signup.php">Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <div class="left-content">
            <h1 style="color: beige;">Conference Hall Booking Dashboard</h1>
            <div class="search-section">
                <h2>Search Conference Halls</h2>
                <form method="POST" action="">
                    <select name="searchType" id="searchType" onchange="showSearchCriteria()">
                        <option value="">Select Search Criteria</option>
                        <option value="date">Search by Date</option>
                        <option value="price">Search by Price Range</option>
                        <option value="capacity">Search by Capacity</option>
                    </select>

                    <div id="dateSearch" class="search-criteria">
                        <input type="date" name="searchDate" placeholder="Select Date">
                    </div>

                    <div id="priceSearch" class="search-criteria">
                        <select name="priceRange">
                            <option value="">Select Price Range</option>
                            <option value="0-500">₹0 - ₹500</option>
                            <option value="501-750">₹501 - ₹750</option>
                            <option value="751-1000">₹751 - ₹1000</option>
                        </select>
                    </div>

                    <div id="capacitySearch" class="search-criteria">
                        <select name="capacity">
                            <option value="">Select Capacity</option>
                            <option value="10">Small (Up to 10 people)</option>
                            <option value="50">Medium (11-50 people)</option>
                            <option value="100">Large (51-100 people)</option>
                        </select>
                    </div>

                    <button type="submit" name="search" style="margin-top: 10px; padding: 10px; background-color: #2c3e50; color: white; border: none; border-radius: 5px; cursor: pointer;">Search</button>
                </form>

                <?php
                if(isset($_POST['search'])) {
                    $searchType = $_POST['searchType'];
                    $query = "";

                    if($searchType == 'date' && !empty($_POST['searchDate'])) {
                        $searchDate = $_POST['searchDate'];
                        $query = "SELECT DISTINCT cr.* FROM conference_room cr 
                                LEFT JOIN booking b ON cr.Hall_id = b.Hall_id 
                                AND b.Booking_date = '$searchDate'
                                WHERE b.Booking_id IS NULL";
                    }
                    else if($searchType == 'price' && !empty($_POST['priceRange'])) {
                        $priceRange = explode('-', $_POST['priceRange']);
                        $minPrice = $priceRange[0];
                        $maxPrice = $priceRange[1];
                        $query = "SELECT * FROM conference_room 
                                WHERE Price BETWEEN $minPrice AND $maxPrice";
                    }
                    else if($searchType == 'capacity' && !empty($_POST['capacity'])) {
                        $capacity = $_POST['capacity'];
                        if($capacity == "10") {
                            $query = "SELECT * FROM conference_room WHERE Capacity <= 10";
                        } else if($capacity == "50") {
                            $query = "SELECT * FROM conference_room WHERE Capacity > 10 AND Capacity <= 50";
                        } else {
                            $query = "SELECT * FROM conference_room WHERE Capacity > 50 AND Capacity <= 100";
                        }
                    }

                    if($query) {
                        $result = mysqli_query($conn, $query);
                        if(mysqli_num_rows($result) > 0) {
                            echo "<div class='search-results'>";
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<div class='hall-item'>";
                                echo "<h3>Room ID: " . $row['Room_id'] . "</h3>";
                                echo "<p>Capacity: " . $row['Capacity'] . " people</p>";
                                echo "<p>Price: ₹" . $row['Price'] . "</p>";
                                echo "<p>Hall ID: " . $row['Hall_id'] . "</p>";
                                echo "</div>";
                            }
                            echo "</div>";
                        } else {
                            echo "<p>No halls found matching your criteria.</p>";
                        }
                    }
                }
                ?>
            </div>
            <div class="reviews">
                <div class="review-card">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Rajesh Kumar">
                    <h3>Rajesh Kumar</h3>
                    <div class="stars">★★★★★</div>
                    <p>"Perfect venue for our corporate event. The staff was very professional and helpful. Will definitely book again!"</p>
                </div>
                <div class="review-card">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Priya Sharma">
                    <h3>Priya Sharma</h3>
                    <div class="stars">★★★★☆</div>
                    <p>"Great amenities and modern facilities. The booking process was smooth and hassle-free."</p>
                </div>
                <div class="review-card">
                    <img src="https://randomuser.me/api/portraits/men/22.jpg" alt="Amit Patel">
                    <h3>Amit Patel</h3>
                    <div class="stars">★★★★★</div>
                    <p>"Excellent location and beautiful halls. The acoustics were perfect for our seminar. Highly recommended!"</p>
                </div>
                <div class="review-card">
                    <img src="https://randomuser.me/api/portraits/women/28.jpg" alt="Sneha Reddy">
                    <h3>Sneha Reddy</h3>
                    <div class="stars">★★★★★</div>
                    <p>"Outstanding service and beautiful ambiance. The technical support team was exceptional. Perfect for our tech conference!"</p>
                </div>
            </div>
        </div>
        <div class="right-content">
            <h2 style="color: beige;">Gallery</h2>
            <div class="hall-images">
                <img src="https://5.imimg.com/data5/SV/DX/GLADMIN-33559172/conference-hall-500x500.jpg" alt="Conference Hall 1" class="hall-image">
                <img src="https://lh5.googleusercontent.com/proxy/wNk65s5a1fjtUFLfTApIOSjHIKc_PgF32mO1q3Hox234G8TAEq9XLueBaa3A_EzneRcZH1UZJSUJIUN8Z_xzJmniEgZ11El5296Kz8c6TyP74SfPwF2kkQl9" alt="Conference Hall 2" class="hall-image">
                <img src="https://thumbs.dreamstime.com/b/empty-conference-hall-18851712.jpg" alt="Conference Hall 3" class="hall-image">
            </div>
        </div>
    </div>

    <script>
        function showSearchCriteria() {
            document.querySelectorAll('.search-criteria').forEach(div => {
                div.classList.remove('active');
            });

            const selectedValue = document.getElementById('searchType').value;
            if (selectedValue) {
                document.getElementById(selectedValue + 'Search').classList.add('active');
            }
        }
    </script>
</body>
</html>
