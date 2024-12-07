<?php

session_start();

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // profile picture upload, if provided

    //$_FILES superglobal array contains information about files uploaded 

    $profilePicture = 'default.jpg'; // Default picture if none is uploaded

    // //checks if there were no errors during the file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profilePictureFile = $_FILES['profile_picture'];// variable will  contain details such as the file name, type....
        $targetDir = "./"; //// Store in the same directory 
        $targetFile = $targetDir . basename($profilePictureFile["name"]); //contain the path where the uploaded file will be saved
        
        // Ensure the file is an image
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($profilePictureFile["tmp_name"], $targetFile)) {
                $profilePicture = $profilePictureFile["name"];
            } else {
                die("Sorry, there was an error uploading your file.");
            }
        } else {
            die("Only image files (jpg, jpeg, png, gif) are allowed.");
        }
    }

    // Validate the email domain to determine user type
    if (preg_match('/^\d+@stu\.uob\.edu\.bh$/', $email)) {
        $userType = 'student';
    } elseif (preg_match('/@uob\.edu\.bh$/', $email)) {
        $userType = 'staff';
    } else {
        die("Invalid email domain.");
    }

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

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
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Register</h1>

    <form method="POST" action="register.php" enctype="multipart/form-data">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="example@stu.uob.edu.bh">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <!-- Optional Profile Picture Upload -->
        <label for="profile_picture">Profile Picture (Optional):</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

        <button type="submit">Register</button>
    </form>
</body>
</html>