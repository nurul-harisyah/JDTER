<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewer Application Guidelines</title>
    <link rel="stylesheet" href="css/term_condition.css">
</head>
<body>
<div class="sidebar">
    <div class="review-dash"><button onclick="window.location.href='reviewer_dashboard.php'"><h1>Reviewer Dashboard</h1></button>
    <ul>
        <li><button onclick="window.location.href='reviewerapplication.php'">Apply Reviewer</button></li>
        <li><button onclick="window.location.href='request.php'">Request</button></li>
        <li><button onclick="window.location.href='download_assignment.php'">Download</button></li>
        <li><button onclick="window.location.href='evaluation_list.php'">Evaluate Manuscripts</button></li>
        <li><button onclick="window.location.href='notification.php'">Notification</button></li>
        <li><button onclick="window.location.href='profile.php'">My Profile</button></li>
        <li><button onclick="window.location.href='index.php'">Logout</button></li>
    </ul>
    </div>
</div>
<div class="main-content">
    <div class="guideline-container">
        <h1>Reviewer Application Guidelines</h1>
        
        <div class="guideline-section">
            <h2>Terms & Conditions</h2>
            
            <div class="term-card">
                <h3 class="term-title">1. Academic Credentials</h3>
                <ul class="term-list">
                    <li>A PhD or equivalent in a relevant field is often preferred, though not always mandatory</li>
                    <li>Active involvement in research and a solid publication record in peer-reviewed journals are commonly expected</li>
                </ul>
            </div>
            <h3 style="text-align: center;">OR</h3>
            <div class="term-card">
                <h3 class="term-title">2. Expertise</h3>
                <p>Demonstrated expertise in the subject area of the journal is crucial. This can be evidenced by:</p>
                <ul class="term-list">
                    <li>Recent publications in the field</li>
                    <li>Researcher profiles on platforms like ORCID, Scopus, or Google Scholar</li>
                </ul>
            </div>
            
            <div class="action-buttons">
                <button onclick="window.location.href='reviewerapplication.php'" class="apply-button">Proceed to Application</button>
                <button onclick="window.history.back()" class="back-button">Back</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>