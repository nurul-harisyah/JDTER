<?php
session_start();
require('db_connection.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

$assignment_id = intval($_GET['assignment_id']);

// Fetch evaluation details
$query = "SELECT e.*, m.title FROM evaluations e
          JOIN reviewer_assignments ra ON e.assignment_id = ra.assignment_id
          JOIN submissions s ON ra.submission_id = s.submission_id
          JOIN metadata m ON s.submission_id = m.submission_id
          WHERE e.assignment_id = :assignment_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
$stmt->execute();
$evaluation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evaluation) {
    echo "No evaluation found.";
    exit;
}

// If print is requested, update status
if (isset($_GET['print']) && $_GET['print'] == 'true') {
    $updateQuery = "UPDATE evaluations SET status = 'Finished Evaluation' WHERE assignment_id = :assignment_id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
    $updateStmt->execute();

    echo "<script>window.onload = function() { window.print(); }</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Print Review</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { width: 80%; margin: auto; }
        .button-container { margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Evaluation Report for: <?php echo htmlspecialchars($evaluation['title']); ?></h2>
    <p><strong>Willing to Review Revision:</strong> <?php echo htmlspecialchars($evaluation['willing_to_review']); ?></p>
    <p><strong>Recommendation:</strong> <?php echo htmlspecialchars($evaluation['recommendation']); ?></p>
    <p><strong>Confidential Comments to Editor:</strong></p>
    <p><?php echo nl2br(htmlspecialchars($evaluation['confidential_comments'])); ?></p>
    <p><strong>Comments to Author:</strong></p>
    <p><?php echo nl2br(htmlspecialchars($evaluation['author_comments'])); ?></p>

    <div class="button-container">
        <a href="print_review.php?assignment_id=<?php echo $assignment_id; ?>&print=true">
            <button type="button">Save & Print</button>
        </a>
        <a href="evaluation_list.php">Back to Evaluations</a>
    </div>
</div>

</body>
</html>
