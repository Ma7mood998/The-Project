<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    $profilePicture = $user['profile_picture']; // Keep the current picture by default
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profilePictureFile = $_FILES['profile_picture'];
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($profilePictureFile["name"]);
        move_uploaded_file($profilePictureFile["tmp_name"], $targetFile);
        $profilePicture = $profilePictureFile["name"]; // Update profile picture to the new one
    }

    // password change if provided
    if (!empty($password) && !empty($newPassword) && !empty($confirmPassword)) {
        if (password_verify($password, $user['password_hash'])) {
            if ($newPassword === $confirmPassword) {
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password_hash = :password_hash WHERE id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':password_hash', $passwordHash);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
            } else {
                die("Passwords do not match.");
            }
        } else {
            die("Current password is incorrect.");
        }
    }

    // Delete profile picture if the option is selected
    if (isset($_POST['delete_picture']) && $_POST['delete_picture'] == 'yes') {
        $profilePicture = 'default.jpg'; // Reset to default picture
    }

    // Update the user data in the database
    $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, profile_picture = :profile_picture WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':first_name', $firstName);
    $stmt->bindParam(':last_name', $lastName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':profile_picture', $profilePicture);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    header('Location: profile.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['role'] == 'admin' ? 'Admin' : 'User'; ?> Profile</title>
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 2px solid #0172AD;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0; 
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

        .profile-picture {
            width: 150px; /* Ensures the size is consistent */
            padding-left: 15px; /* Adds space between the picture and the left edge */
            padding-top: 15px;
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

        p {
            text-align: center;
            color: black;
            margin-bottom: 10px;
            padding-left: 15px;
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

        .button-container {
            display: flex;
            justify-content: center; /* Horizontally center the button */
            margin: 20px 0; /* Optional space above/below the button */
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

        /* Style the container */
        .custom-checkbox-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 16px;
            user-select: none;
        }

        /* Hide default checkbox */
        .custom-checkbox-container input[type="checkbox"] {
            display: none;
        }

        /* Create a custom checkbox look */
        .custom-checkbox-container .checkmark {
            height: 20px;
            width: 20px;
            background-color: white;
            border: 2px solid #0172AD;
            display: inline-block;
            position: relative;
            margin-right: 8px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        /* Checkbox checked effect */
        .custom-checkbox-container input[type="checkbox"]:checked + .checkmark {
            background-color: #0172AD;
            border-color: #0172AD;
        }

        /* Add a checkmark symbol when checked */
        .custom-checkbox-container input[type="checkbox"]:checked + .checkmark::after {
            content: "";
            position: absolute;
            top: 3px;
            left: 6px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }


        /* Footer Styles */
        .footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            font-size: 14px;
        }


        .footer p {
            text-align: center;
            color: white;
            font-size: 14px;
        }
    </style>
    </style>
</head>
<body>
    <div class="navbar">
        <a href="admin.php" style="float: left;">
            <img src="uploads/rbook.jpg" alt="Site Logo" class="logo-img">
        </a>
        <h1><?php echo $_SESSION['role'] == 'admin' ? 'Admin' : 'User'; ?> Profile</h1>
    </div>

    <div class="container">

    <!-- Show profile picture if it exists -->
    <?php if (!empty($user['profile_picture'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture" width="150">
    <?php endif; ?>

    <!-- Show Profile Details -->
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name']) . " " . htmlspecialchars($user['last_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    
    
    <div class="button-container">
    <button id="editProfileBtn">Edit Profile</button>
    </div>

    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Profile</h2>
            
            <form method="POST" enctype="multipart/form-data">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required><br>

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture"><br>

                <label for="password">Current Password:</label>
                <input type="password" id="password" name="password"><br>

                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password"><br>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password"><br>

                <label class="custom-checkbox-container">
                <input type="checkbox" id="delete_picture" name="delete_picture" value="yes">
                <span class="checkmark"></span> Delete Profile Picture
                </label>

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <p><a href="admin.php"><button>Back to Home</button></a></p>

    <script>
        // Get the modal and button
        var modal = document.getElementById('editProfileModal');
        var btn = document.getElementById('editProfileBtn');
        var span = document.getElementsByClassName('close')[0];

        // When the user clicks the button, open the modal
        btn.onclick = function() {
            modal.style.display = 'block';
        }

        // When the user clicks on (x), close the modal
        span.onclick = function() {
            modal.style.display = 'none';
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
    </div>
    <div class="footer">
        <p>&copy; 2024 IT College Room Booking System</p>
    </div>
</body>
</html>