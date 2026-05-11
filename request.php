<?php
session_start();

// Check if the user is logged in as a reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

require('db_connection.php');

$user_id = $_SESSION['user']['id']; // ✅ Set the user ID from the session

$query = "SELECT ra.assignment_id, m.title, s.submission_id, ra.status, DATE_ADD(ra.assigned_at, INTERVAL 2 WEEK) AS due_date, m.abstract 
          FROM reviewer_assignments ra
          JOIN submissions s ON ra.submission_id = s.submission_id
          JOIN metadata m ON s.submission_id = m.submission_id
          WHERE ra.reviewer_id = :user_id AND ra.status = 'pending'";

$stmt = $pdo->prepare($query);
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Review Invitations</title>
    <link rel="stylesheet" href="css/request.css">
    <style>
        .view-btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="review-dash"><button onclick="window.location.href='reviewer_dashboard.php'">
                <h1>Reviewer Dashboard</h1>
            </button>
            <ul>
                <li><button onclick="window.location.href='reviewerapplication.php'">Apply Reviewer</button></li>
                <li><button onclick="window.location.href='request.php'">Request</button></li>
                <li><button onclick="window.location.href='download_assignment.php'">Download</button></li>
                <li><button onclick="window.location.href='evaluation_list.php'">Evaluate Manuscripts</button></li>
                <li><button onclick="window.location.href='notification.php'">Notification</button></li>
                <li><button onclick="window.location.href='profile.php'">My Profile</button></li>
                <li><button onclick="window.location.href='index.php'">Logout</button></li>
            </ul>
        </div>
    </div>
    <div class="main-content">
        <h2>You Have a New Review Invitation</h2>

        <?php if (empty($result)): ?>
            <p>No pending assignments found.</p>
        <?php else: ?>
            <table border="1">
                <tr>
                    <th>Manuscript ID</th>
                    <th>Title</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td><?php echo $row['assignment_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo $row['due_date']; ?></td>
                        <td>
                            <button class="view-btn"
                                onclick="window.location.href='view_abstract.php?assignment_id=<?php echo $row['assignment_id']; ?>'">
                                View Abstract
                            </button>

                            <?php if (strtotime($row['due_date']) < time()): ?>
                                <span style="color: red;">Expired Date</span>
                            <?php elseif ($row['status'] === 'accepted'): ?>
                                <!-- Show download button if the assignment is accepted -->
                                <button
                                    onclick="window.location.href='download_assignment.php?assignment_id=<?php echo $row['assignment_id']; ?>'">
                                    Download Assignment
                                </button>
                            <?php else: ?>

                                <?php
                                // Update status to 'under review'
                                $stmt = $pdo->prepare("UPDATE submission_status SET status = 'under review' WHERE submission_id = :submission_id");
                                $stmt->execute(['submission_id' => $row['submission_id']]);
                                ?>


                                <!-- Show accept/decline buttons if the assignment is still pending -->
                                <button
                                    onclick="window.location.href='accept_review.php?assignment_id=<?php echo $row['assignment_id']; ?>'">
                                    Accept
                                </button>
                                <button
                                    onclick="window.location.href='decline_confirmation.php?assignment_id=<?php echo $row['assignment_id']; ?>'">
                                    Decline
                                </button>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
</body>
</div>
</body>

</html>