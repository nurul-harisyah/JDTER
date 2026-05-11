<?php
session_start();
require('db_connection.php');

// Ensure only reviewers access the page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

// Get assignment_id from URL and validate it
$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
if ($assignment_id <= 0) {
    exit("Error: Invalid assignment_id.");
}

// Fetch manuscript details (Ensure submission_id is not null)
$query = "SELECT m.submission_id, m.title, m.abstract
          FROM reviewer_assignments ra
          LEFT JOIN submissions s ON ra.submission_id = s.submission_id
          LEFT JOIN metadata m ON s.submission_id = m.submission_id
          WHERE ra.assignment_id = :assignment_id AND ra.status = 'accepted'";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
$stmt->execute();
$manuscript = $stmt->fetch(PDO::FETCH_ASSOC);

// Ensure a valid manuscript is found
if (!$manuscript || empty($manuscript['submission_id'])) {
    exit("Error: No valid manuscript found for assignment_id $assignment_id.");
}

// Fetch existing evaluation (if saved as draft)
$evaluation_query = "SELECT * FROM evaluations WHERE assignment_id = :assignment_id";
$evaluation_stmt = $pdo->prepare($evaluation_query);
$evaluation_stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
$evaluation_stmt->execute();
$evaluation = $evaluation_stmt->fetch(PDO::FETCH_ASSOC);

// Pre-fill values if draft exists
$willing_to_review = $evaluation['willing_to_review'] ?? '';
$recommendation = $evaluation['recommendation'] ?? '';
$confidential_comments = $evaluation['confidential_comments'] ?? '';
$author_comments = $evaluation['author_comments'] ?? '';
$attachments = !empty($evaluation['attachments']) ? explode(',', $evaluation['attachments']) : [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Evaluation</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 60%; margin: auto; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        textarea { width: 100%; height: 100px; }
        .buttons { margin-top: 10px; }
    </style>
</head>

<body>
    <div class="container">
        <h2>Evaluation for Manuscript: <?php echo htmlspecialchars($manuscript['title']); ?></h2>
        <p><strong>Manuscript ID:</strong> <?php echo $manuscript['submission_id']; ?></p>
        <p>
            <button onclick="window.location.href='view_abstract.php?assignment_id=<?php echo $assignment_id; ?>'">
                View Abstract
            </button>
        </p>

        <form action="submit_evaluation.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="assignment_id" value="<?php echo $assignment_id; ?>">
            <input type="hidden" name="submission_id" value="<?php echo $manuscript['submission_id']; ?>">

            <label>Would you be willing to review a revision of this manuscript?</label>
            <input type="radio" name="willing_to_review" value="yes" <?php echo ($willing_to_review == 'yes') ? 'checked' : ''; ?> required> Yes
            <input type="radio" name="willing_to_review" value="no" <?php echo ($willing_to_review == 'no') ? 'checked' : ''; ?>> No

            <label>Recommendation</label>
            <input type="radio" name="recommendation" value="accept" <?php echo ($recommendation == 'accept') ? 'checked' : ''; ?> required> Accept<br>
            <input type="radio" name="recommendation" value="minor_revision" <?php echo ($recommendation == 'minor_revision') ? 'checked' : ''; ?>> Minor Revision Required<br>
            <input type="radio" name="recommendation" value="major_revision" <?php echo ($recommendation == 'major_revision') ? 'checked' : ''; ?>> Major Revision Required<br>
            <input type="radio" name="recommendation" value="reject" <?php echo ($recommendation == 'reject') ? 'checked' : ''; ?>> Reject<br>

            <label>Confidential Comments to the Associate Editor</label>
            <textarea name="confidential_comments" required><?php echo htmlspecialchars($confidential_comments); ?></textarea>

            <label>Comments to the Author</label>
            <textarea name="author_comments" required><?php echo htmlspecialchars($author_comments); ?></textarea>

            <label>Attach Files</label>
            <input type="file" name="attachments[]" multiple>
            
            <!-- Show previously uploaded files -->
            <?php if (!empty($attachments)): ?>
                <p>Previously uploaded files:</p>
                <ul>
                    <?php foreach ($attachments as $file): ?>
                        <li><a href="<?php echo htmlspecialchars($file); ?>" target="_blank"><?php echo basename($file); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="buttons">
            <button type="submit" name="action" value="save_draft" class="evaluate-btn">Save as Draft</button>
            <button type="submit" name="action" value="submit_review" class="evaluate-btn">Submit Review</button>
            <button type="button" onclick="window.location.href='evaluation_list.php';" class="back-btn">Back</button>

            </div>
        </form>
    </div>
</body>
</html>
