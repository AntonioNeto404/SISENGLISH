<?php
// config/csrf.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Generates CSRF token if not set
function csrf_generate_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validates CSRF token in POST requests
function csrf_validate() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            // Invalid CSRF token
            http_response_code(400);
            die('Invalid CSRF token.');
        }
    }
}

// Returns HTML input element for CSRF token
function csrf_input() {
    $token = htmlspecialchars(csrf_generate_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}
?>