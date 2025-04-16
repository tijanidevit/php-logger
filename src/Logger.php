<?php

namespace TijaniDevIt\Logger;

class Logger
{
    protected $logFile;

    public function __construct($filename = null)
    {
        $config = require __DIR__ . '/../config/logger.php';
        $this->logFile = $filename ?: $config['log_file'];
    }

    public function log($message, $level = 'INFO')
    {
        $time = date('Y-m-d H:i:s');
        $entry = "[$time] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    public function info($message)
    {
        $this->log($message, 'INFO');
    }

    public function warning($message)
    {
        $this->log($message, 'WARNING');
    }

    public function error($message)
    {
        $this->log($message, 'ERROR');
    }
}
