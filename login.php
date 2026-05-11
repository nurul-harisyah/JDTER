<?php
session_start();
require('db_connection.php'); // Ensure database connection is established

$role = $_GET['role'] ?? ''; // Get role from URL, default to empty if not set

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect login credentials
    $email = $_POST['email'];
    $password = $_POST['password'];

     // Check if the user is an editor
     if ($role === 'editor') {
        // Check editor login
        $stmt = $pdo->prepare("SELECT * FROM editor WHERE email = ?");
        $stmt->execute([$email]);
        $editor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($editor) {
            // Use password_verify to compare the entered password with the stored hashed password
            if (password_verify($password, $editor['password'])) {
                // Password matches, log the editor in
                $_SESSION['editor'] = $editor;
                header('Location: editor_dashboard.php');  // Redirect to the editor page
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No editor found with that email.";
        }
    } else {
        // Check if the user is an author/reviewer
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify password for author/reviewer login
            if (password_verify($password, $user['password'])) {
                // Store user session data
                $_SESSION['user'] = $user;  // Store entire user data
                
                // Redirect based on the role
                if ($role === 'author/reviewer' && ($user['role'] === 'author' || $user['role'] === 'reviewer')) {
                    header('Location: dashboard.php');  // Redirect to Author/Reviewer dashboard
                    exit;
                } else {
                    $error = "Incorrect role for the selected login option.";
                }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No user found with that email.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JDTER Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-page"> <!-- Add login-page class to body -->

<div class="login-container">

    <h2>Login</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <h5><a href="index.php">Home</a> / Login</h5>
        <label for="email">Email</label>
        <input type="email" name="email" required>
        
        <label for="password">Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
    <p><a href="forgot_password.php">Forgot password?</a></p>
</div>
</body>
</html>
    