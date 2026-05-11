<?php
session_start();
require('db_connection.php');

// Ensure user is logged in and submission_id exists
if (!isset($_SESSION['user']) || !isset($_SESSION['submission_id'])) {
    header('Location: login.php');
    exit;
}

// Retrieve submission ID and user ID
$submission_id = $_SESSION['submission_id'];
// Fetch the current user's data
$user = $_SESSION['user'];
$user_id = $user['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submission_id = $_SESSION['submission_id'];
    $article_type = $_POST['article_type'];

    // Check if a file is uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        
        // Define upload directory
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/manuscript_submission/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        // File properties
        $fileName = basename($_FILES['file']['name']);
        $filePath = $uploadDir . $fileName;
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileSize = $_FILES['file']['size'];

        // Validate file extension
        $validExtensions = ['pdf', 'docx', 'txt'];
        if (!in_array($fileExtension, $validExtensions)) {
            echo "Invalid file type. Only PDF, DOCX, and TXT files are allowed.";
            exit;
        }

        // Validate file size (max 10MB)
        $maxFileSize = 10 * 1024 * 1024; // 10MB
        if ($fileSize > $maxFileSize) {
            echo "File is too large. Maximum allowed size is 10MB.";
            exit;
        }

        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
            
            try {
                // Insert record into submission_files table
                $stmt = $pdo->prepare("INSERT INTO submission_files 
                    (submission_id, file_type, file_path, file_name, file_extension, file_size, user_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $submission_id,
                    $article_type,
                    $filePath,
                    $fileName,
                    $fileExtension,
                    $fileSize,
                    $user_id
                ]);


 // ✅ Insert initial 'submitted' status into submission_status table
 $stmt = $pdo->prepare("INSERT INTO submission_status (submission_id, status) VALUES (?, 'submitted')");
 $stmt->execute([$submission_id]);



                // Update submission status to 'upload'
                $stmt = $pdo->prepare("UPDATE submissions SET status = 'upload' WHERE submission_id = ?");
                $stmt->execute([$submission_id]);

                // Redirect to metadata entry page
                header('Location: enter_metadata.php');
                exit;
            } catch (PDOException $e) {
                echo "Database error: " . $e->getMessage();
            }

        } else {
            echo "Error: Failed to move the uploaded file.";
        }
    } else {
        echo "Error in file upload: " . $_FILES['file']['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Upload Submission</title>
</head>

<body class="upload-page">

<div class="dashboard">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php">
                <h2>Dashboard</h2>
            </a>
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
        <a href="guideline_author.php">Guideline Author</a>
        <a href="index.php">Logout</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div id="upload_submission" class="section">
            <form method="post" enctype="multipart/form-data">
                <h1>Upload Submission</h1>

                <label for="article_type">Article Type:</label>
                <select name="article_type" id="article_type" required>
                    <option value="article">Article</option>
                    <option value="research_instrument">Research Instrument</option>
                    <option value="research_materials">Research Materials</option>
                    <option value="research_results">Research Results</option>
                    <option value="transcript">Transcript</option>
                    <option value="data_analysis">Data Analysis</option>
                    <option value="data_set">Data Set</option>
                    <option value="source_texts">Source Texts</option>
                    <option value="other">Other</option>
                </select><br>

                <label for="file">Upload Manuscript (Word, PDF):</label>
                <input type="file" name="file" id="file" accept=".doc,.docx,.pdf,.txt" required><br>

                <button type="submit">Continue</button>
                <button type="button" onclick="window.location.href='start.php'">Cancel</button>
            </form>
        </div>
    </div>

</div>

<!-- JavaScript for Sidebar Toggle -->
<script>
    function toggleSubmenu(menuId) {
        const menu = document.getElementById(menuId + '_menu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }
</script>

</body>
</html>