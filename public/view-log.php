<?php

$config = require __DIR__ . '/../config/logger.php';
$allowedPassword = $config['password'];
$password = $_GET['password'] ?? null;
$page = max(1, (int)($_GET['page'] ?? 1));
$linesPerPage = $config['lines_per_page'] ?? 100;
$logFile = $config['log_file'];
$requiresPassword = $allowedPassword !== null;

$order = $_GET['order'] ?? 'desc'; // 'asc' or 'desc'
$filter = strtoupper($_GET['level'] ?? ''); // '', 'INFO', 'WARNING', 'ERROR'
$search = $_GET['search'] ?? '';

// Auth check
if ($requiresPassword && $password !== $allowedPassword) {
    http_response_code(403);
    echo "Access Denied";
    exit;
}

// Clear log
if (isset($_POST['clear_log'])) {
    file_put_contents($logFile, "");
    $message = "Log has been cleared.";
} else {
    $message = "";
}

// Load and filter log
$logLines = file_exists($logFile) ? file($logFile) : [];

if ($order === 'desc') {
    $logLines = array_reverse($logLines);
}

if ($filter) {
    $logLines = array_filter($logLines, fn($line) => stripos($line, "[$filter]") !== false);
}

if ($search) {
    $logLines = array_filter($logLines, fn($line) => stripos($line, $search) !== false);
}

$totalLines = count($logLines);
$totalPages = max(1, ceil($totalLines / $linesPerPage));
$page = min($page, $totalPages);

$start = ($page - 1) * $linesPerPage;
$paginatedLines = array_slice($logLines, $start, $linesPerPage);

// Color-coded output
$coloredLog = '';
foreach ($paginatedLines as $line) {
    $class = 'info';
    if (stripos($line, '[ERROR]') !== false) $class = 'error';
    elseif (stripos($line, '[WARNING]') !== false) $class = 'warning';
    $coloredLog .= '<div class="log-line ' . $class . '">' . htmlspecialchars($line) . '</div>';
}

// Rebuild query string for filters
function buildQuery($overrides = []) {
    $base = array_merge($_GET, $overrides);
    return http_build_query($base);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log Viewer</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #00ff88; padding: 20px; }
        .btn { background-color: #ff5555; border: none; color: white; padding: 10px 15px; cursor: pointer; }
        .log-line { margin: 5px 0; }
        .log-line.info { color: #00ff88; }
        .log-line.warning { color: #ffaa00; }
        .log-line.error { color: #ff4444; }
        .pagination a { color: #00ff88; margin: 0 5px; text-decoration: none; }
        .pagination span.current { font-weight: bold; color: #ffffff; }
        .filter-group { margin-bottom: 20px; }
        .filter-group input[type="text"], .filter-group select {
            padding: 5px; font-size: 14px; background: #2e2e2e; color: #00ff88; border: 1px solid #444;
        }
        .filter-group label { margin-right: 10px; }
    </style>
</head>
<body>
    <h1>Application Log</h1>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" action="?<?= http_build_query($_GET) ?>">
        <button type="submit" name="clear_log" class="btn">Clear Log</button>
    </form>

    <form method="GET" class="filter-group">
        <input type="hidden" name="password" value="<?= htmlspecialchars($password) ?>">

        <label>
            Order:
            <select name="order" onchange="this.form.submit()">
                <option value="desc" <?= $order === 'desc' ? 'selected' : '' ?>>Newest First</option>
                <option value="asc" <?= $order === 'asc' ? 'selected' : '' ?>>Oldest First</option>
            </select>
        </label>

        <label>
            Level:
            <select name="level" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="INFO" <?= $filter === 'INFO' ? 'selected' : '' ?>>INFO</option>
                <option value="WARNING" <?= $filter === 'WARNING' ? 'selected' : '' ?>>WARNING</option>
                <option value="ERROR" <?= $filter === 'ERROR' ? 'selected' : '' ?>>ERROR</option>
            </select>
        </label>

        <label>
            Search:
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search..." />
        </label>
        <button type="submit" class="btn">Apply</button>
    </form>

    <div class="pagination">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p == $page): ?>
                <span class="current"><?= $p ?></span>
            <?php else: ?>
                <a href="?<?= buildQuery(['page' => $p]) ?>"><?= $p ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>

    <pre><?= $coloredLog ?></pre>
</body>
</html>
