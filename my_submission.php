<?php
session_start();
require('db_connection.php');

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$isReviewer = isset($user['role']) && $user['role'] === 'reviewer';
$user_id = is_array($user) ? $user['id'] : $user;
$selected_status = isset($_GET['status']) ? $_GET['status'] : 'all';

$query = "
    SELECT 
        sf.file_id, sf.file_name, sf.file_type, sf.uploaded_at, 
        m.title, sf.submission_id, 
        GROUP_CONCAT(e.author_comments ORDER BY e.evaluation_id SEPARATOR '|||') AS combined_comments,
        GROUP_CONCAT(e.attachments ORDER BY e.evaluation_id) AS all_attachments,
        GROUP_CONCAT(e.recommendation ORDER BY e.evaluation_id SEPARATOR '|||') AS all_recommendations,
        ed.decision_type,
        ss.status AS submission_status,
        pa.status AS published_status
    FROM submission_files sf
    JOIN metadata m ON sf.submission_id = m.submission_id
    LEFT JOIN submission_status ss ON sf.submission_id = ss.submission_id
    LEFT JOIN editor_decisions ed ON sf.submission_id = ed.submission_id
    LEFT JOIN evaluations e ON sf.submission_id = e.submission_id
    LEFT JOIN publish_article pa ON sf.submission_id = pa.submission_id
    WHERE m.user_id = :user_id
      AND sf.uploaded_at = (
          SELECT MAX(sf2.uploaded_at)
          FROM submission_files sf2
          WHERE sf2.submission_id = sf.submission_id
      )
";

if ($selected_status !== 'all') {
    $query .= " AND (
        CASE
            WHEN pa.status = 'Published' THEN 'published'
            WHEN ed.decision_type = 'Accept' THEN 'accept'
            WHEN ed.decision_type = 'Minor Revision Required' THEN 'minor revision required'
            WHEN ed.decision_type = 'Major Revision Required' THEN 'major revision required'
            WHEN ed.decision_type = 'Reject' THEN 'reject'
            WHEN ss.status = 'under review' THEN 'under review'
            ELSE 'submitted'
        END
    ) = :selected_status";
}

$query .= " GROUP BY sf.file_id ORDER BY sf.uploaded_at DESC";

