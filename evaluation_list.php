<?php
session_start();

// Ensure the user is logged in and is a reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

require('db_connection.php');

$user_id = $_SESSION['user']['id'];

// Fetch assigned manuscripts and evaluation status
$query = "SELECT ra.assignment_id, m.title, s.submission_id, 
                 DATE_ADD(ra.assigned_at, INTERVAL 2 WEEK) AS due_date, 
                 COALESCE(e.status, 'pending') AS status
          FROM reviewer_assignments ra
          JOIN submissions s ON ra.submission_id = s.submission_id
          JOIN metadata m ON s.submission_id = m.submission_id
          LEFT JOIN evaluations e ON ra.assignment_id = e.assignment_id
          WHERE ra.reviewer_id = :user_id 
          AND DATE_ADD(ra.assigned_at, INTERVAL 2 WEEK) >= NOW()";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/evaluation_list.css">
    <title>Manuscripts for Evaluation</title>
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
    <h2>Manuscripts Assigned for Evaluation</h2>

    <?php if (empty($assignments)): ?>
        <p>No active manuscripts available for evaluation.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Manuscript ID</th>
                    <th>Title</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['assignment_id']); ?></td>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= htmlspecialchars($row['due_date']); ?></td>
                        <td class="<?php 
                            if ($row['status'] === 'submitted') {
                                echo 'completed';
                            } elseif ($row['status'] === 'draft') {
                                echo 'draft';
                            } else {
                                echo 'pending';
                            } ?>">
                            <?= ucfirst($row['status']); ?>
                        </td>
                        <td>
                            <button class="evaluate-btn" onclick="location.href='evaluation.php?assignment_id=<?= $row['assignment_id']; ?>'">
                                <?= $row['status'] === 'submitted' ? "View" : "Evaluate"; ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    </div>
</body>
</html>
