<?php
session_start();

// Check if the user is logged in as a reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

require('db_connection.php');

// Get evaluation_id from URL and validate it
$evaluation_id = isset($_GET['evaluation_id']) ? intval($_GET['evaluation_id']) : 0;
if ($evaluation_id <= 0) {
    exit("Error: Invalid evaluation_id.");
}

// Fetch evaluation details along with manuscript information
$query = "SELECT e.*, m.title, m.submission_id, 
                 ra.reviewer_id, ra.assignment_id
          FROM evaluations e
          JOIN reviewer_assignments ra ON e.assignment_id = ra.assignment_id
          JOIN submissions s ON ra.submission_id = s.submission_id
          JOIN metadata m ON s.submission_id = m.submission_id
          WHERE e.evaluation_id = :evaluation_id 
          AND e.status = 'submitted'
          AND ra.reviewer_id = :reviewer_id";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':evaluation_id', $evaluation_id, PDO::PARAM_INT);
$stmt->bindParam(':reviewer_id', $_SESSION['user']['id'], PDO::PARAM_INT);
$stmt->execute();
$review = $stmt->fetch(PDO::FETCH_ASSOC);

// Ensure a valid review is found and it belongs to the logged-in reviewer
if (!$review) {
    exit("Error: Review not found or you don't have permission to view it.");
}

// Process attachments if they exist
$attachments = !empty($review['attachments']) ? explode(',', $review['attachments']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submitted Review</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { width: 80%; margin: 20px auto; }
        .review-section { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .review-section h3 { margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .file-list { list-style-type: none; padding: 0; }
        .file-list li { margin-bottom: 5px; }
        .file-list a { color: #0066cc; text-decoration: none; }
        .file-list a:hover { text-decoration: underline; }
        .back-btn { margin-top: 20px; padding: 10px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        .back-btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Submitted Review Details</h1>
        
        <div class="review-section">
            <h3>Manuscript Information</h3>
            <p><strong>Title:</strong> <?= htmlspecialchars($review['title']) ?></p>
            <p><strong>Manuscript ID:</strong> <?= htmlspecialchars($review['submission_id']) ?></p>
            <p><strong>Assignment ID:</strong> <?= htmlspecialchars($review['assignment_id']) ?></p>
        </div>
        
        <div class="review-section">
            <h3>Review Decision</h3>
            <p><strong>Recommendation:</strong> 
                <?php 
                switch($review['recommendation']) {
                    case 'accept': echo 'Accept'; break;
                    case 'minor_revision': echo 'Minor Revision Required'; break;
                    case 'major_revision': echo 'Major Revision Required'; break;
                    case 'reject': echo 'Reject'; break;
                    default: echo 'Not specified';
                }
                ?>
            </p>
            <p><strong>Willing to review a revision:</strong> <?= ucfirst(htmlspecialchars($review['willing_to_review'])) ?></p>
        </div>
        
        <div class="review-section">
            <h3>Confidential Comments to the Associate Editor</h3>
            <p><?= nl2br(htmlspecialchars($review['confidential_comments'])) ?></p>
        </div>
        
        <div class="review-section">
            <h3>Comments to the Author</h3>
            <p><?= nl2br(htmlspecialchars($review['author_comments'])) ?></p>
        </div>
        
        <?php if (!empty($attachments)): ?>
        <div class="review-section">
            <h3>Attachments</h3>
            <ul class="file-list">
                <?php foreach ($attachments as $file): ?>
                    <li><a href="<?= htmlspecialchars($file) ?>" target="_blank"><?= htmlspecialchars(basename($file)) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <div class="review-section">
            <h3>Review Metadata</h3>
            <p><strong>Submitted on:</strong> <?= htmlspecialchars($review['updated_at']) ?></p>
        </div>
        
        <button class="back-btn" onclick="window.close();">Close Window</button>
    </div>
</body>
</html>