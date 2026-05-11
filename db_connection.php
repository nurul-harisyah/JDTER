<?php
// Database connection settings
$host = 'sql303.infinityfree.com'; // Database host
$dbname = 'if0_38070204_journal'; // Replace with your actual database name
$username = 'if0_38070204'; // Database username
$password = 'Ye65Y0FtgUxFh'; // Database password

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If the connection fails, show an error message
    die("Connection failed: " . $e->getMessage());
}
?>
