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
include_once '../../models/professor.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header('Location: ../../views/expiration.php'); exit();
}

// Get Professor ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) {
    $_SESSION['error_message'] = 'ID do professor não especificado.';
    header('Location: ../../views/professores/index.php'); exit();
}

// Delete professor
$professor = new Professor($db);
$professor->id = $id;
if($professor->delete()) {
    $GLOBALS['log']->info("Professor excluído: id={$id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['message'] = 'Professor excluído com sucesso.';
} else {
    $GLOBALS['log']->error("Falha ao excluir professor: id={$id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['error_message'] = 'Erro ao excluir professor.';
}

// Redirect to professores list
header('Location: ../../views/professores/index.php');
exit();
?>