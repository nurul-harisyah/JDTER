<?php
require 'db_connection.php';
session_start();

// Check if the user is logged in as an editor
if (!isset($_SESSION['editor'])) {
    exit('Access denied: You must be logged in as an editor.');
}

$editor = $_SESSION['editor']; // Fetch editor information
$message = '';

$query = "
    SELECT ed.submission_id, ed.decision_type, pp.file_path, m.title, c.name AS author_name
    FROM editor_decisions ed
    JOIN submissions s ON ed.submission_id = s.submission_id
    JOIN metadata m ON m.submission_id = s.submission_id
    JOIN contributors c ON c.submission_id = s.submission_id
    LEFT JOIN payment_proofs pp ON ed.submission_id = pp.submission_id
    WHERE ed.decision_type = 'Accept' AND pp.file_path IS NOT NULL
    AND (c.role = 'author' OR c.role = 'co-author' OR c.role IS NULL OR c.role = '')
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Publish article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id']) && isset($_POST['publish'])) {
    $submission_id = $_POST['submission_id'];

    // Check if the article is already published
    $stmt = $pdo->prepare("SELECT status FROM publish_article WHERE submission_id = :submission_id");
    $stmt->execute([':submission_id' => $submission_id]);
    $status = $stmt->fetchColumn();

    // If not already published, update status
    if ($status !== 'Published') {
        // Insert 'published' status into publish_article table
        $stmt = $pdo->prepare("INSERT INTO publish_article (submission_id, status) 
                               VALUES (:submission_id, 'Published')");
        $stmt->execute([':submission_id' => $submission_id]);

        // Update the article's status to 'published' in the submissions table
        $stmt = $pdo->prepare("UPDATE submissions SET status = 'published' WHERE submission_id = :submission_id");
        $stmt->execute([':submission_id' => $submission_id]);

        $message = "Article published successfully!";
    } else {
        $message = "This article is already published.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Publish Article</title>
    <link rel="stylesheet" href="css/publish_article.css">
    <script>
        function toggleSubmenu(id) {
            var submenu = document.getElementById(id + '_menu');
            submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
        }
    </script>
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
        <h2>Publish Accepted Articles</h2>

        <?php if ($message): ?>
            <p class="<?= strpos($message, 'successful') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Submission ID</th>
                    <th>Title</th>
                    <th>Author(s)</th>
                    <th>Proof of Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="6">No accepted submissions with payment proof found.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['submission_id']) ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author_name']) ?></td>
                            <td>
                                <?php if ($row['file_path']): ?>
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank">Download Proof</a>
                                <?php else: ?>
                                    No proof uploaded
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    // Check publish status
                                    $stmt = $pdo->prepare("SELECT status FROM publish_article WHERE submission_id = :submission_id");
                                    $stmt->execute([':submission_id' => $row['submission_id']]);
                                    $publishStatus = $stmt->fetchColumn();
                                    echo htmlspecialchars($publishStatus ?: 'Not Published Yet');
                                ?>
                            </td>
                            <td>
                                <?php if ($publishStatus !== 'Published'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="submission_id" value="<?= $row['submission_id'] ?>">
                                        <button type="submit" name="publish" class="publish-button">Publish</button>
                                    </form>
                                <?php else: ?>
                                    <span>Already Published</span>
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