<?php
session_start();

require('db_connection.php');
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$isReviewer = isset($user['is_reviewer']) && $user['is_reviewer']; // Check if the user is a reviewer
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JDTER Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: rgb(42,42,101);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

@media (max-width: 480px) {
    .container {
        margin: 20px;
    }
}

        .container {
            background: white;
            max-width: 800px;
            width: 100%;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .welcome {
            margin-bottom: 30px;
            text-align: center;
        }
        .welcome h1 {
            color:rgb(42,42,101);
            margin-bottom: 10px;
        }
        .welcome p {
            font-size: 16px;
            color: #555;
        }
        .quick-links {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }
        .quick-links a {
            display: block;
            padding: 15px 20px;
            background: #ffffff;
            border: 1px solid #dcdcdc;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            flex: 1 1 200px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .quick-links a:hover {
            background:rgb(42,42,101);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .note {
            margin-top: 30px;
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            color: #333;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="welcome">
        <h1>Welcome back, <?php echo htmlspecialchars($user['username']); ?>! 🎉</h1>
        <p>Manage your submissions, stay updated on notification.  Use the links below to navigate faster.</p>
    </div>

    <div class="quick-links">
        <a href="start.php">➕ New Submission</a>
        <a href="my_submission.php">📄 My Submissions</a>
        <a href="status_change.php">🔔 Notifications</a>
        <a href="payment.php">💳 Payments</a>
        <a href="myProfile.php">👤 My Profile</a>
        <a href="guideline_author.php">📜 Guideline Author</a>
        <?php if ($isReviewer): ?>
            <a href="reviewer_dashboard.php">📝 Reviewer Section</a>
        <?php endif; ?>
        <a href="index.php">🚪 Logout</a>
    </div>

    <div class="note">
        💡Submit your mansucript, get to know your real time status and feedback of your manuscript 
        and make a payment in a easy way !
    </div>
</div>

</body>
</html>
