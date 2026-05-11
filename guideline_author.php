<?php
require 'db_connection.php';
session_start();

if (!isset($_SESSION['user'])) {
    exit('Access denied: You must be logged in.');
}

$user = $_SESSION['user'];
$isReviewer = isset($user['role']) && $user['role'] === 'reviewer';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Author Guidelines</title>
    <link rel="stylesheet" href="guideline_author.css">
    <script>
        function toggleSubmenu(id) {
            var submenu = document.getElementById(id + '_menu');
            submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body class="dashboard-page">

<div class="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php"><h2>Dashboard</h2></a>
    </div>

    <a href="#" onclick="toggleSubmenu('new_submission')">New Submission</a>
    <ul id="new_submission_menu" style="display: none;">
        <li><a href="start.php">1. Start</a></li>
        <li><a href="upload_submission.php">2. Upload Manuscript</a></li>
        <li><a href="enter_metadata.php">3. Enter Metadata</a></li>
        <li><a href="confirmation.php">4. Confirmation</a></li>
    </ul>

    <a href="my_submission.php">My Submission</a>
    <a href="status_change.php">Notification</a>
    <a href="payment.php">Payment</a>
 
    <a href="myProfile.php">My Profile</a>
    <a href="guideline_author.php" >Guideline Author</a>
    <?php if ($isReviewer): ?>
        <a href="reviewer_dashboard.php">Reviewer Section</a>
    <?php endif; ?>

    <a href="index.php">Logout</a>
</div>

<div class="main-content">
    <h2>Author Submission Guidelines</h2>

    <div class="section">
        <h3>📌 How to Submit Your Manuscript</h3>
        <p>1️⃣ Go to the <strong>New Submission</strong> section.<br>
           2️⃣ Follow the four-step submission process:<br>
           <ul>
               <li><strong>Start</strong> — Complete submission checklist and consent.</li>
               <li><strong>Upload Manuscript</strong> — Upload your manuscript files (PDF, DOCX, etc.).</li>
               <li><strong>Enter Metadata</strong> — Fill in author details, title, abstract, and keywords.</li>
               <li><strong>Confirmation</strong> — Finalize and confirm your submission.</li>
           </ul>
        </p>
    </div>

    <div class="section">
        <h3>🔄 Manuscript Process Flow</h3>
        <ol>
            <li><strong>Submitted</strong> — Manuscript uploaded and awaiting review assignment.</li>
            <li><strong>Under Review</strong> — Reviewer 1 and Reviewer 2 assigned to evaluate your work.</li>
            <li><strong>Review Decisions</strong> — Feedback and recommendations from both reviewers.</li>
            <li><strong>Final Decision</strong> — Editor makes the final decision: Accept / Reject / Revision.</li>
            <li><strong>Published</strong> — Accepted manuscripts have been succesful published.</li>
        </ol>
    </div>

    <div class="section">
        <h3>💸 Payment Information</h3>
        <ul>
            <li><strong>Waiting for Payment</strong> — You have not uploaded any proof of payment yet.</li>
            <li><strong>Pending Payment Confirmation</strong> — Proof uploaded, awaiting editor verification.</li>
            <li><strong>Payment Successful</strong> — Payment verified; your manuscript will proceed to publishing.</li>
        </ul>
        <p>Please upload payment proof in <strong>JPG, PNG, or PDF</strong> format via the <a href="payment.php">Payment Section</a>.</p>
    </div>

</div>

</body>
</html>
