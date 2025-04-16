<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TijaniDevIt\Logger\Logger;

function assertTrue($condition, $message = '') {
    if (!$condition) {
        echo "❌ FAIL: $message" . PHP_EOL;
    } else {
        echo "✅ PASS: $message" . PHP_EOL;
    }
}

// Setup
$logPath = __DIR__ . '/test.log';
if (file_exists($logPath)) {
    unlink($logPath);
}

// Instantiate logger
$logger = new Logger($logPath);

// Test writing logs
$logger->info("This is an info message.");
$logger->warning("This is a warning.");
$logger->error("This is an error.");

// Read log
$logContent = file_get_contents($logPath);

assertTrue(strpos($logContent, 'INFO') !== false, "Info log was written");
assertTrue(strpos($logContent, 'WARNING') !== false, "Warning log was written");
assertTrue(strpos($logContent, 'ERROR') !== false, "Error log was written");
assertTrue(substr_count($logContent, "\n") === 3, "Log has 3 entries");

// Clean up
unlink($logPath);
