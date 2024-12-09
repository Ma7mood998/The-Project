<?php
session_start();

require_once 'db.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if (password_verify($password, $user['password_hash'])) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['first_name'] = $user['first_name'];

                header("Location: admin.php");
                exit;
            } else {
                $error_message = "Incorrect email or password.";
            }
        } else {
            $error_message = "No user found with that email.";
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
            color: #0172AD;
            background-image: url('../uploads/wallpaper.jpg'); /* Path to the image */
            background-size: cover; /* Ensures the image covers the entire background */
            background-position: center; /* Centers the image */
            background-repeat: no-repeat; /* Prevents tiling of the image */
        }

        /* Navbar Styles */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: black;
            color: white;
            padding: 10px 20px;
            height: 60px;
        }

        /* Logo Styling */
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

        /* Container Styles */
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border: 2px solid #0172AD;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #0172AD;
        }

        p {
            text-align: center;
            color: black;
            margin-bottom: 10px;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #0172AD;
        }

        input[type="email"],
        input[type="password"] {
            padding: 10px;
            border: 1px solid #0172AD;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #0172AD;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: black;
        }

        /* Footer Styles */
        .footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        .footer p {
            text-align: center;
            font-size: 14px;
            color: white;
        }

        
    </style>
</head>
<body>
    <div class="navbar">
        <a href="welcome.php" style="float: left;">
            <img src="uploads/rbook.jpg" alt="Site Logo" class="logo-img">
        </a>
        <h1>Login</h1>
    </div>
    <div class="container">

        <?php if (!empty($error_message)): ?>
            <div class="error-message" style="color: red;"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email">
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password">
            
            <button type="submit">Login</button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <p>Don't have an account?</p>
            <a href="register.php" style="text-decoration: none;">
                <button>
                    Register Here
                </button>
            </a>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 IT College Room Booking System</p>
    </div>
</body>
</html>