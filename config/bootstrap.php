<?php
// config/bootstrap.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Initialize logger
$log = new Logger('siscap');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::DEBUG));

// Make logger available globally
$GLOBALS['log'] = $log;
?>