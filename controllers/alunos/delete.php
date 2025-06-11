<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/csrf.php';

// Validate CSRF
csrf_validate();

// Check if user is logged in and is admin
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

if($_SESSION['tipo'] != 'ADMINISTRADOR') {
    header("Location: ../../views/dashboard.php");
    exit();
}

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/estudante.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../../views/expiration.php");
    exit();
}

// Get ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : die("Erro: ID do estudante não especificado.");

// Initialize Estudante object
$estudante = new Estudante($db);
$estudante->id = $id;

// Delete estudante
if($estudante->delete()) {
    $GLOBALS['log']->info("Estudante excluído: id={$id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['message'] = "Estudante excluído com sucesso.";
} else {
    $GLOBALS['log']->error("Falha ao excluir estudante: id={$id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['error_message'] = "Erro ao excluir estudante.";
}

// Redirect to list
header("Location: ../../views/estudantes/index.php");
exit();