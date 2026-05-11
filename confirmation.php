<?php
session_start();
require('db_connection.php');

// Check if the user has entered metadata
if (!isset($_SESSION['submission_id'])) {
    header('Location: enter_metadata.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get submission ID from session
    $submission_id = $_SESSION['submission_id'];

    // Finalize the submission process (e.g., confirm submission and notify)
    // You can insert any final actions here, such as notifying the user

    // Update the submission status to 'confirmation'
    $stmt = $pdo->prepare("UPDATE submissions SET status = 'confirmation' WHERE submission_id = ?");
    $stmt->execute([$submission_id]);

    // Redirect to the dashboard or a confirmation page
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Confirmation</title>
</head>

<body class="confirmation-page">
    
<div class="dashboard">

<div class="sidebar">
        <div class="sidebar-header">
        <a href="dashboard.php">
    <h2>Dashboard</h2>
</a>
           
        </div>
        <a href="#" onclick="toggleSubmenu('new_submission')">New Submission</a>
     <ul id="new_submission_menu" style="display: none;">
    <!-- Replace onclick with href links pointing to respective PHP pages -->
    <li><a href="start.php">1. Start</a></li>
    <li><a href="upload_submission.php">2. Upload Manuscript</a></li>
    <li><a href="enter_metadata.php">3. Enter Metadata</a></li>
    <li><a href="confirmation.php">4. Confirmation</a></li>
</ul>
<a href="my_submission.php">My Submission</a>
<a href="status_change.php">Notification</a>
<a href="payment.php">Payment</a>
<a href="myProfile.php">My Profile</a>
<a href="guideline_author.php">Guideline Author</a>
   <!-- Add the Logout Section in the Sidebar -->
   <a href="index.php">Logout</a>
</div>

<div class="content">

<div id="confirmation" class="section">
    <form method="post">
    <h1>Confirm Your Submission</h1>
    <p>Are you sure you want to complete the submission?</p>
        <button type="submit">Complete Submission</button>
        <button type="button" onclick="window.location.href='enter_metadata.php'">Cancel</button>
    </form>
        </div>


        <script>
        function toggleSubmenu(menuId) {
            const menu = document.getElementById(menuId + '_menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }

        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });

            // Show the desired section
            const sectionToShow = document.getElementById(sectionId);
            if (sectionToShow) {
                sectionToShow.classList.add('active');
            }
        }
        </script>
   
</body>
</html>
