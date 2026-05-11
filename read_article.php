<?php
session_start();
require 'db_connection.php';

if (!isset($_GET['id'])) {
    header("Location: current.php");
    exit;
}

$submission_id = $_GET['id'];

// Fetch metadata (title, abstract)
$meta_stmt = $pdo->prepare("SELECT title, abstract FROM metadata WHERE submission_id = ?");
$meta_stmt->execute([$submission_id]);
$metadata = $meta_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch contributor names
$contrib_stmt = $pdo->prepare("SELECT name FROM contributors WHERE submission_id = ?");
$contrib_stmt->execute([$submission_id]);
$contributors = $contrib_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch file data including file_id
$file_stmt = $pdo->prepare("SELECT file_id, uploaded_at, file_path, file_name FROM submission_files WHERE submission_id = ? ORDER BY uploaded_at ASC LIMIT 1");
$file_stmt->execute([$submission_id]);
$file_data = $file_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch publish date
$publish_stmt = $pdo->prepare("SELECT published_at FROM publish_article WHERE submission_id = ?");
$publish_stmt->execute([$submission_id]);
$publish_data = $publish_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($metadata['title'] ?? 'Article') ?> - JDTER</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
            color: black;
        }

        header {
            background-color: #1e3a8a !important;
            color: #fff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        nav {
            margin-top: 10px;
        }

        .main-links,
        .auth-links {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .main-links li,
        .auth-links li {
            display: inline-block;
            margin-right: 15px;
        }

        .main-links a,
        .auth-links a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .main-links a:hover,
        .auth-links a:hover {
            background-color: burlywood;
        }

        .search-bar {
            display: flex;
            align-items: center;
        }

        .search-bar input {
            padding: 5px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-bar button {
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 5px;
            background-color: gray;
            color: #1e3a8a;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: burlywood;
            color: #fff;
        }

        .auth-links ul {
            display: none;
            position: absolute;
            background-color: #fff;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
            padding: 10px;
            list-style-type: none;
        }

        .auth-links li:hover ul {
            display: block;
        }

        .auth-links ul li a {
            color: #1e3a8a;
            padding: 8px 15px;
            display: block;
            text-decoration: none;
        }

        .auth-links ul li a:hover {
            background-color: burlywood;
            color: #fff;
        }

        main {
            padding: 20px;
        }

        h2 {
            color: #1e3a8a;
            margin-bottom: 20px;
            text-align: center;
        }

        footer p {
            font-size: 12px !important;
            text-align: center;
            color: white !important;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #1e3a8a;
            padding: 20px 0;
            border-top: 1px solid #eee;
        }

        .article-details {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: lightgray;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .article-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #000;
            text-decoration: none;
            cursor: pointer;
            line-height: 2.5;
            margin-bottom: 15px;
            text-align: left;
        }

        .authors-list {
            color: #000;
            font-size: 1rem;
            margin-bottom: 15px;
            font-style: italic;
        }

        .dates {
            color: #000;
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .dates p {
            margin: 5px 0;
        }

        .abstract-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 25px;
            border: 1px solid #e0e0e0;
        }

        .abstract-section h3 {
            color: #1e3a8a;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .abstract-section p {
            line-height: 1.6;
            color: #333;
        }

        .download-btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #1e3a8a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .download-btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .download-btn i {
            margin-right: 8px;
        }

        .no-file,
        .error {
            color: #dc3545;
            font-style: italic;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .article-details {
                padding: 15px;
                margin: 15px;
            }

            .article-title {
                font-size: 24px;
            }

            .abstract-section {
                padding: 15px;
            }

            .download-btn {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body class="index-page">
    <header>
        <h1 style="margin: 0; font-size: 20px;">JDTER Management System</h1>
        <nav>
            <ul class="main-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="announcement.php">Announcements</a></li>
                <li><a href="current.php">Current</a></li>
                <li><a href="archives.php">Archives</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </nav>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <button>Search</button>
        </div>
        <ul class="auth-links">
            <li>
                <a href="#">Login</a>
                <ul>
                    <li><a href="login.php?role=author/reviewer">Author/Reviewer</a></li>
                    <li><a href="login.php?role=editor">Editor</a></li>
                </ul>
            </li>
            <li><a href="register.php">Register</a></li>
        </ul>
    </header>

    <main>
        <section class="article-details">
            <?php if ($metadata): ?>
                <h2 class="article-title"><?= htmlspecialchars($metadata['title']) ?></h2>

                <div class="authors-list">
                    <?php
                    $formatted_names = [];
                    foreach ($contributors as $contributor) {
                        // Split the name into first name and last name
                        $name_parts = explode(' ', $contributor['name']);
                        $first_name = $name_parts[0];
                        $last_name = end($name_parts); // Get the last part as last name
                        $formatted_name = $first_name . ' ' . strtoupper(substr($last_name, 0, 1)) . '.'; // Format as "First Last."
                        $formatted_names[] = htmlspecialchars($formatted_name);
                    }
                    echo implode(', ', $formatted_names);
                    ?>
                </div>

                <div class="dates">
                    <p><strong>Submitted Date:</strong> <?= date('F j, Y', strtotime($file_data['uploaded_at'])) ?></p>
                    <p><strong>Published Date:</strong> <?= date('F j, Y', strtotime($publish_data['published_at'])) ?></p>
                </div>

                <div class="abstract-section">
                    <h3>Abstract</h3>
                    <p><?= nl2br(htmlspecialchars($metadata['abstract'])) ?></p>
                </div>

                <?php if (!empty($file_data['file_path'])): ?>
                    <a href="download.php?file_id=<?= $file_data['file_id'] ?>" class="download-btn">
                        <i class="fa fa-download" style="margin-right: 8px;"></i>Download
                    </a>
                <?php else: ?>
                    <p class="no-file">No manuscript file available.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="error">Article details not found.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 JDTER Management System. All rights reserved.</p>
    </footer>
</body>

</html>