<?php
session_start();
require_once 'db.php';

// Initialize variables
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate the password strength
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
        $error = "Password must contain at least 8 characters, including uppercase, lowercase letters, and numbers.";
    } elseif (!preg_match('/^\d+@stu\.uob\.edu\.bh$/', $email) && !preg_match('/@uob\.edu\.bh$/', $email)) {
        $error = "Invalid email domain. Please use a UoB email address.";
    } else {
        // Profile picture upload
        $profilePicture = 'default.jpg'; // Default picture if none is uploaded
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $profilePictureFile = $_FILES['profile_picture'];
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($profilePictureFile["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($profilePictureFile["tmp_name"], $targetFile)) {
                    $profilePicture = $profilePictureFile["name"];
                } else {
                    $error = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error = "Only image files (jpg, jpeg, png, gif) are allowed.";
            }
        }

        if (empty($error)) {
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Determine user type
            $userType = preg_match('/^\d+@stu\.uob\.edu\.bh$/', $email) ? 'student' : 'staff';

            try {
                // Insert user data into the database
                $sql = "INSERT INTO users (first_name, last_name, email, password_hash, user_type, profile_picture) 
                        VALUES (:first_name, :last_name, :email, :password_hash, :user_type, :profile_picture)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':first_name', $firstName);
                $stmt->bindParam(':last_name', $lastName);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password_hash', $passwordHash);
                $stmt->bindParam(':user_type', $userType);
                $stmt->bindParam(':profile_picture', $profilePicture);
                $stmt->execute();

                header("Location: welcome.php");
                exit;
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
            color: #0172AD;
            background-image: url('uploads/wallpaper.jpg'); /* Path to the image */
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

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
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

    <div class="navbar">
        <a href="welcome.php" style="float: left;">
            <img src="uploads/rbook.jpg" alt="Site Logo" class="logo-img">
        </a>
        <h1>Registration</h1>
    </div>

    <div class="container">

        <?php if (!empty($error)): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php" enctype="multipart/form-data">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($firstName ?? ''); ?>">

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required value="<?php echo htmlspecialchars($lastName ?? ''); ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="example@stu.uob.edu.bh" value="<?php echo htmlspecialchars($email ?? ''); ?>">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="profile_picture">Profile Picture (Optional):</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

            <button type="submit">Register</button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <p>Already have an account?</p>
            <a href="login.php" style="text-decoration: none;">
                <button>
                    Login Here
                </button>
            </a>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 IT College Room Booking System</p>
    </div>

</body>
</html>
