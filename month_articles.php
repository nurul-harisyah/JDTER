<?php
session_start();
require 'db_connection.php';

if (!isset($_GET['month'])) {
    header("Location: archives.php");
    exit();
}

$monthYear = $_GET['month'];
$date = DateTime::createFromFormat('Y-m', $monthYear);
$monthName = $date->format('F');
$year = $date->format('Y');


$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';


$query = "SELECT pa.submission_id, m.title, m.user_id, pa.published_at 
          FROM publish_article pa 
          JOIN metadata m ON pa.submission_id = m.submission_id 
          WHERE pa.status = 'Published' 
          AND DATE_FORMAT(pa.published_at, '%Y-%m') = :monthYear";


if (!empty($searchTerm)) {
    $query .= " AND m.title LIKE :searchTerm";
}

$query .= " ORDER BY pa.published_at DESC";

$stmt = $pdo->prepare($query);
$params = [':monthYear' => $monthYear];

if (!empty($searchTerm)) {
    $params[':searchTerm'] = '%' . $searchTerm . '%';
}

$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($monthName . ' ' . $year) ?> Articles - JDTER Management System</title>
    <link rel="stylesheet" href="styles.css">
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .month-header {
            color: #1e3a8a;
            text-align: left;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1e3a8a;
        }

        .article-box {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            transition: box-shadow 0.3s ease;
        }

        .article-box:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .article-title {
            text-align: left;
            font-size: 1.3rem;
            font-weight: bold;
            color: #000;
            text-decoration: none;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .article-title a {
            text-align: left;
            color: inherit;
            text-decoration: none;
        }

        .article-title a:hover {
            text-decoration: underline;
        }

        .article-authors {
            text-align: left;
            font-style: italic;
            margin-bottom: 8px;
            color: #555;
        }

        .article-published {
            color: #777;
            font-size: 0.9rem;
            text-align: left;
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
    </style>
</head>
<body>
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
        <form class="search-bar" method="GET" action="month_articles.php">
            <input type="hidden" name="month" value="<?= htmlspecialchars($monthYear) ?>">
            <input type="text" name="search" placeholder="Search titles..." 
                   value="<?= htmlspecialchars($searchTerm) ?>">
            <button type="submit">Search</button>
        </form>
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
        <h1 class="month-header"><?= htmlspecialchars($monthName . ' ' . $year) ?></h1>
        
        <?php if (!empty($searchTerm)): ?>
            <div class="search-results-info">
                Showing results for: <strong><?= htmlspecialchars($searchTerm) ?></strong>
            </div>
        <?php endif; ?>
        
        <?php if (count($articles) > 0): ?>
            <?php foreach ($articles as $article): ?>
                <?php
                // Get contributors for this submission
                $contributors_query = "SELECT name FROM contributors WHERE submission_id = :submission_id";
                $contributors_stmt = $pdo->prepare($contributors_query);
                $contributors_stmt->execute(['submission_id' => $article['submission_id']]);

                $contributors = [];
                while ($contributor = $contributors_stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Split the name into first name and last name
                    $name_parts = explode(' ', $contributor['name']);
                    $first_name = $name_parts[0];
                    $last_name = end($name_parts);
                    $formatted_name = $first_name . ' ' . strtoupper(substr($last_name, 0, 1)) . '.';
                    $contributors[] = $formatted_name;
                }

                // If no contributors found, use the author's name from users table
                if (empty($contributors)) {
                    $author_query = "SELECT full_name FROM users WHERE id = :user_id";
                    $author_stmt = $pdo->prepare($author_query);
                    $author_stmt->execute(['user_id' => $article['user_id']]);
                    $author = $author_stmt->fetch(PDO::FETCH_ASSOC);
                    if ($author) {
                        $author_parts = explode(' ', $author['full_name']);
                        $first_name = $author_parts[0];
                        $last_name = end($author_parts);
                        $formatted_author_name = $first_name . ' ' . strtoupper(substr($last_name, 0, 1)) . '.';
                        $contributors[] = $formatted_author_name;
                    }
                }

                // Format date
                $published_date = date('F j, Y', strtotime($article['published_at']));
                ?>
                
                <div class="article-box">
                    <div class="article-title"><a href="read_article.php?id=<?= $article['submission_id'] ?>"><?= htmlspecialchars($article['title']) ?></a></div>
                    <?php if (!empty($contributors)): ?>
                        <div class="article-authors"><?= htmlspecialchars(implode(', ', $contributors)) ?></div>
                    <?php endif; ?>
                    <div class="article-published">Published: <?= htmlspecialchars($published_date) ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>
                <?php if (!empty($searchTerm)): ?>
                    No articles found matching "<?= htmlspecialchars($searchTerm) ?>" in <?= htmlspecialchars($monthName . ' ' . $year) ?>.
                <?php else: ?>
                    No Manuscript found for this month.
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </main>

    <footer>
        <p style="text-align: center;">&copy; 2024 JDTER Management System. All rights reserved.</p>
    </footer>
</body>
</html>