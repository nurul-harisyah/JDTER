<?php
require 'db_connection.php';
session_start();

if (!isset($_SESSION['user'])) {
    exit('Access denied: You must be logged in.');
}

$user = $_SESSION['user'];
$user_id = $user['id'];
$isReviewer = isset($user['role']) && $user['role'] === 'reviewer';

$message = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id']) && isset($_FILES['proof'])) {
    $submission_id = $_POST['submission_id'];
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['proof']['name']);
    $targetFile = $uploadDir . time() . '_' . $fileName;

    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];

    if (in_array($fileType, $allowedTypes)) {
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES['proof']['tmp_name'], $targetFile)) {
            $stmt = $pdo->prepare("INSERT INTO payment_proofs (submission_id, file_path) VALUES (:submission_id, :file_path)");
            $stmt->execute([
                ':submission_id' => $submission_id,
                ':file_path' => $targetFile
            ]);
            $message = "Upload successful!";
        } else {
            $message = "Failed to upload the file.";
        }
    } else {
        $message = "Invalid file type. Only JPG, PNG, or PDF allowed.";
    }
}

// Fetch accepted submissions and published status
$query = "
    SELECT ed.submission_id, ed.decision_type, pp.file_path, pa.status AS publish_status
    FROM editor_decisions ed
    JOIN submissions s ON ed.submission_id = s.submission_id
    JOIN metadata m ON m.submission_id = s.submission_id
    LEFT JOIN payment_proofs pp ON ed.submission_id = pp.submission_id
    LEFT JOIN publish_article pa ON ed.submission_id = pa.submission_id
    WHERE ed.decision_type = 'Accept' AND m.user_id = :user_id
";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Proof Upload</title>
    <link rel="stylesheet" href="pay.css">
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
        <a href="payment.php" class="active">Payment</a>
        <a href="myProfile.php">My Profile</a>
        <a href="guideline_author.php">Guideline Author</a>
        <?php if ($isReviewer): ?>
            <a href="reviewer_dashboard.php">Reviewer Section</a>
        <?php endif; ?>

        <a href="index.php">Logout</a>
    </div>

    <div class="main-content">
        <h2>Upload Proof of Payment</h2>

        <?php if ($message): ?>
            <p class="<?= strpos($message, 'successful') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Proof of Payment</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="4">No accepted submissions found.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['submission_id']) ?></td>
                            <td><?= htmlspecialchars($row['decision_type']) ?></td>
                            <td>
                                <?php if ($row['file_path']): ?>
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank">View Proof</a>
                                <?php else: ?>
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="submission_id" value="<?= $row['submission_id'] ?>">
                                        <input type="file" name="proof" required>
                                        <button type="submit">Upload</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$row['file_path']): ?>
                                    <span style="color:red;">Waiting for Payment</span>
                                <?php elseif ($row['publish_status'] === 'Published'): ?>
                                    <span style="color:green;">Payment Successful</span>
                                <?php else: ?>
                                    <span style="color:orange;">Pending for Payment</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
