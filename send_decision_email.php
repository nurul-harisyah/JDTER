<?php
session_start();
require 'db_connection.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['editor'])) {
    exit('Access denied: You must be logged in as an editor.');
}

$submission_id = $_POST['submission_id'] ?? null;
$email_subject = $_POST['email_subject'] ?? '';
$email_body = $_POST['email_body'] ?? '';

if (!$submission_id || !$email_subject || !$email_body) {
    exit('Missing data.');
}

// Get author's email and submission details - UPDATED to match your schema
$query = "
    SELECT u.email, s.submission_date, sf.file_id
    FROM submissions s
    JOIN users u ON s.user_id = u.id
    LEFT JOIN submission_files sf ON s.submission_id = sf.submission_id
    WHERE s.submission_id = :submission_id
    LIMIT 1
";
$stmt = $pdo->prepare($query);
$stmt->execute([':submission_id' => $submission_id]);
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$submission || empty($submission['email'])) {
    exit('Error: Author email not found.');
}

$author_email = $submission['email'];
$file_id = $submission['file_id'] ?? "N/A";
$submission_date = $submission['submission_date'] ?? date('Y-m-d H:i:s');

// Send email using PHPMailer
$mail = new PHPMailer(true);
try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'azielaazieatul@gmail.com';
    $mail->Password = 'yhob ulan jvva rmit';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Recipients
    $mail->setFrom('azielaazieatul@gmail.com', 'ADMIN_JDTER');
    $mail->addAddress($author_email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = $email_subject;
    
    $mail->Body = "
        <p>$email_body</p>
        <p><strong>Manuscript ID:</strong> $submission_id</p>
        <p><strong>File ID:</strong> $file_id</p>
        <p><strong>Submission Date:</strong> $submission_date</p>
    ";

    $mail->send();
    $message = "Decision email sent to author for Manuscript ID $submission_id";
} catch (Exception $e) {
    $message = "Email failed to send. Mailer Error: " . $mail->ErrorInfo;
}

header("Location: publication.php?message=" . urlencode($message));
exit;