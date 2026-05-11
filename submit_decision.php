<?php
require 'db_connection.php';
session_start();

if (!isset($_SESSION['editor'])) {
    exit('Access denied: You must be logged in as an editor.');
}

$editor_id = $_SESSION['user']['id'];
$submission_id = $_POST['submission_id'] ?? null;
$decision_type = $_POST['decision'] ?? null;
$comments = trim($_POST['comments'] ?? '');
$action = $_POST['action'] ?? null;

if (!$submission_id || !$decision_type || !$action) {
    exit('Error: Missing required data.');
}

// Save the form data in session regardless of action
if (!isset($_SESSION['saved_decisions'])) {
    $_SESSION['saved_decisions'] = [];
}

$_SESSION['saved_decisions'][$submission_id] = [
    'decision' => $decision_type,
    'comments' => $comments
];

// Prevent duplicate decision for the same submission
$checkStmt = $pdo->prepare("SELECT * FROM editor_decisions WHERE submission_id = :submission_id");
$checkStmt->execute([':submission_id' => $submission_id]);
$existingDecision = $checkStmt->fetch();

if ($existingDecision && $action === 'commit_decision') {
    header("Location: publication.php?message=Decision already made for this manuscript.");
    exit;
}

if ($action === 'draft_email') {
    // Store draft in session
    $_SESSION['draft_email'] = [
        'submission_id' => $submission_id,
        'decision_type' => $decision_type,
        'comments' => $comments,
    ];
    header("Location: draft_email.php");
    exit;
} elseif ($action === 'commit_decision') {
    // Save final decision
    $stmt = $pdo->prepare("INSERT INTO editor_decisions (submission_id, editor_id, decision_type, comments, decision_date)
                           VALUES (:submission_id, :editor_id, :decision_type, :comments, NOW())");
    $stmt->execute([
        ':submission_id' => $submission_id,
        ':editor_id' => $editor_id,
        ':decision_type' => $decision_type,
        ':comments' => $comments
    ]);

    // Clear the saved decision for this submission
    if (isset($_SESSION['saved_decisions'][$submission_id])) {
        unset($_SESSION['saved_decisions'][$submission_id]);
    }

    // Redirect to publication page with message
    $message = urlencode("Decision submitted: Manuscript has been marked as '{$decision_type}'.");
    header("Location: publication.php?message={$message}");
    exit;
}
?>