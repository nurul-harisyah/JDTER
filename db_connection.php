<?php
// Database connection settings
$host = ''; // Database host
$dbname = ''; // Replace with your actual database name
$username = ''; // Database username
$password = ''; // Database password

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
