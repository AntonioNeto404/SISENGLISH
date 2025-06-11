<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/csrf.php';

// Auth: only admin
if(!isset($_SESSION['user_id']) || $_SESSION['tipo'] != 'ADMINISTRADOR') {
    header('Location: ../../index.php'); exit();
}

// Include database and model
include_once '../../config/database.php';
include_once '../../models/disciplina.php';

// Get DB connection
$database = new Database();
$db = $database->getConnection();

// Check expiration
if($database->checkExpiration()) {
    header('Location: ../../views/expiration.php'); exit();
}

// Validate CSRF
csrf_validate();

// Get ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) {
    $_SESSION['error_message'] = 'ID da disciplina não especificado.';
    header('Location: ../../controllers/disciplinas/index.php'); exit();
}

// Delete disciplina
$disc = new Disciplina($db);
$disc->id = $id;
if($disc->delete()) {
    $GLOBALS['log']->info("Disciplina excluída: id={$id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['message'] = 'Disciplina excluída com sucesso.';
} else {
    $GLOBALS['log']->error("Falha ao excluir disciplina: id={$id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['error_message'] = 'Erro ao excluir disciplina.';
}

// Redirect to list
header('Location: ../../controllers/disciplinas/index.php');
exit();
?>