<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}



$role = $_SESSION['role'];
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'User';

if (!isset($_SESSION['welcome_shown'])) {
    $_SESSION['welcome_shown'] = true; // Set the flag that the modal has been shown
    $showWelcomeModal = true;
} else {
    $showWelcomeModal = false;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link for Pico CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="common.css">
    <link rel="stylesheet" href="rooms_styles.css">
    <!-- Link for dark and lightmode JavaScript -->
    <script src="theme.js"></script>
    <!-- Link for room info/cards JavaScript -->
    <script src="rooms.js"></script>
    <script src="modal.js"></script>
    <style>
        nav {
            background-color: #333;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between; /* Distribute links and dropdown */
            align-items: center;
        }
        /* Logo Styling */
        .logo-img {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            border: 2px solid white;
            cursor: pointer;
        }
        .nav-links {
            display: flex;
            gap: 20px; /* Space between links */
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 18px;
        }
        .nav-links a:hover {
            background-color: #575757;
            border-radius: 4px;
        }

        .dropdown {
            position: relative;
        }
        .dropdown button {
            background-color: transparent;
            color: white;
            border: none;
            font-size: 18px;
            padding: 10px 15px;
            cursor: pointer;
        }
        .dropdown button:hover {
            background-color: #575757;
            border-radius: 4px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #333;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .dropdown-content a,
        .dropdown-content form button {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            background-color: transparent;
            border: none;
            text-align: left;
            cursor: pointer;
        }
        .dropdown-content a:hover,
        .dropdown-content form button:hover {
            background-color: #575757;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 30px;
        }

        .footer p {
            text-align: center;
            color: white;
            font-size: 14px;
        }
    </style>


</head>
<body>
    <!-- Nav Bar -->
    <nav >
        <div class="nav-links">
        <a href="welcome.php" style="float: left;">
            <img src="uploads/rbook.jpg" alt="Site Logo" class="logo-img">
        </a>
            <label for="theme-toggle">
                <input type="checkbox" id="theme-toggle">
                Dark Mode
            </label>
            <a href="admin.php">Home</a>
            <a href="my_bookings.php">My Bookings</a>
            <?php if ($role === 'admin'): ?>
            <a href="add_room.php">Add Room</a>
            <a href="room.php">Room Management</a>
            <a href="room_reports.php">Room Reports</a>
            <a href="mothly_report.php">Monthly Room Reports</a>
            <?php endif; ?>
        </div>
        <div class="dropdown">
            <button><?php echo htmlspecialchars($firstName); ?> ▼</button>
            <div class="dropdown-content">
                <a href="profile.php">View Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

        <main class="container">
        <article>
            <h1>Browse Rooms</h1>
            <!-- Dropdown Menu for filtering -->
             
            <form id="filter-form">
                <label for="filter-availability">Availability:</label>
                <select id="filter-availability" name="availability">
                    <option value="all">All</option>
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>

                <label for="filter-capacity">Capacity:</label>
                <select id="filter-capacity" name="capacity">
                    <option value="all">All</option>
                    <option value="gt30">Capacity > 30</option>
                </select>

                <label for="filter-floor">Floor:</label>
                <select id="filter-floor" name="floor">
                    <option value="all">All</option>
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>

                <label for="filter-department">Department:</label>
                <select id="filter-department" name="department">
                    <option value="all">All</option>
                    <option value="information systems">Information Systems</option>
                    <option value="computer science">Computer Science</option>
                    <option value="computer engineering">Computer Engineering</option>
                </select>

                <!-- Searching input -->
                <label for="search-room">Search Room:</label>
                <input type="text" id="search-room" name="room_name" placeholder="Enter room name" />
                <!-- Button to apply selected filers -->
                <button type="submit">Apply Filters</button>
                <!-- Button to reset filters -->
                <button type="button" id="reset-filters" class="secondary">Reset Filters</button>
            </form>
            </article>
            
            <article class="hide">
            <!-- Rooms Container to add room info/cards from rooms.js "JavaScript" -->
            <section id="rooms-container" class="flex" >
                    <!-- Rooms info/cards will inserted here dynamically -->
            </section>
            </article>
        </main>

        <!-- Modal Popup -->
        <div id="popupOverlay" class="hidden">
            <div id="popupContent" class="model-content">
                <span id="closePopup">&times;</span>
                <div id="popupBody">
                    <!-- Content from modal.js will be loaded dynamically here -->
                </div>
            </div>
        </div>

    </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 IT College Room Booking System</p>
    </div>
</body>
</html>
