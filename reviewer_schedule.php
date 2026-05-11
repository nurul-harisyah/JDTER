<?php
session_start();
require('db_connection.php'); 

// Ensure the user is logged in as a reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

// Check if assignment_id is provided
if (!isset($_GET['assignment_id'])) {
    die("Invalid request. No assignment selected.");
}

$assignment_id = $_GET['assignment_id'];

// Fetch due date from the database
$query = "SELECT due_date FROM reviewer_assignments WHERE assignment_id = :assignment_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
$stmt->execute();
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    die("Invalid assignment.");
}

$due_date = $assignment['due_date'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Review Schedule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        .schedule {
            max-width: 400px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px #ccc;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Your Review Schedule</h2>
    <div class="schedule">
        <p><strong>Assignment Due Date:</strong></p>
        <p><?php echo date("F j, Y", strtotime($due_date)); ?></p>
    </div>

    <br>
    <button onclick="window.location.href='reviewer_dashboard.php'">Go to Dashboard</button>
</body>
</html>
