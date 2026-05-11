<?php
session_start();
require('db_connection.php');

// Check if the user is logged in as editor
if (!isset($_SESSION['editor'])) {
    header('Location: login.php?role=editor');
    exit;
}

// Fetch submissions and their related files
$stmt = $pdo->query("SELECT s.submission_id, s.user_id, s.status, s.submission_date, m.title, sf.file_type, u.full_name AS author_name, 
    (SELECT COUNT(*) FROM reviewer_assignments WHERE reviewer_assignments.submission_id = s.submission_id) AS assigned_reviewers
    FROM submissions AS s
    LEFT JOIN submission_files AS sf ON s.submission_id = sf.submission_id
    JOIN metadata m ON m.submission_id = s.submission_id
    LEFT JOIN users AS u ON s.user_id = u.id
    WHERE s.status IN ('confirmation', 'assigned')");
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all expertise
$expertise_list = $pdo->query("SELECT DISTINCT expertise FROM users WHERE role = 'reviewer' AND is_verified = 1")->fetchAll(PDO::FETCH_COLUMN);

// Handle reviewer assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id'], $_POST['reviewer_ids'])) {
    $submission_id = $_POST['submission_id'];
    $reviewer_ids = $_POST['reviewer_ids'];

    foreach ($reviewer_ids as $reviewer_id) {
        $pdo->prepare("INSERT INTO reviewer_assignments (submission_id, reviewer_id) VALUES (?, ?)")->execute([$submission_id, $reviewer_id]);
    }


// Update review_status to 'pending'
$stmt = $pdo->prepare("UPDATE review_status SET status = 'pending' WHERE submission_id = ?");
$stmt->execute([$submission_id]);



    $pdo->prepare("UPDATE submissions SET status = 'assigned' WHERE submission_id = ?")->execute([$submission_id]);
    echo "<script>alert('Reviewers assigned successfully!'); window.location.href='assign_reviewer.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Reviewers</title>
    <link rel="stylesheet" href="css/assign_reviewer.css">
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
    <div class="container">
    <h1>Assign Reviewers</h1>
    <table>
        <thead>
        <tr>
            <th>Manuscript ID</th>
            <th>Manuscript Title</th>
            <th>Author</th>
            <th>Date Submitted</th>
            <th>File Type</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($submissions): ?>
            <?php foreach ($submissions as $submission): ?>
                <tr>
                    <td><?= htmlspecialchars($submission['submission_id']) ?></td>
                    <td><?= htmlspecialchars($submission['title']) ?></td>
                    <td><?= htmlspecialchars($submission['author_name']) ?></td>
                    <td><?= htmlspecialchars(date("d-M-Y", strtotime($submission['submission_date']))) ?></td>
                    <td><?= htmlspecialchars($submission['file_type']) ?></td>
                    <td><?= $submission['assigned_reviewers'] >= 2 ? 'Assigned' : 'Not Assigned' ?></td>
                    <td class="actions">
                        <?php if ($submission['assigned_reviewers'] < 2): ?>
                            <button onclick="openModal(<?= $submission['submission_id'] ?>)">Assign</button>
                        <?php else: ?>
                            <button class="btn-disabled" disabled>Fully Assigned</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No submissions available for review assignment.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal for assigning reviewers -->
    <div id="reviewer-modal" class="modal">
        <h2>Assign Reviewers</h2>
        <form method="POST" action="">
            <input type="hidden" name="submission_id" id="submission-id">

            <label for="expertise">Select Expertise:</label>
            <select id="expertise" onchange="filterReviewers()">
                <option value="">-- Select Expertise --</option>
                <?php foreach ($expertise_list as $expertise): ?>
                    <option value="<?= htmlspecialchars($expertise) ?>">
                        <?= htmlspecialchars($expertise) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div id="reviewer-list" class="reviewer-list">
                <!-- Reviewers will be loaded here dynamically -->
            </div>
            <br>
            <button type="submit">Assign Reviewers</button>
            <button type="button" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
    const reviewers = <?= json_encode($pdo->query("SELECT id, full_name, expertise FROM users WHERE role = 'reviewer' AND is_verified = 1")->fetchAll(PDO::FETCH_ASSOC)) ?>;

    function openModal(submissionId) {
        document.getElementById('submission-id').value = submissionId;
        document.getElementById('reviewer-modal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('reviewer-modal').style.display = 'none';
    }

    function filterReviewers() {
        const expertise = document.getElementById('expertise').value;
        const reviewerList = document.getElementById('reviewer-list');
        reviewerList.innerHTML = '';

        const filteredReviewers = reviewers.filter(r => !expertise || r.expertise === expertise);

        filteredReviewers.forEach(reviewer => {
            const div = document.createElement('div');
            div.className = 'reviewer-item';
            div.innerHTML = `
                <label>
                    <input type="checkbox" name="reviewer_ids[]" value="${reviewer.id}">
                    Reviewer, ${reviewer.full_name} (${reviewer.id})
                </label>
            `;
            reviewerList.appendChild(div);
        });
    }
</script>
</body>
</html>
