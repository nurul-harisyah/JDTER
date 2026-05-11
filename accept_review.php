<?php
session_start();
require('db_connection.php'); 

// Ensure the user is logged in as a reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

// Check if the assignment_id is provided
if (!isset($_GET['assignment_id'])) {
    die("Invalid request. No assignment selected.");
}

$assignment_id = $_GET['assignment_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Set accepted status and calculate due date (3 weeks from acceptance)
    $query = "UPDATE reviewer_assignments 
              SET status = 'accepted', 
                  accepted_at = NOW(), 
                  due_date = DATE_ADD(NOW(), INTERVAL 3 WEEK) 
              WHERE assignment_id = :assignment_id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        // Redirect to reviewer schedule page
        header("Location: reviewer_schedule.php?assignment_id=" . $assignment_id);
        exit;
    } else {
        echo "<script>alert('Error accepting the review. Please try again.');</script>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Accept Review Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        form {
            max-width: 400px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px #ccc;
        }
        button {
            background-color: rgb(42, 42, 101);
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button.cancel {
            background-color: #6c757d;
        }
        button:hover {
            background-color: rgb(42, 42, 101);
        }
    </style>
</head>
<body>
    <h2>Accept Review Invitation</h2>
    <p>Are you sure you want to accept this review invitation?</p>

    <form method="post">
        <button type="submit">Confirm Accept</button>
    </form>

    <br>
    <button type="button" class="cancel" onclick="window.history.back()">Cancel</button>

</body>
</html>
