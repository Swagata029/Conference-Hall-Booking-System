<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JW Marriott Mumbai</title>
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
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .hall-header {
            height: 400px;
            width: 100%;
            margin-bottom: 30px;
            position: relative;
        }
        .hall-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }
        .hall-title {
            position: absolute;
            bottom: 20px;
            left: 20px;
            color: black;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            background-color: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 8px;
        }
        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .room-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: 600px;
        }
        .room-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .status-indicator {
            padding: 6px 15px;
            border-radius: 20px;
            margin: 10px 0;
            display: inline-block;
            width: 100px;
            text-align: center;
            font-weight: 500;
            position: absolute;
            bottom: 20px;
        }
        .available {
            background-color: rgba(76, 175, 80, 0.8);
            color: white;
        }
        .unavailable {
            background-color: rgba(244, 67, 54, 0.8);
            color: white;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin: 5px;
            width: 100%;
            text-align: center;
        }
        .book-btn {
            background-color: #4CAF50;
            color: white;
        }
        .services-btn {
            background-color: #2196F3;
            color: white;
            display: none;
            text-decoration: none;
            margin: 0 auto;
        }
        .book-btn:hover, .services-btn:hover {
            opacity: 0.9;
        }
        .room-info {
            flex-grow: 1;
            position: relative;
            padding-bottom: 60px;
        }
        .button-container {
            margin-top: auto;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php
    // Database connection
    $conn = new mysqli("localhost", "root", "", "conferencefinal");
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get hall details
    $hall_query = "SELECT * FROM conference_hall WHERE Hall_id = 4";
    $hall_result = $conn->query($hall_query);
    $hall_data = $hall_result->fetch_assoc();

    // Get room details
    $room_query = "SELECT * FROM conference_room WHERE Hall_id = 4";
    $room_result = $conn->query($room_query);
    ?>

    <div class="container">
        <div class="hall-header">
            <img src="https://cache.marriott.com/content/dam/marriott-renditions/BLRJW/blrjw-ballroom-0050-hor-feat.jpg" alt="<?php echo $hall_data['Hall_name']; ?>">
            <div class="hall-title">
                <h1><?php echo $hall_data['Hall_name']; ?></h1>
                <p><?php echo $hall_data['Street'] . ', ' . $hall_data['City']; ?></p>
            </div>
        </div>

        <div class="room-grid">
            <?php
            while($room = $room_result->fetch_assoc()) {
                // Get services for this room
                $service_query = "SELECT * FROM service_type WHERE Room_id = " . $room['Room_id'];
                $service_result = $conn->query($service_query);
                $services = array();
                while($service = $service_result->fetch_assoc()) {
                    $services[] = $service['Service_name'];
                }
                $facilities = implode(", ", $services);
            ?>
            <div class="room-card">
                <img src="https://cache.marriott.com/content/dam/marriott-renditions/BOMSA/bomsa-boardroom-0028-hor-wide.jpg" alt="Room <?php echo $room['Room_id']; ?>" class="room-image">
                <div class="room-info">
                    <h2>Room <?php echo $room['Room_id']; ?></h2>
                    <p><strong>Capacity:</strong> <?php echo $room['Capacity']; ?> people</p>
                    <p><strong>Price:</strong> ₹<?php echo number_format($room['Price']); ?> per day</p>
                    <p><strong>Facilities:</strong> <?php echo $facilities; ?></p>
                    <span class="status-indicator <?php echo $room['room_status']; ?>"><?php echo ucfirst($room['room_status']); ?></span>
                </div>
                <div class="button-container">
                    <button class="btn book-btn" onclick="showServices(this)" <?php echo ($room['room_status'] == 'unavailable') ? 'disabled' : ''; ?>>Book Now</button>
                    <a href="Services.php?room_id=<?php echo $room['Room_id']; ?>&hall_id=4" class="btn services-btn">Add Services</a>            
                </div>
            <?php
            }
            $conn->close();
            ?>
        </div>
    </div>

    <script>
        function showServices(bookBtn) {
            const servicesBtn = bookBtn.nextElementSibling;
            servicesBtn.style.display = 'block';
        }
    </script>
</body>
</html>
