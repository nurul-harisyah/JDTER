<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['editor'])) {
    exit('Access denied: You must be logged in as an editor.');
}

// Fetch manuscripts that haven't been decided yet
$query = "
    SELECT s.submission_id, m.title, e.reviewer_id, e.recommendation
    FROM submissions s
    JOIN metadata m ON s.submission_id = m.submission_id
    JOIN reviewer_assignments ra ON s.submission_id = ra.submission_id
    JOIN evaluations e ON ra.assignment_id = e.assignment_id
    WHERE e.status = 'submitted' 
      AND s.submission_id NOT IN (SELECT submission_id FROM editor_decisions)
    ORDER BY s.submission_id, e.reviewer_id
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group recommendations by submission
$grouped = [];
foreach ($evaluations as $eval) {
    $sid = $eval['submission_id'];
    if (!isset($grouped[$sid])) {
        $grouped[$sid] = [
            'title' => $eval['title'],
            'reviews' => []
        ];
    }
    $grouped[$sid]['reviews'][] = [
        'reviewer_id' => $eval['reviewer_id'],
        'recommendation' => $eval['recommendation']
    ];
}

// Get any saved form data from session
$savedDecisions = $_SESSION['saved_decisions'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/publication.css">
    <title>Make Publication Decision</title>
</head>
<body>
<div class="sidebar">
        <div class="editor-dash"><button onclick="window.location.href='editor_dashboard.php'"><h1>Editor Dashboard</h1></button>
        <ul>
            <li><button onclick="window.location.href='reviewer_verification.php'">Reviewer Verification</button></li>
            <li><button onclick="window.location.href='assign_reviewer.php'">Assign Reviewers</button></li>
            <li><button onclick="window.location.href='guideline.php'">Guidelines Making Decision</button></li>
            <li><button onclick="window.location.href='publication.php'">Publication Making Decision</button></li>
            <li><button onclick="window.location.href='publish_article.php'">Publication</button></li>
            <li><button onclick="window.location.href='index.php'">Logout</button></li>
        </ul>
        </div>
    </div>
    <br>
    <div class="main-content">
    <h2>Finalize Publication Decisions</h2>

    <?php if (isset($_GET['message'])): ?>
        <div class="success-message"><?= htmlspecialchars($_GET['message']) ?></div>
    <?php endif; ?>

    <?php if (empty($grouped)): ?>
        <p>No submissions available for decision.</p>
    <?php else: ?>
        <?php foreach ($grouped as $submission_id => $data): ?>
            <div class="manuscript">
                <h3><?= htmlspecialchars($data['title']) ?> (ID: <?= $submission_id ?>)</h3>

                <div class="reviews">
                    <strong>Reviewer Recommendations:</strong>
                    <ul>
                        <?php foreach ($data['reviews'] as $review): ?>
                            <li>Reviewer <?= $review['reviewer_id'] ?>: <em><?= htmlspecialchars($review['recommendation']) ?></em></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <form method="POST" action="submit_decision.php">
                    <input type="hidden" name="submission_id" value="<?= $submission_id ?>">

                    <div class="form-section">
                        <label>Final Decision:</label>
                        <select name="decision" required>
                            <option value="">-- Select Decision --</option>
                            <option value="Accept" <?= isset($savedDecisions[$submission_id]) && $savedDecisions[$submission_id]['decision'] === 'Accept' ? 'selected' : '' ?>>Accept</option>
                            <option value="Minor Revision Required" <?= isset($savedDecisions[$submission_id]) && $savedDecisions[$submission_id]['decision'] === 'Minor Revision Required' ? 'selected' : '' ?>>Minor Revision Required</option>
                            <option value="Major Revision Required" <?= isset($savedDecisions[$submission_id]) && $savedDecisions[$submission_id]['decision'] === 'Major Revision Required' ? 'selected' : '' ?>>Major Revision Required</option>
                            <option value="Reject" <?= isset($savedDecisions[$submission_id]) && $savedDecisions[$submission_id]['decision'] === 'Reject' ? 'selected' : '' ?>>Reject</option>
                        </select>
                    </div>

                    <div class="form-section">
                        <label>Internal Comments (Not shown to author):</label>
                        <textarea name="comments" rows="4" placeholder="Enter internal notes here..."><?= isset($savedDecisions[$submission_id]) ? htmlspecialchars($savedDecisions[$submission_id]['comments']) : '' ?></textarea>
                    </div>

                    <div class="form-section">
                        <button type="submit" name="action" value="draft_email">Create Draft E-Mail</button>
                        <button type="submit" name="action" value="commit_decision">Commit Decision</button>
                    </div>
                </form>
                <button onclick="window.location.href='editor_dashboard.php'">Back</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>