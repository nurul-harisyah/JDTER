<?php
session_start();
require 'db_connection.php';

// Initialize search term
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$articles = [];

// Check if search form was submitted
if (!empty($searchTerm)) {
    try {
        // Query to search published articles by title
        $query = "SELECT s.submission_id, m.title, m.user_id, p.published_at 
                  FROM publish_article p
                  JOIN submissions s ON p.submission_id = s.submission_id
                  JOIN metadata m ON s.submission_id = m.submission_id
                  WHERE p.status = 'Published' 
                  AND m.title LIKE :searchTerm
                  ORDER BY p.published_at DESC
                  LIMIT 5"; // Limit to 5 most recent results
        
        $stmt = $pdo->prepare($query);
        $stmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        echo '<p>Error searching articles: ' . $e->getMessage() . '</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - JDTER Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .copyright-footer {
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
        <form class="search-bar" method="GET" action="announcement.php">
            <input type="text" name="search" placeholder="Search manuscripts..." 
                   value="<?php echo htmlspecialchars($searchTerm); ?>">
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
        <?php if (!empty($searchTerm)): ?>
            <div class="search-results">
                <h3>Search Results for "<?php echo htmlspecialchars($searchTerm); ?>"</h3>
                
                <?php if (count($articles) > 0): ?>
                    <?php foreach ($articles as $article): ?>
                        <?php
                        // Get contributors for this submission
                        $contributors_query = "SELECT name FROM contributors WHERE submission_id = :submission_id";
                        $contributors_stmt = $pdo->prepare($contributors_query);
                        $contributors_stmt->execute(['submission_id' => $article['submission_id']]);

                        $contributors = [];
                        while ($contributor = $contributors_stmt->fetch(PDO::FETCH_ASSOC)) {
                            $name_parts = explode(' ', $contributor['name']);
                            $first_name = $name_parts[0];
                            $last_name = end($name_parts);
                            $formatted_name = $first_name . ' ' . strtoupper(substr($last_name, 0, 1)) . '.';
                            $contributors[] = $formatted_name;
                        }

                        // If no contributors found, use the author's name
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
                        
                        <div class="search-result-item">
                            <div class="search-result-title">
                                <a href="read_article.php?id=<?= $article['submission_id'] ?>">
                                    <?= htmlspecialchars($article['title']) ?>
                                </a>
                            </div>
                            <div class="search-result-meta">
                                <?php if (!empty($contributors)): ?>
                                    By <?= htmlspecialchars(implode(', ', $contributors)) ?> | 
                                <?php endif; ?>
                                Published: <?= htmlspecialchars($published_date) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-results">No published manuscripts found matching your search.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <section>
                <h2 style="text-align: center;">Latest Announcements</h2>
                <article>
                    <h3>Important Update on Submission Guidelines</h3>
                    <p><strong>Date:</strong> 30 March, 2025</p>
                    <p>We have updated our submission guidelines to improve the review process. Please check the new requirements before submitting your manuscript.</p>
                </article>
                <article>
                    <h3>New Review Board Members Announced</h3>
                    <p><strong>Date:</strong> March 25, 2025</p>
                    <p>We are pleased to welcome new experts to our editorial and review board. Their expertise will help enhance the quality of our journal.</p>
                </article>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <p class="copyright-footer" style="text-align: center;">&copy; 2024 JDTER Management System. All rights reserved.</p>
    </footer>
</body>
</html>