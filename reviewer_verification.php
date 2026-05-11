<?php
session_start();
require('db_connection.php'); // Ensure database connection is established

// Check if the user is logged in as editor
if (!isset($_SESSION['editor'])) {
    header('Location: login.php?role=editor');
    exit;
}

// Fetch reviewers pending verification
$stmt = $pdo->prepare("SELECT id, full_name, email, certification_file, expertise, organization FROM users WHERE role = 'reviewer' AND is_verified = 0");
$stmt->execute();
$reviewers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle POST requests for reviewer actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewerId = $_POST['reviewer_id'] ?? '';
    $action = $_POST['action'] ?? '';

    if (!empty($reviewerId)) {
        if ($action === 'accept') {
            // Verify reviewer
            $stmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
            $stmt->execute([$reviewerId]);
            $message = "Reviewer verified successfully.";
        } elseif ($action === 'reject') {
            // Delete reviewer
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$reviewerId]);
            $message = "Reviewer rejected and removed.";
        }
        // Refresh the reviewer list
        $stmt = $pdo->prepare("SELECT id, full_name, email, certification_file, expertise, organization FROM users WHERE role = 'reviewer' AND is_verified = 0");
        $stmt->execute();
        $reviewers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewer Verification</title>
    <link rel="stylesheet" href="css/reviewer_verification.css">
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
    <h2>Reviewer Verification</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Expertise</th>
                <th>Organization</th>
                <th>Certification File</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($reviewers)): ?>
                <?php foreach ($reviewers as $reviewer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reviewer['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($reviewer['email']); ?></td>
                        <td><?php echo htmlspecialchars($reviewer['expertise']); ?></td>
                        <td><?php echo htmlspecialchars($reviewer['organization']); ?></td>
                        <td>
                            <a href="<?php echo htmlspecialchars($reviewer['certification_file']); ?>" target="_blank" class="btn btn-info">View File</a>
                        </td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="reviewer_id" value="<?php echo $reviewer['id']; ?>">
                                <button type="submit" name="action" value="accept" class="btn btn-success">Accept</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No pending reviewers for verification.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
