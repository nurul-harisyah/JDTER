<?php
session_start();
require('db_connection.php');

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $filePath = __DIR__ . '/' . $file;

    // Ensure file exists and prevent directory traversal attacks
    if (file_exists($filePath) && strpos(realpath($filePath), realpath(__DIR__ . '/uploads')) === 0) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "File not found or unauthorized access.";
        exit;
    }
} else {
    echo "No file specified.";
}
