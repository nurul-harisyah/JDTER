<?php
// Database connection settings
$host = 'localhost'; // Database host
$dbname = 'journal'; // Database name
$username = 'root'; // Database username
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

// Define the emails and plain passwords
$editors = [
    'editor1@gmail.com' => 'jdter2024',
    'editor2@gmail.com' => 'jdter2024',
    'editor3@gmail.com' => 'jdter2024',
    'editor4@gmail.com' => 'jdter2024',
    'editor5@gmail.com' => 'jdter2024'
];

// Insert each editor with a hashed password into the database
foreach ($editors as $email => $password) {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert the editor into the database
    $stmt = $pdo->prepare("INSERT INTO editor(email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hashedPassword]);
}

echo "Editors inserted successfully!";
?>

