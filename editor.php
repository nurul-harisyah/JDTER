<?php
session_start();

// Check if the user is logged in as an editor
if (!isset($_SESSION['editor'])) {
    // Redirect to login page if not logged in
    header('Location: login.php?role=editor');
    exit;
}

require('db_connection.php'); // Ensure database connection is established

// Variables for messages
$message = '';
$error = '';

// Fetch pending reviewers for verification
$stmt = $pdo->prepare("SELECT id, full_name, email, certification_file, expertise, organization FROM users WHERE role = 'reviewer' AND is_verified = 0");
$stmt->execute();
$reviewers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle certification verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewerId = $_POST['reviewer_id'] ?? '';
    $action = $_POST['action'] ?? '';

    if (!empty($reviewerId) && $action === 'accept') {
        // Update reviewer as verified
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
        $stmt->execute([$reviewerId]);

        $message = "Reviewer certification has been verified successfully.";
    } elseif (!empty($reviewerId) && $action === 'reject') {
        // Optionally handle rejection (e.g., delete or notify reviewer)
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$reviewerId]);

        $message = "Reviewer certification has been rejected and the user has been removed.";
    } else {
        $error = "Invalid action or reviewer ID.";
    }

    // Refresh the list of pending reviewers
    $stmt = $pdo->prepare("SELECT id, full_name, email, certification_file, expertise, organization FROM users WHERE role = 'reviewer' AND is_verified = 0");
    $stmt->execute();
    $reviewers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php?role=editor');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor - Verify Reviewer Certifications</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>
    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Editor Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                    <a class="nav-link text-danger" href="?logout=true">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <h2 class="my-4">Reviewer Certification Verification</h2>

        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered">
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
                    <?php if (count($reviewers) > 0): ?>
                        <?php foreach ($reviewers as $reviewer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reviewer['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($reviewer['email']); ?></td>
                                <td><?php echo htmlspecialchars($reviewer['expertise']); ?></td>
                                <td><?php echo htmlspecialchars($reviewer['organization']); ?></td>
                                <td><a href="<?php echo htmlspecialchars($reviewer['certification_file']); ?>" target="_blank" class="btn btn-info btn-sm">View File</a></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="reviewer_id" value="<?php echo $reviewer['id']; ?>">
                                        <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No pending reviewers for verification.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
