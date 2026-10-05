<?php
// Start session to read the stored data
session_start();

// Retrieve data from session and clean them up so they don't persist forever
$query         = $_SESSION['searchQuery'] ?? '';
$results       = $_SESSION['searchResults'] ?? [];
$errorMessage = $_SESSION['searchError'] ?? null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="results-container">
        <header class="results-header">
            <a href="index.html" class="back-link">&larr; Back to Main page</a>
            <h1>Search Results</h1>
            <p class="search-query-info">
                Query: <strong>"<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>"</strong>
            </p>
        </header>

        <main class="results-list">
            <?php if (!empty($errorMessage)): ?>
                <div class="warning-text">
                    <strong>Serper API Error:</strong> <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php elseif (!empty($results)): ?>

            <div class="download-container">
                <form action="scripts/download-content.php" method="GET">
                <span>Download results: </span>
                <button type="submit" name="downloadFormat" value="json" class="download-btn">JSON</button>
                <button type="submit" name="downloadFormat" value="xml" class="download-btn">XML</button>
                <button type="submit" name="downloadFormat" value="md" class="download-btn">Markdown</button>
                </form>
            </div>

                <?php foreach ($results as $item): ?>
                    <div class="result-card">
                        <span class="result-url"><?= htmlspecialchars($item['link'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h2 class="result-title">
                            <a href="<?= htmlspecialchars($item['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                                <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </h2>
                        <p class="result-snippet" style="font-size: 14px; color: #4d5156; margin-top: 6px; line-height: 1.4;">
                            <?= htmlspecialchars($item['snippet'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="warning-text">No results found for your query.</p>
            <?php endif; ?>
        </main>
    </div>

</body>
</html>