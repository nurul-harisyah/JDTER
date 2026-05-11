<?php
session_start();

// Ensure the user is logged in as a reviewer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'reviewer') {
    header('Location: login.php?role=author/reviewer');
    exit;
}

// Check if an assignment_id is provided
if (!isset($_GET['assignment_id']) || empty($_GET['assignment_id'])) {
    die("Invalid request. No assignment selected.");
}

$assignment_id = $_GET['assignment_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update assignment status to declined and set declined_at timestamp
    $query = "UPDATE reviewer_assignments SET status = 'declined', declined_at = NOW() WHERE assignment_id = :assignment_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect to evaluation page
    header("Location: evaluation_page.php?message=Review declined successfully");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decline Review Assignment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        form {
            max-width: 400px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px #ccc;
        }
        label {
            font-weight: bold;
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-top: 10px;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #dc3545;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button.cancel {
            background-color: #6c757d;
        }
        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <h2>Decline Review Assignment</h2>
    <p>Please provide a reason for declining this review assignment.</p>

    <form action="decline_review.php" method="POST">
        <input type="hidden" name="assignment_id" value="<?php echo htmlspecialchars($assignment_id); ?>">

        <label for="reason">Reason for Declining:</label>
        <textarea name="reason" id="reason" required></textarea>

        <br>
        <button type="submit">Submit</button>
        <button type="button" class="cancel" onclick="window.history.back()">Cancel</button>
    </form>
    
</body>
</html>
