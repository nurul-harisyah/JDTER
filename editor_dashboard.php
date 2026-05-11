<?php
session_start();
require('db_connection.php'); // Ensure database connection is established

// Check if the user is logged in as editor
if (!isset($_SESSION['editor'])) {
    header('Location: login.php?role=editor');
    exit;
}

// Determine which section to display based on the 'page' parameter
$page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Dashboard - JDTER Management System</title>
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
            color: rgb(42,42,101);
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
            background: rgb(42,42,101);
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
        <h1>Editor Dashboard</h1>
        <p>Manage reviewer assignments, publication decisions, and article publishing.</p>
    </div>

    <div class="quick-links">
        <a href="reviewer_verification.php">✓ Reviewer Verification</a>
        <a href="assign_reviewer.php">👥 Assign Reviewers</a>
        <a href="guideline.php">📋 Guidelines Making Decision</a>
        <a href="publication.php">📊 Publication Making Decision</a>
        <a href="publish_article.php">📰 Publication</a>
        <a href="index.php">🚪 Logout</a>
    </div>

    <div class="note">
        💡 Welcome to the Editor Dashboard. Please select an option from above to get started.
    </div>
</div>

</body>
</html>