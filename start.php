<?php
session_start();
require('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: login.php');
    exit;
}

// Get user information from the session
$user = $_SESSION['user'];  // Assuming $_SESSION['user'] contains user ID or relevant data
$user_id = is_array($user) ? $user['id'] : $user;  // If user is an array, get the ID, else use the value directly

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate POST data
    $comments = isset($_POST['comments']) ? $_POST['comments'] : '';
    $copyright_agreement = isset($_POST['copyright_agreement']) ? 1 : 0;  // Checkbox (1 if checked, 0 if not)
    $privacy_agreement = isset($_POST['privacy_agreement']) ? 1 : 0;  // Checkbox (1 if checked, 0 if not)

    // Prepare the SQL query to insert submission
    try {
        $stmt = $pdo->prepare("INSERT INTO submissions (user_id, status, comments_to_editor, copyright_agreement, privacy_agreement) 
                                VALUES (?, 'start', ?, ?, ?)");
        // Execute the query with the parameters
        $stmt->execute([$user_id, $comments, $copyright_agreement, $privacy_agreement]);

        // Get the submission ID to store in session for later use (upload_submission.php)
        $_SESSION['submission_id'] = $pdo->lastInsertId();

        // Redirect to the upload submission page
        header('Location: upload_submission.php');
        exit;
    } catch (PDOException $e) {
        // Handle errors gracefully
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Start Submission</title>
</head>
<body class="start-page">
    
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
<a href="guideline_author.php">Guideline Author</a>
<a href="myProfile.php">My Profile</a>
   <!-- Add the Logout Section in the Sidebar -->
   <a href="index.php">Logout</a>
</div>


<!-- Content / Form -->
<div class="content">
<div id="start" class="section">
<form method="post">
    <h1>Start Submission</h1>
        <h3><strong>Submission Requirements</strong></h3>
        <label><input type="checkbox" name="acknowledge" required> You must read and acknowledge that you've completed the requirements below before proceeding.</label><br>
        <label><input type="checkbox" name="previously_published" required> The submission has not been previously published, nor is it before another journal for consideration.</label><br>
        <label><input type="checkbox" name="journal_template" required> Manuscript should be prepared according to journal template. The template can be downloaded below.</label><br>
        <label><input type="checkbox" name="file_format" required> The submission file is in Microsoft Word or PDF document file format.</label><br>
        <label><input type="checkbox" name="url_references" required> Where available, URLs for the references have been provided.</label><br>
        <label><input type="checkbox" name="adherence_guidelines" required> The text adheres to the stylistic and bibliographic requirements outlined in the Author Guidelines.</label><br>

        <h3><strong>Acknowledge the copyright statement</strong></h3>
        <label><input type="checkbox" name="copyright_agreement" required> Yes, I agree to abide by the terms of the copyright statement.</label><br>
        <label><input type="checkbox" name="privacy_agreement" required> Yes, I agree to have my data collected and stored according to the privacy statement.</label><br>

        <textarea name="comments" placeholder="Add comments for the editor (optional)"></textarea><br>

        <button type="submit">Continue</button>
        <button type="button" onclick="window.location.href='dashboard.php'">Cancel</button>
    </form>

    </div>
        </div>
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