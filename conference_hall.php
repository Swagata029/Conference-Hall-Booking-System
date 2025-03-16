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
        .search-criteria {
            display: none;
        }
        .search-criteria.active {
            display: block;
        }
        .hall-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .hall-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            position: relative;
            min-height: 600px;
            display: flex;
            flex-direction: column;
        }
        .hall-card:hover {
            transform: translateY(-5px);
        }
        .hall-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .hall-card h2 {
            margin-top: 0;
            color: #333;
            font-size: 1.4rem;
        }
        .hall-card p {
            margin: 8px 0;
            color: #555;
        }
        .action-btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .preview-btn {
            background-color: #2196F3;
            color: white;
            width: 100%;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .preview-btn:hover {
            background-color: #1976D2;
            transform: scale(1.02);
        }
        .button-group {
            margin-top: auto;
            padding: 15px 0;
            width: 100%;
        }
        .room-status {
            margin: 15px 0;
        }
        .status-indicator {
            padding: 6px 15px;
            border-radius: 20px;
            margin: 2px 0;
            display: inline-block;
            width: 100px;
            text-align: center;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .available {
            background-color: rgba(76, 175, 80, 0.8);
            color: white;
        }
        .unavailable {
            background-color: rgba(244, 67, 54, 0.8);
            color: white;
        }
        .home-btn {
            position: fixed;
            bottom: 30px;
            right: 50px;
            background-color: #2196F3;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .home-btn:hover {
            background-color: #1976D2;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-content">
            <h1 style="color: black;">Conference Hall Booking Dashboard</h1>
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
                // Database connection
                $conn = new mysqli("localhost", "root", "", "conferencefinal");
                
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

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
                        
                        // Display room status
                        echo "<div class='room-status'>";
                        $room_ids = explode(',', $row['room_ids']);
                        $capacities = explode(',', $row['capacities']);
                        $statuses = explode(',', $row['room_statuses']);
                        
                        for($i = 0; $i < count($room_ids); $i++) {
                            $status = isset($statuses[$i]) ? $statuses[$i] : 'unavailable';
                            echo "<p>Room ID " . $room_ids[$i] . " (Capacity: " . $capacities[$i] . "): ";
                            echo "<span class='status-indicator " . $status . "'>" 
                                . ucfirst($status) . "</span></p>";
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

    <a href="client_homepage.php">
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
