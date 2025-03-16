<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Hall Management</title>
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
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: relative;
            min-height: 100vh;
            padding-bottom: 100px;
        }
        h1 {
            color: #333;
            text-align: center;
            font-size: 2.2rem;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .search-section {
            background-color: white;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .search-section select, .search-section input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .search-section select:focus, .search-section input:focus {
            border-color: #2196F3;
            outline: none;
        }
        .hall-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            padding: 25px;
        }
        .hall-card {
            background-color: #fff;
            border: none;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .hall-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .hall-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .hall-card h2 {
            margin-top: 0;
            color: #1a1a1a;
            font-size: 1.6rem;
            font-weight: 600;
        }
        .hall-card p {
            margin: 12px 0;
            color: #444;
            font-size: 1.1rem;
        }
        .status-select {
            padding: 10px;
            border-radius: 6px;
            border: 2px solid #ddd;
            margin-left: 10px;
            font-size: 0.95rem;
            cursor: pointer;
            background-color: #fff;
            transition: all 0.3s ease;
        }
        .status-select:hover {
            border-color: #2196F3;
        }
        .status-select:focus {
            outline: none;
            border-color: #2196F3;
            box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
        }
        .status-indicator {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 500;
        }
        .available {
            background-color: #4CAF50;
            color: white;
        }
        .unavailable {
            background-color: #f44336;
            color: white;
        }
        .action-btn {
            padding: 12px 24px;
            margin: 8px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .preview-btn {
            background-color: #2196F3;
            color: white;
            width: 100%;
            font-size: 1.1rem;
        }
        .preview-btn:hover {
            background-color: #1976D2;
            transform: scale(1.02);
        }
        .home-btn {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background-color: #2196F3;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .home-btn:hover {
            background-color: #1976D2;
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0,0,0,0.25);
        }
        .room-status {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .update-status-btn {
            padding: 8px 16px;
            margin-left: 10px;
            border: none;
            border-radius: 4px;
            background-color: #2196F3;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .update-status-btn:hover {
            background-color: #1976D2;
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

    // Handle room status update
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['room_id']) && isset($_POST['status'])) {
            $room_id = $_POST['room_id'];
            $status = $_POST['status'];
            
            $update_sql = "UPDATE conference_room SET room_status = ? WHERE Room_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("si", $status, $room_id);
            
            if ($stmt->execute()) {
                echo "<script>
                    alert('Room status updated successfully!');
                    window.location.href = window.location.href;
                </script>";
            } else {
                echo "<script>alert('Error updating room status: " . $stmt->error . "');</script>";
            }
            
            $stmt->close();
        }
    }
    ?>

    <div class="container">
        <div class="left-content">
            <h1>Conference Hall Management Dashboard</h1>
            <div class="search-section">
                <h2>Search Conference Halls</h2>
                <select id="searchType" onchange="showSearchCriteria()">
                    <option value="">Select Search Criteria</option>
                    <option value="availability">Search by Availability</option>
                    <option value="price">Search by Price Range</option>
                    <option value="rooms">Search by Rooms</option>
                </select>

                <div id="availabilitySearch" class="search-criteria">
                    <input type="date" placeholder="Select Date">
                </div>

                <div id="priceSearch" class="search-criteria">
                    <select>
                        <option value="">Select Price Range</option>
                        <option value="0-50000">₹0 - ₹50,000</option>
                        <option value="50001-100000">₹50,001 - ₹1,00,000</option>
                        <option value="100001-200000">₹1,00,001 - ₹2,00,000</option>
                        <option value="200001+">₹2,00,001+</option>
                    </select>
                </div>

                <div id="roomsSearch" class="search-criteria">
                    <select>
                        <option value="">Select Number of Rooms</option>
                        <option value="1-2">1-2 Rooms</option>
                        <option value="3-4">3-4 Rooms</option>
                        <option value="5+">5+ Rooms</option>
                    </select>
                </div>
            </div>
        
            <div class="hall-grid">
                <?php
                // Query to get hall and room details using the hall_room_details view
                $sql = "SELECT ch.Hall_id, ch.Hall_name, ch.Street, ch.City, 
                        COUNT(hrd.Room_id) as room_count,
                        MIN(hrd.Price) as min_price,
                        MAX(hrd.Price) as max_price,
                        GROUP_CONCAT(hrd.Room_id) as room_ids,
                        GROUP_CONCAT(hrd.Capacity) as capacities,
                        GROUP_CONCAT(hrd.Price) as prices,
                        GROUP_CONCAT(cr.room_status) as room_statuses
                        FROM conference_hall ch
                        LEFT JOIN hall_room_details hrd ON ch.Hall_id = hrd.Hall_id
                        LEFT JOIN conference_room cr ON hrd.Room_id = cr.Room_id
                        GROUP BY ch.Hall_id";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $hall_images = [
                            1 => "https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1a/3d/41/dd/mansion-social-event.jpg",
                            2 => "https://www.oberoihotels.com/-/media/oberoi-hotels/website-images/the-oberoi-gurgaon/events/details/togn-business-centre-16-seater-meeting-room.jpg",
                            3 => "https://www.itchotels.com/content/dam/itchotels/in/umbrella/itc/hotels/itcgrandcentral-mumbai/images/meetings-and-socials-landing-page/headmast/desktop/banquet-meeting.png",
                            4 => "https://cache.marriott.com/content/dam/marriott-renditions/BLRJW/blrjw-ballroom-0050-hor-feat.jpg"
                        ];

                        echo "<div class='hall-card'>";
                        echo "<img src='" . $hall_images[$row['Hall_id']] . "' alt='" . $row['Hall_name'] . "'>";
                        echo "<h2>" . $row['Hall_name'] . "</h2>";
                        echo "<p><strong>Location:</strong> " . $row['Street'] . ", " . $row['City'] . "</p>";
                        echo "<p><strong>Rooms:</strong> " . $row['room_count'] . " Rooms</p>";
                        echo "<p><strong>Price Range:</strong> ₹" . number_format($row['min_price']) . " - ₹" . number_format($row['max_price']) . " per day</p>";
                        
                        // Display room status with form for updating
                        echo "<div class='room-status'>";
                        $room_ids = explode(',', $row['room_ids']);
                        $capacities = explode(',', $row['capacities']);
                        $statuses = explode(',', $row['room_statuses']);
                        
                        for($i = 0; $i < count($room_ids); $i++) {
                            $status = isset($statuses[$i]) ? $statuses[$i] : 'unavailable';
                            echo "<form method='POST' style='display:inline;'>";
                            echo "<p>Room " . $room_ids[$i] . " (Capacity: " . $capacities[$i] . "): ";
                            echo "<input type='hidden' name='room_id' value='" . $room_ids[$i] . "'>";
                            echo "<select name='status' class='status-select'>";
                            echo "<option value='available' " . ($status == 'available' ? 'selected' : '') . ">Available</option>";
                            echo "<option value='unavailable' " . ($status == 'unavailable' ? 'selected' : '') . ">Unavailable</option>";
                            echo "</select>";
                            echo "<button type='submit' class='update-status-btn'>Update Status</button></p>";
                            echo "</form>";
                        }
                        echo "</div>";
                        
                        echo "<div class='button-group'>";
                        echo "<button class='action-btn preview-btn' onclick=\"window.location.href='Hall" . $row['Hall_id'] . ".php'\">Preview</button>";
                        echo "</div>";
                        echo "</div>";
                    }
                }
                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <a href="admin_homepage.php">
        <button class="home-btn">Home</button>
    </a>

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
