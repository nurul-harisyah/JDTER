<?php
session_start();
require('db_connection.php');

// Ensure the user is logged in as a reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

// Check if the request is POST and contains required data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment_id'], $_POST['reason'])) {
    $assignment_id = $_POST['assignment_id'];
    $reason = trim($_POST['reason']);
    $reviewer_id = $_SESSION['user']['id'];
    
    if (empty($reason)) {
        die("Decline reason is required.");
    }
    
    // Update the database to mark the assignment as declined
    $query = "UPDATE reviewer_assignments SET status = 'declined', decline_reason = :reason WHERE assignment_id = :assignment_id AND reviewer_id = :reviewer_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
    $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
    $stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo "<script>alert('You have successfully declined the assignment.'); window.location.href='request.php';</script>";
    } else {
        echo "<script>alert('Error declining the assignment. Please try again.'); window.history.back();</script>";
    }
} else {
    die("Invalid request.");
}
?>
