<?php
require 'db_connection.php'; // Database connection

if (!isset($_GET['file_id']) || !is_numeric($_GET['file_id'])) {
    die('Invalid file ID.');
}

$file_id = (int) $_GET['file_id'];

// Fetch the file information from the database
$stmt = $pdo->prepare("SELECT file_name, file_path FROM submission_files WHERE file_id = ?");
$stmt->execute([$file_id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    die('File not found.');
}

$file_path = $file['file_path'];
$file_name = $file['file_name'];

// Ensure the file exists on the server
if (!file_exists($file_path)) {
    die('File does not exist.');
}

// Set headers to initiate the file download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

// Output the file
readfile($file_path);
exit;
