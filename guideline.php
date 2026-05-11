<?php
session_start();

// Check if the user is logged in as editor
if (!isset($_SESSION['editor'])) {
    header('Location: login.php?role=editor');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Guidelines</title>
    <link rel="stylesheet" href="css/guideline.css">
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
<div class="main-content">
    <h1>Editorial Decision Guidelines</h1>
    
    <div class="guideline-section">
        <h2>Decision Making Module</h2>
        <p>This module supports editors in making publication decisions based on reviewer assessments.</p>
        
        <div class="decision-card">
            <h3 class="accept">Accept Submission</h3>
            <ul>
                <li>Used when the submission meets all criteria</li>
                <li>Approved for publication without further changes</li>
                <li>When reviewers and editors agree the article is well-written and meets standards</li>
                <li><strong>Guideline:</strong> When both reviewers accept</li>
            </ul>
        </div>
        
        <div class="decision-card">
            <h3 class="minor-revision">Minor Revision Required</h3>
            <ul>
                <li>Article is mostly acceptable but requires minor revisions</li>
                <li>Examples: grammatical errors, formatting adjustments, small content clarifications</li>
                <li><strong>Guideline:</strong> When both reviewers suggest minor revisions OR one reviewer suggests minor revisions and another accepts.</li>
            </ul>
        </div>
        
        <div class="decision-card">
            <h3 class="major-revision">Major Revision Required</h3>
            <ul>
                <li>Article has significant issues that need addressing</li>
                <li>Examples: structural problems, insufficient data, critical content gaps</li>
                <li>Review process starts again after revisions</li>
                <li><strong>Guideline:</strong> When both reviewers request major revisions OR one requests major and another accepts</li>
            </ul>
        </div>
        
        <div class="decision-card">
            <h3 class="decline">Decline Submission</h3>
            <ul>
                <li>Reject articles that don't meet journal's scope or quality standards</li>
                <li>Used for fundamental flaws that cannot be addressed through revisions</li>
                <li>Examples: out of scope, unethical research, fundamentally flawed methodology</li>
                <li><strong>Guideline:</strong> When both reviewers suggest reject OR one reviewer suggests.</li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>