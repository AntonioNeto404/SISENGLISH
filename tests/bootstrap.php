<?php
// tests/bootstrap.php
require_once __DIR__ . '/../vendor/autoload.php';
// Start session for CSRF tests
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Load CSRF helpers
require_once __DIR__ . '/../config/csrf.php';

// Load models
foreach (glob(__DIR__ . '/../models/*.php') as $modelFile) {
    require_once $modelFile;
}