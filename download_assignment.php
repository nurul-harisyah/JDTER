<?php
session_start();
require('db_connection.php');

// Ensure the user is logged in as a reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

$user_id = $_SESSION['user']['id']; 

// Fetch assigned manuscripts where the reviewer has accepted the review
$query = "SELECT m.submission_id, m.title, m.abstract, sf.file_name, sf.file_path, 
                 DATE_ADD(ra.accepted_at, INTERVAL 3 WEEK) AS due_date
          FROM reviewer_assignments ra
          JOIN submissions s ON ra.submission_id = s.submission_id
          JOIN metadata m ON s.submission_id = m.submission_id
          LEFT JOIN submission_files sf ON s.submission_id = sf.submission_id
          WHERE ra.reviewer_id = :user_id AND ra.status = 'accepted'";

$stmt = $pdo->prepare($query);
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Your Assigned Manuscripts</title>
    <style>
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
    color: #333;
}

/* Sidebar Styles (consistent with other reviewer pages) */
.sidebar {
    width: 300px;
    background-color: rgb(42, 42, 101);
    color: #fff;
    height: 100vh;
    position: fixed;
    padding: 20px 10px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.sidebar h1 {
    font-size: 1.5em;
    margin-bottom: 20px;
    text-align: center;
    color: white;
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
    margin-top: 20px;
}

.sidebar ul li {
    margin: 15px 0;
}

.sidebar ul li button {
    background-color: rgb(42, 42, 101);
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    text-align: left;
    width: 100%;
    transition: background-color 0.3s;
}

.sidebar ul li button:hover {
    background-color: darkblue;
}

.review-dash button{
    background-color: rgb(42, 42, 101); 
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    text-align: left;
    width: 100%;
}

.review-dash button:hover {
    background-color: darkblue; 
}
/* Main Content Styles */
.main-content {
    margin-left: 400px;
    padding: 30px;
    width: fit-content;
}

h2 {
    color: rgb(42, 42, 101);
    margin-left: 0;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #ddd;
}

/* Table Styles */
table {
    width: fit-content;
    border-collapse: collapse;
    margin-top: 20px;
    margin-left: 0;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 10px 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: rgb(42, 42, 101);
    color: white;
    font-weight: bold;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}

/* Button and Link Styles */
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

.view-btn:hover {
    background-color: #45a049;
}

a[download] {
    display: inline-block;
    background-color: rgb(42, 42, 101);
    color: white;
    padding: 6px 12px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    transition: background-color 0.3s;
}

a[download]:hover {
    background-color: darkblue;
}

/* Status Styles */
.not-available {
    color: #777;
    font-style: italic;
}

/* Message Styles */
.no-assignments {
    text-align: center;
    padding: 15px;
    color: #777;
    font-style: italic;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }
    
    .main-content {
        margin-left: 0;
        width: 100%;
        padding: 15px;
    }
    
    table {
        display: block;
        overflow-x: auto;
    }
    
    th, td {
        padding: 8px 10px;
    }
}

/* Abstract Column Specific Styles */
td:nth-child(3) {
    max-width: 250px;
    word-wrap: break-word;
}
    </style>
    
</head>
<body>
<div class="sidebar">
        <div class="review-dash"><button onclick="window.location.href='reviewer_dashboard.php'"><h1>Reviewer Dashboard</h1></button>
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
    <h2>Download Your Assigned Manuscripts</h2>
    <table>
        <tr>
            <th>Manuscript ID</th>
            <th>Title</th>
            <th>Abstract</th>
            <th>Due Date</th>
            <th>Download</th>
        </tr>
        <?php if (empty($assignments)): ?>
            <tr><td colspan="5">No assigned manuscripts found.</td></tr>
        <?php else: ?>
            <?php foreach ($assignments as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['submission_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td>
                        <?php if (!empty($row['abstract'])): ?>
                            <a href="view_abstract_dw.php?assignment_id=<?php echo $row['submission_id']; ?>" class="view-btn">
    View Abstract
</a>

                        <?php else: ?>
                            <span class="not-available">Abstract Not Found</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date("Y-m-d", strtotime($row['due_date'])); ?></td>
                    <td>
                        <?php
                        if (!empty($row['file_path']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/manuscript_submission/" . basename($row['file_path']))) {
                            $file_url = "/uploads/manuscript_submission/" . basename($row['file_path']);
                            echo "<a href='$file_url' download>Download</a>";
                        } else {
                            echo "<span class='not-available'>File not available</span>";
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
    </div>
</body>
</html>
