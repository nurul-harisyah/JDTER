<?php
session_start();
require('db_connection.php');

$message = ''; // Initialize message variable
$message_type = ''; // Initialize message type (success or error)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $email = $_SESSION['reset_email'];
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("UPDATE users SET password = ?, password_reset_token = NULL WHERE email = ?");
        $stmt->execute([$hashed_password, $email]);

        unset($_SESSION['reset_code']);
        unset($_SESSION['reset_email']);

        $message = "Password updated successfully! Redirecting to login..."; // Success message
        $message_type = 'success'; // Set message type to success
        header("Refresh: 3; url=login.php?role=author/reviewer");
        exit();
    } else {
        $message = "Passwords do not match. Please try again."; // Error message
        $message_type = 'error'; // Set message type to error
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Reset Password</title>
</head>
<body class="reset-page">
<div class="reset-container">
    <form method="POST">
        <label for="new_password">Enter new password:</label>
        <input type="password" name="new_password" required>
        <br>
        <label for="confirm_password">Confirm new password:</label>
        <input type="password" name="confirm_password" required>
        <br>

        <!-- Display success or error message -->
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <button type="submit">Continue</button>
    </form>
</div>
</body>
</html>
