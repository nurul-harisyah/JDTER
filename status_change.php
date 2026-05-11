<?php
session_start();
require('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo "You must be logged in to view notifications.";
    exit;
}

$user = $_SESSION['user'];
$user_id = is_array($user) ? $user['id'] : $user;

// ✅ Updated query to include submission_status table
$query = "
    SELECT 
        s.submission_id, 
        s.submission_date,
        sf.file_id,
        ed.decision_type AS final_decision,
        ed.decision_date,
        pa.status AS published_status,
        e1.recommendation AS reviewer1_status,
        e2.recommendation AS reviewer2_status,
        ss.status AS submission_status,
        ss.updated_at AS status_updated_at,
        GREATEST(
            IFNULL(s.submission_date, '0000-00-00 00:00:00'), 
            IFNULL(ed.decision_date, '0000-00-00 00:00:00'),
            IFNULL(pa.published_at, '0000-00-00 00:00:00'),
            IFNULL(ss.updated_at, '0000-00-00 00:00:00')
        ) AS latest_update
    FROM submissions s
    LEFT JOIN submission_files sf ON s.submission_id = sf.submission_id
    LEFT JOIN evaluations e1 ON s.submission_id = e1.submission_id 
    LEFT JOIN evaluations e2 ON s.submission_id = e2.submission_id 
    LEFT JOIN editor_decisions ed ON s.submission_id = ed.submission_id
    LEFT JOIN publish_article pa ON s.submission_id = pa.submission_id
    LEFT JOIN submission_status ss ON s.submission_id = ss.submission_id
    WHERE s.user_id = :user_id
    ORDER BY latest_update DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Filter to keep only the latest status per submission_id
$uniqueSubmissions = [];
foreach ($submissions as $submission) {
    $id = $submission['submission_id'];
    if (!isset($uniqueSubmissions[$id]) || $submission['latest_update'] > $uniqueSubmissions[$id]['latest_update']) {
        $uniqueSubmissions[$id] = $submission;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Notifications</title>
    <link rel="stylesheet" href="noti.css">
    <script>
        function toggleSubmenu(id) {
            var submenu = document.getElementById(id + '_menu');
            submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>

<div class="dashboard">
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

    <div class="main-content">
        <div class="notification-container">
            <?php if ($uniqueSubmissions): ?>
                <?php foreach ($uniqueSubmissions as $submission): 
                    $file_id = $submission['file_id'] ?? "N/A";
                    $submission_id = $submission['submission_id'];
                    $submission_date = $submission['submission_date'];
                    $reviewer1_status = $submission['reviewer1_status'];
                    $reviewer2_status = $submission['reviewer2_status'];
                    $final_decision = $submission['final_decision'];
                    $published_status = $submission['published_status'];
                    $submission_status = $submission['submission_status'];
                    $latest_update = $submission['latest_update'];

                    // ✅ Determine the latest status using priority
                    $latest_status = "submitted"; // Default

                    if ($submission_status) {
                        $latest_status = $submission_status;
                    }

                    if ($reviewer1_status) {
                        $latest_status = "reviewer1: $reviewer1_status";
                    }
                    if ($reviewer2_status) {
                        $latest_status = "reviewer2: $reviewer2_status";
                    }
                    if ($final_decision) {
                        $latest_status = "final: $final_decision";
                    }
                    if ($published_status === 'Published') {
                        $latest_status = "published";
                    }

                    $message = "Your manuscript ID ($submission_id) = $latest_status";
                    $status_class = strtolower(str_replace(" ", "_", $latest_status));
                ?>
                    <div class="notification <?= htmlspecialchars($status_class) ?>">
                        <a href="tracking.php?submission_id=<?= $submission_id ?>" class="message-link">
                            <p class="message"><?= htmlspecialchars($message) ?></p>
                        </a>
                        <p class="status">Latest status: <?= htmlspecialchars($latest_status) ?></p>
                        <span class="date">Last updated: <?= $latest_update ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No notifications available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
