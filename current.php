<?php
session_start();
require_once 'db_connection.php';

if (!$pdo) {
    die("Database connection not established");
}

$searchTerm = '';
$articles = [];

// Check if search form was submitted
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = trim($_GET['search']);
    
    try {
        $query = "SELECT s.submission_id, m.title, m.user_id, p.published_at 
                  FROM publish_article p
                  JOIN submissions s ON p.submission_id = s.submission_id
                  JOIN metadata m ON s.submission_id = m.submission_id
                  WHERE p.status = 'Published' 
                  AND m.title LIKE :searchTerm
                  ORDER BY p.published_at DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        echo '<p>Error searching articles: ' . $e->getMessage() . '</p>';
    }
} else {
    try {
        // Default query to get all published articles if no search term
        $query = "SELECT s.submission_id, m.title, m.user_id, p.published_at 
                  FROM publish_article p
                  JOIN submissions s ON p.submission_id = s.submission_id
                  JOIN metadata m ON s.submission_id = m.submission_id
                  WHERE p.status = 'Published'
                  ORDER BY p.published_at DESC";
        
        $stmt = $pdo->query($query);
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        echo '<p>Error retrieving articles: ' . $e->getMessage() . '</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Issue - JDTER Management System</title>
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

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: center;
            }

            .main-links li,
            .auth-links li {
                display: block;
                margin-bottom: 10px;
            }

            .search-bar {
                margin: 15px auto;
            }
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

        .download-icon {
            float: right;
            font-size: 20px;
            color: #555;
            text-decoration: none;
        }

        .download-icon:hover {
            color: #000;
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
            <li><a class="active" href="archives.php">Archives</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>
    <form class="search-bar" method="GET" action="current.php">
        <input type="text" name="search" placeholder="Search by title..." value="<?php echo htmlspecialchars($searchTerm); ?>">
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
    <section>
        <h2 style="text-align: center;">Current Issue</h2>
        <p style="text-align: center;">Explore the latest research and scholarly manuscripts published in our journal.
            Stay updated with groundbreaking studies and academic insights.</p>

        <div class="current-issue">
            <h3>Published Manuscripts</h3>
            
            <?php if (!empty($searchTerm)): ?>
                <p>Showing results for: <strong><?php echo htmlspecialchars($searchTerm); ?></strong></p>
            <?php endif; ?>

            <?php
            if (count($articles) > 0) {
                foreach ($articles as $row) {
                    // Get contributors for this submission
                    $contributors_query = "SELECT name FROM contributors WHERE submission_id = :submission_id";
                    $contributors_stmt = $pdo->prepare($contributors_query);
                    $contributors_stmt->execute(['submission_id' => $row['submission_id']]);

                    $contributors = [];
                    while ($contributor = $contributors_stmt->fetch(PDO::FETCH_ASSOC)) {
                        // Split the name into first name and last name
                        $name_parts = explode(' ', $contributor['name']);
                        $first_name = $name_parts[0];
                        $last_name = end($name_parts); // Get the last part as last name
                        $formatted_name = $first_name . ' ' . strtoupper(substr($last_name, 0, 1)) . '.'; // Format as "First Last."
                        $contributors[] = $formatted_name;
                    }

                    // If no contributors found, use the author's name from users table
                    if (empty($contributors)) {
                        $author_query = "SELECT full_name FROM users WHERE id = :user_id";
                        $author_stmt = $pdo->prepare($author_query);
                        $author_stmt->execute(['user_id' => $row['user_id']]);
                        $author = $author_stmt->fetch(PDO::FETCH_ASSOC);
                        // Split the author's name and format
                        $author_parts = explode(' ', $author['full_name']);
                        $first_name = $author_parts[0];
                        $last_name = end($author_parts);
                        $formatted_author_name = $first_name . ' ' . strtoupper(substr($last_name, 0, 1)) . '.';
                        $contributors[] = $formatted_author_name;
                    }

                    // Format date
                    $published_date = date('F j, Y', strtotime($row['published_at']));

                    echo '<div class="article-box">';
                    echo '<div class="article-title"><a href="read_article.php?id=' . $row['submission_id'] . '">' . $row['title'] . '</a></div>';
                    echo '<div class="article-authors">' . implode(', ', $contributors) . '</div>';
                    echo '<div class="article-published">published: ' . $published_date . '</div>';
                    echo '</div>';
                }
            } else {
                if (!empty($searchTerm)) {
                    echo '<p>No published articles found matching your search.</p>';
                } else {
                    echo '<p>No published articles found.</p>';
                }
            }
            ?>
        </div>
    </section>
</main>

<footer>
    <p class="copyright-footer" style="text-align: center;">&copy; 2024 JDTER Management System. All rights
        reserved.</p>
</footer>
</body>
</html>