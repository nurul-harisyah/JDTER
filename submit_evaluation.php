<?php
session_start();
require('db_connection.php');

// Ensure user is logged in as reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

// Handle form submission
if (!isset($_POST['assignment_id'], $_POST['submission_id'], $_POST['willing_to_review'], $_POST['recommendation'], $_POST['confidential_comments'], $_POST['author_comments'], $_POST['action'])) {
    exit("Error: Missing required fields.");
}

$assignment_id = intval($_POST['assignment_id']);
$submission_id = intval($_POST['submission_id']);
$willing_to_review = $_POST['willing_to_review'];
$recommendation = $_POST['recommendation'];
$confidential_comments = trim($_POST['confidential_comments']);
$author_comments = trim($_POST['author_comments']);
$action = $_POST['action'];
$reviewer_id = $_SESSION['user']['id'];

// Determine status based on action
$status = ($action === 'submit_review') ? 'submitted' : 'draft';

// Insert or update review data in the evaluations table
$query = "INSERT INTO evaluations 
            (assignment_id, submission_id, reviewer_id, willing_to_review, recommendation, confidential_comments, author_comments, status, created_at, updated_at) 
          VALUES 
            (:assignment_id, :submission_id, :reviewer_id, :willing_to_review, :recommendation, :confidential_comments, :author_comments, :status, NOW(), NOW())
          ON DUPLICATE KEY UPDATE
            willing_to_review = VALUES(willing_to_review),
            recommendation = VALUES(recommendation),
            confidential_comments = VALUES(confidential_comments),
            author_comments = VALUES(author_comments),
            status = VALUES(status),
            updated_at = NOW()";

$stmt = $pdo->prepare($query);
$stmt->execute([
    ':assignment_id' => $assignment_id,
    ':submission_id' => $submission_id,
    ':reviewer_id' => $reviewer_id,
    ':willing_to_review' => $willing_to_review,
    ':recommendation' => $recommendation,
    ':confidential_comments' => $confidential_comments,
    ':author_comments' => $author_comments,
    ':status' => $status
]);

// If the review is submitted, redirect back to evaluation list
if ($action === 'submit_review') {
    // Redirect back to evaluation list
    header('Location: evaluation_list.php?message=Review submitted successfully.');
} else {
    // Redirect if the draft is saved
    header('Location: evaluation.php?assignment_id=' . $assignment_id . '&message=Draft saved.');
}

exit;
