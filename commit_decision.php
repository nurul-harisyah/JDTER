<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['editor'])) {
    exit('Access denied: You must be logged in as an editor.');
}

$editor_id = $_SESSION['user']['id'];
$submission_id = $_POST['submission_id'] ?? null;
$decision_type = $_POST['decision_type'] ?? null;
$comments = $_POST['comments'] ?? '';

if (!$submission_id || !$decision_type) {
    exit('Missing decision data.');
}

// Save decision to database - UPDATED QUERY
$stmt = $pdo->prepare("INSERT INTO editor_decisions 
    (submission_id, editor_id, decision, comments, decision_date)  
    VALUES (:submission_id, :editor_id, :decision, :comments, NOW())");

$stmt->execute([
    ':submission_id' => $submission_id,
    ':editor_id' => $editor_id,
    ':decision' => $decision_type,  
    ':comments' => $comments
]);

// Store in session for email drafting
$_SESSION['draft_email'] = [
    'submission_id' => $submission_id,
    'decision_type' => $decision_type,
    'comments' => $comments
];

header("Location: draft_email.php");
exit;