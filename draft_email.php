<?php
session_start();

if (!isset($_SESSION['editor'])) {
    exit('Access denied: You must be logged in as an editor.');
}

$draft = $_SESSION['draft_email'] ?? null;

if (!$draft) {
    echo "No draft email found.";
    exit;
}

$submission_id = $draft['submission_id'];
$decision_type = $draft['decision_type'];
$comments = $draft['comments'];

// Connect to database (please update username, password, host if needed)
$mysqli = new mysqli("localhost", "root", "", "journal");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch manuscript title from metadata table
$stmt = $mysqli->prepare("SELECT title FROM metadata WHERE submission_id = ?");
$stmt->bind_param("i", $submission_id);
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();
$mysqli->close();

// Handle case if title not found
if (!$title) {
    $title = "Untitled Manuscript";
}

// Generate email content
$subject = "Editorial Decision for Manuscript ID: $submission_id\n\nManuscript Title: \"$title\"";
$body = "Dear Author(s),\n\nThank you for submitting your manuscript titled \"$title\" to the The Journal of Digital Expert Review (JDTER).\nWe have completed the review process for your submission (ID: $submission_id).\n\nAfter careful consideration by our editorial team, your manuscript have been " . ucfirst(str_replace('_', ' ', $decision_type))."\n\nThank you for submitting your work to The Journal of Digital Expert Review (JDTER). We wish you the best of success with your future research endeavors and look forward to the possibility of recieving submissions from you in the future.\n\nKindly be informed that, if your manuscript has been accepted for publication, you are requested to complete the publication fee payment in accordance with the journal's guidelines. Further payment instructions will be provided below:

    \n\n-Payment Information-
\nBank Name:----------
\nAccount Number:**************

\nIf your manuscript has not been accepted, no further action is required, and you may disregard this message. \n\nBest regards,\nEditorial Team";
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Draft Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        textarea,
        input {
            width: 100%;
            padding: 20px;
            margin-top: 10px;
        }

        button {
            padding: 10px 15px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <h2>Edit Draft Email</h2>

    <form method="POST" action="send_decision_email.php">
        <input type="hidden" name="submission_id" value="<?= htmlspecialchars($submission_id) ?>">

        <label>Email Subject:</label>
        <textarea name="email_subject" rows="2"><?= htmlspecialchars($subject) ?></textarea>

        <label>Email Body:</label>
        <textarea name="email_body" rows="10"><?= htmlspecialchars($body) ?></textarea>

        <button type="submit">Send Final Decision</button>
    </form>
</body>

</html>