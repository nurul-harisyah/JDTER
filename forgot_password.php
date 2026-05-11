<?php
session_start();
require('db_connection.php');
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = '';
$message = ''; // Variable to store success or error messages
$message_type = ''; // Variable to store the type of message (success or error)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $email = $_POST['email']; // Preserve email value

    if ($action === 'request_code') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate 4-digit code
            $code = rand(1000, 9999);
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_email'] = $email;

            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'azielaazieatul@gmail.com';
                $mail->Password = 'flsv xxob ddjy psll';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('azielaazieatul+no-reply@gmail.com', 'ADMIN_JDTER');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Your Password Reset Code';
                $mail->Body = "Your password reset code is: <b>$code</b>";

                $mail->send();
                $message = "Reset code sent to your email!";
                $message_type = "success"; // Success message
            } catch (Exception $e) {
                $message = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                $message_type = "error"; // Error message
            }
        } else {
            $message = "Email not found!";
            $message_type = "error"; // Error message
        }
    } elseif ($action === 'continue') {
        $input_code = $_POST['code'];
        if (isset($_SESSION['reset_code']) && $_SESSION['reset_code'] == $input_code) {
            header("Location: reset_password.php");
            exit();
        } else {
            $message = "Invalid code. Please try again.";
            $message_type = "error"; // Error message
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Forgot Password</title>
</head>
<body class="forgot-page">
<div class="forgot-container">
    <form method="POST">
        <label for="email">Enter your registered email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        <br>
        <label for="code">Enter the code:</label>
        <input type="text" name="code">
        <br>

        <!-- Message display -->
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <button type="submit" name="action" value="request_code">Request Code</button>
        <button type="submit" name="action" value="continue">Continue</button>
    </form>
</div>
</body>
</html>
