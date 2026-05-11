<?php
session_start();

// Include database connection
require('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Fetch the current user's data
$user = $_SESSION['user'];
$user_id = $user['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    echo "User not found.";
    exit;
}

// Handle form submission for profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $country = $_POST['country'];
    $role = $_POST['role']; // Role from dropdown

    // Update the user's profile in the database
    $updateStmt = $pdo->prepare(
        "UPDATE users SET full_name = ?, email = ?, username = ?, country = ?, role = ? WHERE id = ?"
    );
    $updateStmt->execute([$full_name, $email, $username, $country, $role, $user_id]);

    // Update the session data
    $_SESSION['user']['full_name'] = $full_name;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['country'] = $country;
    $_SESSION['user']['role'] = $role;

    // Redirect to refresh data
    header('Location: myProfile.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="profileMy-page">

<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php">
                <h2>Dashboard</h2>
            </a>
        </div>

        <a href="#" onclick="toggleSubmenu('new_submission')">New Submission</a>
        <ul id="new_submission_menu" style="display: none;">
            <li><a href="start.php">1. Start</a></li>
            <li><a href="upload_submission.php">2. Upload Manuscript</a></li>
            <li><a href="enter_metadata.php">3. Enter Metadata</a></li>
            <li><a href="confirmation.php">4. Confirmation</a></li>
        </ul>
        <a href="my_submission.php">My Submission</a>
        <a href="status_change.php">Notification</a>
        <a href="payment.php">Payment</a>
        <a href="myProfile.php">My Profile</a>
        <a href="guideline_author.php">Guideline Author</a>
        <?php if ($userData['role'] === 'reviewer'): ?>
         
            <a href="reviewer_dashboard.php">Reviewer Section</a>
          
        <?php endif; ?>

        <a href="index.php">Logout</a>
    </div>

    <div id="my_profile" class="section">
        <div class="profile-container">
            <h1>My Profile</h1>
            <form method="POST">
                <table border="1">
                    <tr>
                        <th>Field</th>
                        <th>Value</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <th>Full Name</th>
                        <td>
                            <span id="full_name_display"><?= htmlspecialchars($userData['full_name']) ?></span>
                            <input type="text" id="full_name_input" name="full_name" value="<?= htmlspecialchars($userData['full_name']) ?>" style="display:none;" required>
                        </td>
                        <td>
                            <button type="button" onclick="toggleEdit('full_name')">Edit</button>
                            <button type="submit" style="display:none;" id="save_full_name">Save</button>
                        </td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>
                            <span id="email_display"><?= htmlspecialchars($userData['email']) ?></span>
                            <input type="email" id="email_input" name="email" value="<?= htmlspecialchars($userData['email']) ?>" style="display:none;" required>
                        </td>
                        <td>
                            <button type="button" onclick="toggleEdit('email')">Edit</button>
                            <button type="submit" style="display:none;" id="save_email">Save</button>
                        </td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td>
                            <span id="username_display"><?= htmlspecialchars($userData['username']) ?></span>
                            <input type="text" id="username_input" name="username" value="<?= htmlspecialchars($userData['username']) ?>" style="display:none;" required>
                        </td>
                        <td>
                            <button type="button" onclick="toggleEdit('username')">Edit</button>
                            <button type="submit" style="display:none;" id="save_username">Save</button>
                        </td>
                    </tr>
                    <tr>
                        <th>Country</th>
                        <td>
                            <span id="country_display"><?= htmlspecialchars($userData['country']) ?></span>
                            <input type="text" id="country_input" name="country" value="<?= htmlspecialchars($userData['country']) ?>" style="display:none;" required>
                        </td>
                        <td>
                            <button type="button" onclick="toggleEdit('country')">Edit</button>
                            <button type="submit" style="display:none;" id="save_country">Save</button>
                        </td>
                    </tr>
                    <!-- Role Selection -->
                    <tr>
                        <th>Role</th>
                        <td>
                            <select name="role" required>
                                <option value="author" <?= $userData['role'] === 'author' ? 'selected' : '' ?>>Author</option>
                                <option value="reviewer" <?= $userData['role'] === 'reviewer' ? 'selected' : '' ?>>Reviewer</option>
                            </select>
                        </td>
                        <td>
                            <button type="submit">Save</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleEdit(field) {
        const displaySpan = document.getElementById(field + "_display");
        const inputField = document.getElementById(field + "_input");
        const saveButton = document.getElementById("save_" + field);

        if (displaySpan.style.display === "none") {
            displaySpan.style.display = "block";
            inputField.style.display = "none";
            saveButton.style.display = "none";
        } else {
            displaySpan.style.display = "none";
            inputField.style.display = "inline";
            saveButton.style.display = "inline";
        }
    }

    function toggleSubmenu(menuId) {
        const menu = document.getElementById(menuId + '_menu');
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }
</script>

</body>
</html>
