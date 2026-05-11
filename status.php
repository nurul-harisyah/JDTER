<?php
// Database connection
require_once 'db_connection.php'; // Adjust if needed

// Get current user ID (adjust based on your session or auth mechanism)
$user_id = $_SESSION['user_id'] ?? 0;

// Fetch manuscripts and their statuses
$query = "
    SELECT s.submission_id, s.file_path, s.submission_date, 
        IF(ra.status = 'pending', 'Pending', 
            IF(ra.status = 'accepted', 'Under Review',
                IF(e.recommendation = 'accept', 'Pending: Payment',
                    IF(e.recommendation = 'minor_revision', 'Minor Revision Required',
                        IF(e.recommendation = 'major_revision', 'Major Revision Required',
                            IF(e.recommendation = 'reject', 'Rejected',
                                'Submitted'
                            )
                        )
                    )
                )
            )
        ) AS current_status
    FROM submissions s
    LEFT JOIN reviewer_assignments ra ON s.submission_id = ra.submission_id
    LEFT JOIN evaluations e ON ra.assignment_id = e.assignment_id
    WHERE s.user_id = :user_id
    ORDER BY s.submission_date DESC;
";

$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>My Manuscripts</h2>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>File</th>
            <th>Submission Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($submissions) > 0): ?>
            <?php foreach ($submissions as $submission): ?>
                <tr>
                    <td><?= htmlspecialchars($submission['submission_id']); ?></td>
                    <td><a href="<?= htmlspecialchars($submission['file_path']); ?>" target="_blank">Download</a></td>
                    <td><?= htmlspecialchars($submission['submission_date']); ?></td>
                    <td><?= htmlspecialchars($submission['current_status']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No submissions found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
