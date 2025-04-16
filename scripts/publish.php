<?php

$auto = in_array('--auto', $argv);
$base = getcwd();

$files = [
    __DIR__ . '/../config/logger.php' => $base . '/config/logger.php',
    __DIR__ . '/../public/view-log.php' => $base . '/public/view-log.php',
];

foreach ($files as $src => $dest) {
    if (!file_exists(dirname($dest))) {
        mkdir(dirname($dest), 0755, true);
    }

    if (!file_exists($dest) || $auto) {
        copy($src, $dest);
        echo "Published: " . basename($dest) . PHP_EOL;
    } else {
        echo "Skipped (already exists): " . basename($dest) . PHP_EOL;
    }
}
