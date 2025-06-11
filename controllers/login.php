<?php
session_start();

// CSRF token validation
require_once __DIR__ . '/../config/csrf.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
}

// Include database and object files
include_once '../config/database.php';
include_once '../models/user.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../views/expiration.php");
    exit();
}

// Initialize user object
$user = new User($db);

// Set user credentials from form with sanitization
$user->cognome = filter_input(INPUT_POST, 'cognome', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$user->senha = filter_input(INPUT_POST, 'senha', FILTER_UNSAFE_RAW) ?: '';

// Attempt login
if($user->login()) {
    // Create session variables
    $_SESSION['user_id'] = $user->id;
    $_SESSION['cognome'] = $user->cognome;
    $_SESSION['nome'] = $user->nome;
    $_SESSION['tipo'] = $user->tipo;
    
    // Redirect to dashboard
    header("Location: ../views/dashboard.php");
    exit();
} else {
    // Set error message and redirect back to login page
    $_SESSION['error_message'] = "Usuário ou senha incorretos.";
    header("Location: ../index.php");
    exit();
}
?>
