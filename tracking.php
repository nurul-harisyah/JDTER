<?php
session_start();
require('db_connection.php'); // Ensure database connection is included

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo "You must be logged in to view tracking.";
    exit;
}

// Get submission_id from URL
if (!isset($_GET['submission_id'])) {
    echo "Invalid submission ID.";
    exit;
}

$submission_id = $_GET['submission_id'];

// Fetch manuscript statuses from submission_status table (e.g., submitted, under review)
// We assume all statuses (submitted, under review, etc.) are now logged as individual entries.
$query_status = "
    SELECT status, updated_at 
    FROM submission_status 
    WHERE submission_id = :submission_id
    ORDER BY updated_at ASC
";

// Fetch manuscript tracking from evaluations table (e.g., accept, minor revision)
$query_tracking = "
    SELECT recommendation, created_at 
    FROM evaluations 
    WHERE submission_id = :submission_id
    ORDER BY created_at ASC
";

// Fetch editor's final decision (e.g., accepted, rejected)
$query_final_decision = "
    SELECT decision_type, decision_date 
    FROM editor_decisions 
    WHERE submission_id = :submission_id
    ORDER BY decision_date DESC
    LIMIT 1
";

// Fetch publish status from publish_article table (e.g., Published, Not Published Yet)
$query_publish_status = "
    SELECT status, published_at 
    FROM publish_article 
    WHERE submission_id = :submission_id
    ORDER BY published_at DESC
    LIMIT 1
";

$stmt_status = $pdo->prepare($query_status);
$stmt_status->execute(['submission_id' => $submission_id]);
$status_history = $stmt_status->fetchAll(PDO::FETCH_ASSOC);

$stmt_tracking = $pdo->prepare($query_tracking);
$stmt_tracking->execute(['submission_id' => $submission_id]);
$tracking_history = $stmt_tracking->fetchAll(PDO::FETCH_ASSOC);

$stmt_final_decision = $pdo->prepare($query_final_decision);
$stmt_final_decision->execute(['submission_id' => $submission_id]);
$final_decision = $stmt_final_decision->fetch(PDO::FETCH_ASSOC);

$stmt_publish_status = $pdo->prepare($query_publish_status);
$stmt_publish_status->execute(['submission_id' => $submission_id]);
$publish_status = $stmt_publish_status->fetch(PDO::FETCH_ASSOC);

// Combine both histories into one array, making sure to include both status and tracking info
$all_history = array_merge($status_history, $tracking_history);

// If a final decision exists, add it to the history
if ($final_decision) {
    $all_history[] = [
        'status' => "final: " . htmlspecialchars($final_decision['decision_type']),
        'updated_at' => $final_decision['decision_date']
    ];
}

// If publish status exists, add it to the history
if ($publish_status) {
    $all_history[] = [
        'status' => "publish: " . htmlspecialchars($publish_status['status']),
        'updated_at' => $publish_status['published_at']
    ];
}

// Sort combined history by timestamp
usort($all_history, function($a, $b) {
    return strtotime($a['updated_at'] ?? $a['created_at']) - strtotime($b['updated_at'] ?? $b['created_at']);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Status</title>
    <link rel="stylesheet" href="tracking.css"> <!-- Add CSS for styling -->
</head>
<body>

<a href="status_change.php" class="back-button">← Back to Notifications</a>
<h3>Tracking History for Manuscript ID: <?= htmlspecialchars($submission_id) ?></h3>

<div class="tracking-container">
    <?php if ($all_history): ?>
        <ul class="tracking-list">
            <?php foreach ($all_history as $history): ?>
                <li>
                    <strong>Status:</strong>
                    <?php
                    // Determine status from submission status, evaluation recommendation, or publish status
                    if (isset($history['status'])) {
                        if ($history['status'] === 'under review') {
                            echo "Under Review";
                        } elseif ($history['status'] === 'submitted') {
                            echo "Submitted";
                        } elseif ($history['status'] === 'reject') {
                            echo "Rejected";
                        } elseif ($history['status'] === 'minor revision required') {
                            echo "Minor Revision Required";
                        } elseif ($history['status'] === 'major revision required') {
                            echo "Major Revision Required";
                        } elseif ($history['status'] === 'published') {
                            echo "Published";
                        } elseif (strpos($history['status'], 'final:') === 0) {
                            echo htmlspecialchars($history['status']);
                        } elseif (strpos($history['status'], 'publish:') === 0) {
                            echo "Published"; // Only display 'Published'
                        
                        }
                    } elseif (isset($history['recommendation'])) {
                        // Handle recommendations from evaluations
                        $recommendation = htmlspecialchars($history['recommendation']);
                        echo "Reviewer: " . $recommendation;
                    }
                    ?>
                    <br>
                    <?php
                    // Determine the date format based on the source (submission_status, evaluations, or publish_article)
                    $date = isset($history['updated_at']) ? $history['updated_at'] : (isset($history['created_at']) ? $history['created_at'] : "N/A");
                    $formatted_date = date("F j, Y, g:i a", strtotime($date));
                    ?>
                    <p>Updated on: <?= htmlspecialchars($formatted_date) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No tracking history available.</p>
    <?php endif; ?>
</div>

</body>
</html>
