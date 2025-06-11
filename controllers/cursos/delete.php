<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] != 'ADMINISTRADOR') {
    header("Location: ../../index.php");
    exit();
}

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/curso.php';
require_once __DIR__ . '/../../config/csrf.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if ($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

// Validate CSRF
csrf_validate();

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete course
    $curso = new Curso($db);
    $curso->id = $id;

    if ($curso->delete()) {
        $_SESSION['message'] = 'Curso excluído com sucesso!';
    } else {
        $_SESSION['error_message'] = 'Erro ao excluir o curso. Tente novamente.';
    }
} else {
    $_SESSION['error_message'] = 'ID do curso não fornecido.';
}

header("Location: ../../views/cursos/index.php");
exit();
?>