<?php
session_start();
require 'db_connection.php';

// Initialize search year
$searchYear = isset($_GET['year']) ? trim($_GET['year']) : '';

// Fetch published articles from the publish_article table with optional year filter
$query = "SELECT pa.publish_id, pa.submission_id, pa.status, pa.published_at, 
          m.title FROM publish_article pa 
          JOIN metadata m ON pa.submission_id = m.submission_id 
          WHERE pa.status = 'Published'";

// Add year filter if specified
if (!empty($searchYear) && is_numeric($searchYear)) {
    $query .= " AND YEAR(pa.published_at) = :year";
}

$query .= " ORDER BY pa.published_at DESC";

$stmt = $pdo->prepare($query);

if (!empty($searchYear) && is_numeric($searchYear)) {
    $stmt->bindParam(':year', $searchYear, PDO::PARAM_INT);
}

$stmt->execute();
$archives = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group articles by month and year and assign volume numbers
$groupedArchives = [];
$volumeCounter = 1;
$previousYearMonth = '';

foreach ($archives as $archive) {
    $date = new DateTime($archive['published_at']);
    $year = $date->format('Y');
    $month = $date->format('F');
    $yearMonth = $month . ' ' . $year;
    $monthYearKey = $date->format('Y-m');

    if ($yearMonth !== $previousYearMonth) {
        if ($previousYearMonth !== '') {
            $volumeCounter++;
        }
        $previousYearMonth = $yearMonth;
    }

    if (!isset($groupedArchives[$monthYearKey])) {
        $groupedArchives[$monthYearKey] = [
            'month_name' => $month,
            'year' => $year,
            'volume' => 'Vol ' . $volumeCounter,
            'article_count' => 0,
            'submission_ids' => []
        ];
    }

    $groupedArchives[$monthYearKey]['article_count']++;
    $groupedArchives[$monthYearKey]['submission_ids'][] = $archive['submission_id'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives - JDTER Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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

        .archive-group {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background-color: #f9f9f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .archive-group:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .archive-info {
            flex-grow: 1;
        }

        .archive-title {
            text-align: left;
            color: #1e3a8a;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .archive-meta {
            text-align: left;
            color: #666;
            font-size: 0.9rem;
        }

        .read-month-btn {
            background-color: #1e3a8a;
            color: white;
            padding: 10px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
            white-space: nowrap;
        }

        .read-month-btn:hover {
            background-color: #1e40af;
        }

        .archives-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 60px;
        }

        .no-archives {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }

        .breadcrumb {
            margin-bottom: 20px;
        }

        .search-bar input[type="number"],
        .search-bar input[type="text"] {
            padding: 5px 5px;
            width: 170px;
            border-radius: 6px;
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
        <form class="search-bar" method="GET" action="archives.php">
            <input type="number" name="year" placeholder="Search by year..."
                value="<?php echo htmlspecialchars($searchYear); ?>" min="0000" max="<?php echo date('Y'); ?>">
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
        <div class="breadcrumb">
            <a href="index.php">Home</a> / <a class="active" href="archives.php">Archives</a>
        </div>

        <?php if (!empty($searchYear)): ?>
            <div class="search-results-info">
                Showing archives for year: <strong><?php echo htmlspecialchars($searchYear); ?></strong>
            </div>
        <?php endif; ?>

        <div class="archives-container">
            <?php if (count($archives) > 0): ?>
                <?php foreach ($groupedArchives as $monthYearKey => $archiveGroup): ?>
                    <div class="archive-group">
                        <div class="archive-info">
                            <div class="archive-title">
                                <?= htmlspecialchars($archiveGroup['month_name'] . ' ' . $archiveGroup['year']) ?></div>
                            <div class="archive-meta">
                                <?= htmlspecialchars($archiveGroup['volume']) ?> •
                                <?= $archiveGroup['article_count'] ?>
                                manuscript<?= $archiveGroup['article_count'] > 1 ? 's' : '' ?>
                            </div>
                        </div>
                        <a href="month_articles.php?month=<?= $monthYearKey ?>" class="read-month-btn">Read</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-archives">
                    <?php if (!empty($searchYear)): ?>
                        No archives found for year <?php echo htmlspecialchars($searchYear); ?>.
                    <?php else: ?>
                        No published manuscript available.
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p style="text-align: center;">&copy; 2024 JDTER Management System. All rights reserved.</p>
    </footer>
</body>

</html>