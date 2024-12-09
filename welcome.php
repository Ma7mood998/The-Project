<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f4f4f4;
            color: #333;
            font-family: Arial, Helvetica, sans-serif;
            background-image: url('https://images.pexels.com/photos/911738/pexels-photo-911738.jpeg'); /* Path to the image */
            background-size: cover; /* Ensures the image covers the entire background */
            background-position: center; /* Centers the image */
            background-repeat: no-repeat; /* Prevents tiling of the image */
        }

        /* Wrapper for Flexbox */
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar Section */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: black;
            color: white;
            padding: 10px 20px;
            height: 60px;
        }

        .logo-img {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            border: 2px solid white;
            cursor: pointer;
        }

        .navbar h1 {
            flex-grow: 1;
            margin: 0;
            text-align: center;
            font-size: 24px;
            color: white;
        }

        /* Hero Section */
        .hero-section {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            opacity: 0;
            animation: fadeIn 1s ease-in forwards; /* Trigger fade-in effect */
        }

        /* Buttons */
        .buttons{
            display: flex; /* Use flexbox for proper horizontal alignment */
        justify-content: center; /* Center the buttons horizontally */
        gap: 10px;
        }

        button {
            background-color: #0172AD;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 10px;
            animation: slideIn 1s ease-out forwards;
        }

        button:hover {
            background-color: black;
        }

        /* Footer Section */
        .footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 14px;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Navbar Section -->
        <div class="navbar">
            <a href="welcome.php">
                <img src="uploads/rbook.jpg" alt="Site Logo" class="logo-img">
            </a>
            <h1>IT College Booking System</h1>
        </div>

        <!-- Main Content Section -->
        <div class="hero-section">
            <div>
                <p>Welcome to the IT College Room Booking System!</br> Easily book rooms and manage schedules with just a few clicks.</p>
                <div class="buttons" style="margin-top: 20px;">
                    <a href="login.php">
                        <button>Login</button>
                    </a>
                    <a href="register.php">
                        <button>Register</button>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            &copy; 2024 IT College Room Booking System
        </div>
    </div>
</body>

</html>