<?php
session_start();
require('db_connection.php'); 

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

// Get assignment_id or submission_id from URL
$assignment_id = $_GET['assignment_id'] ?? null;
$submission_id = $_GET['submission_id'] ?? null;

// If only assignment_id is provided, retrieve submission_id
if ($assignment_id && !$submission_id) {
    $query = "SELECT submission_id FROM reviewer_assignments WHERE assignment_id = :assignment_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $submission_id = $result['submission_id'] ?? null;
}

// Validate submission_id
if (!$submission_id) {
    echo "Invalid request.";
    exit;
}

// Fetch abstract using submission_id
$query = "SELECT m.title, m.abstract FROM metadata m
          JOIN submissions s ON m.submission_id = s.submission_id
          WHERE m.submission_id = :submission_id"; 

$stmt = $pdo->prepare($query);
$stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
$stmt->execute();
$abstract = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$abstract) {
    echo "Abstract not found.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Abstract</title>
    <style>
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
    <h2><?php echo htmlspecialchars($abstract['title']); ?></h2>
    <p><?php echo nl2br(htmlspecialchars($abstract['abstract'])); ?></p>
    <br><a href="request.php" class="back-btn">Back</a>
</body>
</html>
