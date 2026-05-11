<?php
session_start();

// Check if the user is logged in as a reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

require('db_connection.php'); // Ensure database connection is established

// Variables for messages
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expertise = $_POST['expertise'] ?? '';
    $organization = $_POST['organization'] ?? '';

    // Validate organization input
    if (empty($organization)) {
        $error = "Please provide your organization.";
    }

    // Handle file upload
    if (empty($error) && isset($_FILES['certified_file']) && $_FILES['certified_file']['error'] === UPLOAD_ERR_OK) {
        // Retrieve file information
        $fileTmpPath = $_FILES['certified_file']['tmp_name'];
        $fileName = $_FILES['certified_file']['name'];
        $fileSize = $_FILES['certified_file']['size'];
        $fileType = $_FILES['certified_file']['type'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        // Allowed file extensions
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];

        // Validate file type
        if (in_array($fileExtension, $allowedExtensions)) {
            // Set upload path
            $uploadFileDir = 'uploads/reviewer_certifications/';
            $destPath = $uploadFileDir . uniqid() . '-' . $fileName;

            // Move the uploaded file to the designated directory
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Save file info to the database
                $stmt = $pdo->prepare("UPDATE users SET certification_file = ?, expertise = ?, organization = ? WHERE id = ?");
                $stmt->execute([$destPath, $expertise, $organization, $_SESSION['user']['id']]);

                $message = "Certification uploaded successfully. Your account is pending verification.";
            } else {
                $error = "There was an error moving the uploaded file.";
            }
        } else {
            $error = "Invalid file type. Only PDF, JPG, PNG, and DOCX files are allowed.";
        }
    } else if (empty($error)) {
        $error = "Please upload a file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewer Verification - JDTER Management System</title>
    <link rel="stylesheet" href="css/reviewerapplication.css">
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
        <div class="reviewer-container">
            
            <h2>Apply Reviewer</h2>

            <?php if ($message): ?>
                <p class="success"><?php echo $message; ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <label for="expertise">Your Expertise Area:</label>
                <select name="expertise" id="expertise" required>
                    <option value="">Select Expertise</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Data Science">Data Science</option>
                    <option value="Networking">Networking</option>
                    <option value="Cybersecurity">Cybersecurity</option>
                    <option value="Artificial Intelligence">Artificial Intelligence</option>
                    <option value="Software Engineering">Software Engineering</option>
                    <option value="Other">Other</option>
                </select>

                <label for="organization">Your Organization:</label>
                <input type="text" name="organization" id="organization" placeholder="Enter your organization" required>

                <label for="certified_file">Upload Certified File (PDF, JPG, PNG, DOCX):</label>
                <input type="file" name="certified_file" required>

                <button type="submit">Upload Certification</button>
            </form>
        </div>
    </div>
</body>
</html>