<?php
session_start();
require('db_connection.php');

// Ensure user is logged in and submission_id exists
if (!isset($_SESSION['user']) || !isset($_SESSION['submission_id'])) {
    header('Location: login.php');
    exit;
}

// Retrieve submission ID and user ID
$submission_id = $_SESSION['submission_id'];
// Fetch the current user's data
$user = $_SESSION['user'];
$user_id = $user['id'];

// Fetch author details
$stmt = $pdo->prepare("SELECT full_name, email, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$author = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle metadata submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Insert metadata
        $stmt = $pdo->prepare("INSERT INTO metadata (submission_id, title, abstract, keywords, user_id ) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $submission_id,
            $_POST['title'],
            $_POST['abstract'],
            $_POST['keywords'] ?? '',
            $user_id
        ]);

        // Insert contributors (including author and additional contributors)
        if (isset($_POST['contributor_name'])) {
            foreach ($_POST['contributor_name'] as $index => $name) {
                $stmt = $pdo->prepare("INSERT INTO contributors (submission_id, name, email, role) 
                                       VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $submission_id,
                    $name,
                    $_POST['contributor_email'][$index],
                    $_POST['contributor_role'][$index]
                ]);
            }
        }

        // Update submission status
        $stmt = $pdo->prepare("UPDATE submissions SET status = 'metadata' WHERE submission_id = ?");
        $stmt->execute([$submission_id]);

        $pdo->commit();
        header('Location: confirmation.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Enter Metadata</title>
</head>

<body class="metadata-page">

    <div class="dashboard">

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php">
                    <h2>Dashboard</h2>
                </a>
            </div>
            <a href="#" onclick="toggleSubmenu('new_submission')">New Submission</a>
            <ul id="new_submission_menu" style="display: none;">
                <li><a href="start.php">1. Start</a></li>
                <li><a href="upload_submission.php">2. Upload Manuscript</a></li>
                <li><a href="enter_metadata.php">3. Enter Metadata</a></li>
                <li><a href="confirmation.php">4. Confirmation</a></li>
            </ul>
            <a href="my_submission.php">My Submission</a>
            <a href="status_change.php">Notification</a>
            <a href="payment.php">Payment</a>
            <a href="myProfile.php">My Profile</a>
            <a href="guideline_author.php">Guideline Author</a>
            <a href="index.php">Logout</a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <h1>Enter Metadata</h1>

            <form method="post">
                <!-- Title -->
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>

                <!-- Abstract -->
                <label for="abstract">Abstract:</label>
                <textarea name="abstract" id="abstract" required></textarea>

                <!-- Contributors Section -->
                <h3>Contributors</h3>
                <table id="contributors">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Automatically Add Author -->
                        <?php if ($author): ?>
                            <tr>
                                <td><input type="text" name="contributor_name[]" value="<?= htmlspecialchars($author['full_name']) ?>" readonly></td>
                                <td><input type="email" name="contributor_email[]" value="<?= htmlspecialchars($author['email']) ?>" readonly></td>
                                <td><input type="text" name="contributor_role[]" value="<?= htmlspecialchars($author['role']) ?>" readonly></td>
                                <td><button type="cbutton" disabled>Author</button></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Add Contributor Button -->
                <button type="cbutton" class="add-button" onclick="addContributor()">Add Contributor</button>

                <!-- Keywords -->
                <label for="keywords">Keywords:</label>
                <input type="text" name="keywords" id="keywords" required>

                <!-- Action Buttons -->
                <button type="submit">Continue</button>
                <button type="button" onclick="window.location.href='upload_submission.php'">Cancel</button>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Add a new contributor row
        function addContributor() {
            const table = document.getElementById('contributors').getElementsByTagName('tbody')[0];
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="contributor_name[]" required></td>
                <td><input type="email" name="contributor_email[]" required></td>
                <td><input type="text" name="contributor_role[]" required></td>
                <td><button type="button" onclick="removeContributor(this)">Remove</button></td>
            `;
            table.appendChild(row);
        }

        // Remove a contributor row
        function removeContributor(button) {
            button.parentElement.parentElement.remove();
        }

        // Toggle submenu visibility
        function toggleSubmenu(menuId) {
            const menu = document.getElementById(menuId + '_menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
    </script>

</body>

</html>
