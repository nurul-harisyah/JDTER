<?php
session_start();

// Check if the user is logged in as a reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

require('db_connection.php'); 

$reviewer_id = $_SESSION['user']['id'];

// Fetch only submitted evaluations for the logged-in reviewer
$query = "SELECT e.evaluation_id, e.assignment_id, m.title, 
                 ra.status AS reviewing_status, 
                 e.status AS review_status, 
                 e.updated_at AS completed_date
          FROM evaluations e
          JOIN reviewer_assignments ra ON e.assignment_id = ra.assignment_id
          JOIN submissions s ON ra.submission_id = s.submission_id
          JOIN metadata m ON s.submission_id = m.submission_id
          WHERE e.status = 'submitted' AND ra.reviewer_id = :reviewer_id
          ORDER BY e.updated_at DESC";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
$stmt->execute();
$completed_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/notification.css">
    <title>Viewing Completed Reviews</title>
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
    <h2>Viewing Completed Reviews</h2>
    
    <?php if (!empty($completed_reviews)): ?>
        <table>
            <thead>
                <tr>
                    <th>Evaluation ID</th>
                    <th>Title</th>
                    <th>Review Status</th>
                    <th>Completed Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($completed_reviews as $review): ?>
                    <tr>
                        <td><?= htmlspecialchars($review['evaluation_id']) ?></td>
                        <td><?= htmlspecialchars($review['title']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($review['review_status'])) ?></td>
                        <td><?= htmlspecialchars($review['completed_date']) ?></td>
                        <td>
                            <a href="view_review.php?evaluation_id=<?= $review['evaluation_id'] ?>" target="_blank">View Submitted Review</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No completed reviews found.</p>
    <?php endif; ?>
    </div>
</body>
</html>
