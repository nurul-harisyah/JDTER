<?php
session_start();

require('db_connection.php');
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewer Dashboard</title>
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
        
        .btn-back {
            display: block;
            width: fit-content;
            margin: 20px auto 0;
            padding: 10px 20px;
            background: rgb(42,42,101);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: darkblue;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="welcome">
        <h1>Reviewer Dashboard</h1>
        <p>Select an option to get started with reviewer activities.</p>
    </div>

    <div class="quick-links">
        <a href="term_condition.php">📜 Term & Conditions</a>
        <a href="reviewerapplication.php">✍️ Apply Reviewer</a>
        <a href="request.php">📨 Request</a>
        <a href="download_assignment.php">📥 Download</a>
        <a href="evaluation_list.php">📝 Evaluate Manuscripts</a>
        <a href="notification.php">🔔 Notification</a>
        <a href="profile.php">👤 My Profile</a>
        <a href="index.php">🚪 Logout</a>
    </div>

    <div class="note">
        💡 Welcome to the Reviewer Dashboard. Please select an option from above to get started.
    </div>
    
    <button onclick="window.location.href='dashboard.php'" class="btn-back">Back to Main Dashboard</button>
</div>

</body>
</html>