<?php
session_start();
require('db_connection.php'); // Ensure database connection is established

// Check if the user is logged in and has the 'reviewer' role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    echo "Error: You must log in as a reviewer to access this page.";
    exit;
}

// Fetch reviewer data using email from the session
$email = $_SESSION['user']['email']; // Assuming 'email' is stored in the session
$stmt = $pdo->prepare("SELECT full_name, email, affiliation, country, expertise, is_verified, organization, profile_image FROM users WHERE email = ?");
$stmt->execute([$email]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if profile data exists
if (!$profile) {
    echo "Error: Reviewer profile not found.";
    exit;
}

// Handle profile image upload
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $fileTmpPath = $_FILES['profile_image']['tmp_name'];
    $fileName = $_FILES['profile_image']['name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if (in_array($fileExtension, $allowedExtensions)) {
        $uploadDir = 'uploads/profile_images/';
        $newFileName = uniqid() . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE email = ?");
            $stmt->execute([$newFileName, $email]);
            $message = "Profile image updated successfully.";
            // Reload profile data
            $stmt = $pdo->prepare("SELECT full_name, email, affiliation, country, expertise, is_verified, organization, profile_image FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $message = "Error uploading the file. Please try again.";
        }
    } else {
        $message = "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="css/profile.css">
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
        <h1>My Profile</h1>

        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <img src="uploads/profile_images/<?php echo htmlspecialchars($profile['profile_image'] ?? 'default.png'); ?>" alt="Profile Image" class="profile-image">

        <table>
            <tr><th>Name</th><td><?= htmlspecialchars($profile['full_name'] ?? 'N/A') ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($profile['email'] ?? 'N/A') ?></td></tr>
            <tr><th>Affiliation</th><td><?= htmlspecialchars($profile['affiliation'] ?? 'N/A') ?></td></tr>
            <tr><th>Country</th><td><?= htmlspecialchars($profile['country'] ?? 'N/A') ?></td></tr>
            <tr><th>Expertise</th><td><?= htmlspecialchars($profile['expertise'] ?? 'N/A') ?></td></tr>
            <tr><th>Reviewer Status</th><td><?= $profile['is_verified'] ? 'Verified' : 'Unverified' ?></td></tr>
            <tr><th>Organization</th><td><?= htmlspecialchars($profile['organization'] ?? 'N/A') ?></td></tr>
        </table>

        <form method="POST" action="" enctype="multipart/form-data" class="upload-form">
        <div class="form-group">
        <label for="profile_image">Upload Profile Image:</label>
        <input type="file" name="profile_image" id="profile_image" accept="image/*" required>
        </div>
        <div class="form-group">
        <button type="submit" class="btn-upload">Upload</button>
        </div>
        </form>

    </div>
</body>
</html>
