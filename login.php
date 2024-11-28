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

                header("Location: rooms.php");
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
    <link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css"
>
</head>
<body>
    <div class="container">
        <h1>Login</h1>

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
    </div>
</body>
</html>