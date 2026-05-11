<?php
session_start();
require('db_connection.php');

// Ensure the user is logged in as a reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

// Ensure `assignment_id` is provided in the URL
if (!isset($_GET['assignment_id']) || empty($_GET['assignment_id'])) {
    die("Invalid request.");
}

$submission_id = $_GET['assignment_id'];

// Fetch the manuscript details from the database
$query = "SELECT m.title, m.abstract
          FROM metadata m
          WHERE m.submission_id = :submission_id";

$stmt = $pdo->prepare($query);
$stmt->bindParam(":submission_id", $submission_id, PDO::PARAM_INT);
$stmt->execute();
$manuscript = $stmt->fetch(PDO::FETCH_ASSOC);

// If manuscript not found
if (!$manuscript) {
    die("Manuscript not found.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Abstract</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .abstract-container { margin-top: 20px; }
        .abstract { white-space: pre-wrap; word-wrap: break-word; }
        .back-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h2><?php echo htmlspecialchars($manuscript['title']); ?></h2>
    
    <div class="abstract-container">
        <h3></h3>
        <p class="abstract"><?php echo nl2br(htmlspecialchars($manuscript['abstract'])); ?></p>
    </div>

    <br><a href="download_assignment.php" class="back-btn">Back</a>

</body>
</html>
