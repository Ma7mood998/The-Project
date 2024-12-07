<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

// fetch the user's data user_id stored in the session
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
//check if the form was submitted via a POST request, retrieves the form data 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);


    $profilePicture = $user['profile_picture']; // Keep the current picture by default
    // Set the target directory to the current directory
    $targetDir = __DIR__ . "/"; // Current directory

   // Check if a new profile picture is uploaded
   if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
      $profilePictureFile = $_FILES['profile_picture'];
      $targetFile = $targetDir . basename($profilePictureFile["name"]); // Full path to the file

      // Move the uploaded file to the target directory
      if (move_uploaded_file($profilePictureFile["tmp_name"], $targetFile)) {
          $profilePicture = $profilePictureFile["name"]; // Update profile picture to the new one
      } else {
          // Handle error if the file could not be moved
          echo "Error uploading the file.";
     }
   }
    
   

    // password change if provided
    if (!empty($password) && !empty($newPassword) && !empty($confirmPassword)) {
        if (password_verify($password, $user['password_hash'])) { // function to check if  password matches the hashed password stored in the database
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

    //fter the update complete, redirects the user to the profile.php page
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
    <link rel="stylesheet" href="style.css">
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
            border: 1px solid #888;
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
    </style>
</head>
<body>
    <h1><?php echo $_SESSION['role'] == 'admin' ? 'Admin' : 'User'; ?> Profile</h1>

    <!-- Show Profile Details -->
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name']) . " " . htmlspecialchars($user['last_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    
    <!-- Show profile picture if it exists -->
    <?php if (!empty($user['profile_picture'])): ?>
        <img src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" width="150">
    <?php endif; ?>

    <button id="editProfileBtn">Edit Profile</button>

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

                <label for="delete_picture">Delete Profile Picture:</label>
                <input type="checkbox" id="delete_picture" name="delete_picture" value="yes"><br>

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <p>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="admin.php"><button>Back to Admin Dashboard</button></a>
    <?php else: ?>
        <a href="home.php"><button>Back to Home</button></a>
    <?php endif; ?>
</p>

    <script>
        
        // Get the modal and button
        var modal = document.getElementById('editProfileModal');//displayed when the user wants to edit their profile
        var btn = document.getElementById('editProfileBtn');//trigger the opening of the modal
        var span = document.getElementsByClassName('close')[0];//X button  to close the modal

       // Function to open the modal
       function openModal() {
          modal.style.display = 'block';
        }

       // Function to close the modal
       function closeModal() {
         modal.style.display = 'none';
        }

       // Function to close the modal when clicking outside of it
       function closeModalOnClickOutside(event) {
          if (event.target == modal) {
               closeModal();
           }
        }

        // When the user clicks the button, open the modal
        btn.onclick = openModal;

       // When the user clicks on (x), close the modal
       span.onclick = closeModal;

       // When the user clicks anywhere outside of the modal, close it
       window.onclick = closeModalOnClickOutside;
    </script>
</body>
</html>