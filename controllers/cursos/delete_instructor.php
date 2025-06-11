<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';

// Auth check
if(!isset($_SESSION['user_id']) || $_SESSION['tipo'] != 'ADMINISTRADOR') {
    header('Location: ../../index.php'); exit();
}

// CSRF optional for GET operations or use token in link
// Include database and model
include_once '../../config/database.php';
include_once '../../models/curso_professor.php';

// DB connection
$database = new Database();
$db = $database->getConnection();

// Expiration
if($database->checkExpiration()) {header('Location: ../../views/expiration.php'); exit();}

// Get params
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: die('Erro: ID não especificado.');
$curso_id = filter_input(INPUT_GET, 'curso_id', FILTER_VALIDATE_INT) ?: die('Erro: Curso não especificado.');

// Delete assignment
$pivot = new CursoProfessor($db);
$pivot->id = $id;
if($pivot->delete()) {
    $GLOBALS['log']->info("Professor removido: pivot_id={$id}, curso_id={$curso_id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['message'] = 'Professor removido com sucesso.';
} else {
    $GLOBALS['log']->error("Falha ao remover professor: pivot_id={$id}, curso_id={$curso_id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['error_message'] = 'Erro ao remover professor.';
}

header("Location: ../../views/cursos/view.php?id={$curso_id}");
exit();