$stmt = $pdo->prepare($query);
$params = ['user_id' => $user_id];
if ($selected_status !== 'all') {
    $params['selected_status'] = $selected_status;
}
$stmt->execute($params);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete row action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_row'])) {
    $file_id_to_delete = $_POST['file_id'];
    try {
        $delete_stmt = $pdo->prepare("DELETE FROM submission_files WHERE file_id = ?");
        $delete_stmt->execute([$file_id_to_delete]);
        header('Location: my_submission.php');
        exit;
    } catch (PDOException $e) {
        echo "Error deleting file: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - My Submissions</title>
    <link rel="stylesheet" href="styles2.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script>
        function toggleSubmenu(id) {
            var submenu = document.getElementById(id + '_menu');
            submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
        }

        function toggleFeedback(id) {
            var moreText = document.getElementById('more_' + id);
            var btnText = document.getElementById('btn_' + id);
            if (moreText.style.display === 'none') {
                moreText.style.display = 'inline';
                btnText.innerText = 'See Less';
            } else {
                moreText.style.display = 'none';
                btnText.innerText = 'See More';
            }
        }
    </script>
</head>

<body class="my-submission-page">
<div class="dashboard">
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
        <a href="guideline_author.php">Guideline Author</a>
        <?php if ($isReviewer): ?>
            <a href="reviewer_dashboard.php">Reviewer Section</a>
        <?php endif; ?>
        <a href="index.php">Logout</a>
    </div>

    <div id="my_submission" class="section">
        <div class="submission-container">
            <h1>My Submissions</h1>

            <form method="GET" action="my_submission.php">
                <label for="status">Filter by Status: </label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="all" <?= $selected_status == 'all' ? 'selected' : '' ?>>All</option>
                    <option value="under review" <?= $selected_status == 'under review' ? 'selected' : '' ?>>Under Review</option>
                    <option value="reject" <?= $selected_status == 'reject' ? 'selected' : '' ?>>Reject</option>
                    <option value="minor revision required" <?= $selected_status == 'minor revision required' ? 'selected' : '' ?>>Minor Revision</option>
                    <option value="major revision required" <?= $selected_status == 'major revision required' ? 'selected' : '' ?>>Major Revision</option>
                    <option value="published" <?= $selected_status == 'published' ? 'selected' : '' ?>>Published</option>
                </select>
            </form>

            <table border="1">
                <thead>
                <tr>
                    <th style="background-color: rgb(42, 42, 101); color: #fff;">Article ID</th>
                    <th style="background-color: rgb(42, 42, 101); color: #fff;">Title</th>
                    <th style="background-color: rgb(42, 42, 101); color: #fff;">Uploaded At</th>
                    <th style="background-color: rgb(42, 42, 101); color: #fff;">Download File</th>
                    <th style="background-color: rgb(42, 42, 101); color: #fff;">Status</th>
                    <th style="background-color: rgb(42, 42, 101); color: #fff;">Feedback</th>
                </tr>
                </thead>

                <tbody>
                <?php if (empty($submissions)): ?>
                    <tr><td colspan="6">No submissions found.</td></tr>
                <?php else: ?>
                    <?php foreach ($submissions as $index => $submission): ?>
                        <tr>
                            <td><?= htmlspecialchars($submission['submission_id']) ?></td>
                            <td><?= htmlspecialchars($submission['title']) ?></td>
                            <td><?= htmlspecialchars($submission['uploaded_at']) ?></td>
                            <td class="center-icon">
                                <a href="download.php?file_id=<?= $submission['file_id'] ?>" title="Download">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </td>
                            <td>
                                <?php
                                if ($submission['published_status'] === 'Published') {
                                    echo "Published";
                                } elseif (!empty($submission['decision_type'])) {
                                    echo "<strong>Final:</strong> " . htmlspecialchars($submission['decision_type']);
                                } elseif (!empty($submission['all_recommendations'])) {
                                    $recommendations = explode('|||', $submission['all_recommendations']);
                                    foreach ($recommendations as $i => $rec) {
                                        echo "<strong>Reviewer_" . ($i + 1) . ":</strong> " . htmlspecialchars($rec) . "<br>";
                                    }
                                } elseif ($submission['submission_status'] === 'under review') {
                                    echo "Under Review";
                                } else {
                                    echo "Submitted";
                                }

                                $final = strtolower($submission['decision_type'] ?? '');
                                if (in_array($final, ['minor revision required', 'major revision required'])): ?>
                                    <br>
                                    <form action="start.php" method="post" style="display:inline;">
                                        <input type="hidden" name="resubmit_id" value="<?= $submission['submission_id'] ?>">
                                        <button type="submit">Resubmit</button>
                                    </form>
                                    <form action="my_submission.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="file_id" value="<?= $submission['file_id'] ?>">
                                        <button type="submit" name="delete_row" onclick="return confirm('Are you sure you want to delete this submission?')">Delete Row</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                if (!empty($submission['combined_comments'])) {
                                    $comments = explode('|||', $submission['combined_comments']);
                                    foreach ($comments as $i => $comment) {
                                        $short = mb_substr($comment, 0, 100);
                                        $full = htmlspecialchars($comment);
                                        $hasMore = mb_strlen($comment) > 100;
                                        echo "<div style='margin-bottom:5px; border-left:3px solid #007BFF; padding:5px; background:#f9f9f9;'>";
                                        echo "<strong>Reviewer_" . ($i + 1) . ":</strong> ";
                                        if ($hasMore) {
                                            echo htmlspecialchars($short) . "<span id='more_{$index}_$i' style='display:none;'> " . htmlspecialchars(mb_substr($comment, 100)) . "</span> ";
                                            echo "<a href='javascript:void(0);' id='btn_{$index}_$i' onclick='toggleFeedback(\"{$index}_$i\")'>See More</a>";
                                        } else {
                                            echo $full;
                                        }
                                        echo "</div>";
                                    }
                                } else {
                                    echo "No feedback available.";
                                }

                                if (!empty($submission['all_attachments'])) {
                                    $attachmentsArray = explode(",", $submission['all_attachments']);
                                    foreach ($attachmentsArray as $attachment) {
                                        if (!empty($attachment)) {
                                            echo '<br><a href="' . htmlspecialchars($attachment) . '" target="_blank">View Attachment</a>';
                                        }
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
