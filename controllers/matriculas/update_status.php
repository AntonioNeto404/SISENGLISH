<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';

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
include_once '../../models/matricula.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../../views/expiration.php");
    exit();
}

// Get params
$id = isset($_GET['id']) ? $_GET['id'] : die("Erro: ID da matrícula não especificado.");
$status = isset($_GET['status']) ? $_GET['status'] : die("Erro: Status não especificado.");
$curso_id = isset($_GET['curso_id']) ? $_GET['curso_id'] : die("Erro: ID do curso não especificado.");

// Initialize matricula object
$matricula = new Matricula($db);

// Set matricula properties
$matricula->id = $id;
$matricula->situacao = $status;

// Update matricula status
if($matricula->updateStatus()) {
    $GLOBALS['log']->info("Status da matricula atualizado: id={$id}, status={$status}, by user_id={$_SESSION['user_id']}");
    $_SESSION['message'] = "Status do estudante atualizado para " . $status . ".";
} else {
    $GLOBALS['log']->error("Falha ao atualizar status da matricula: id={$id}, status={$status}, by user_id={$_SESSION['user_id']}");
    $_SESSION['error_message'] = "Erro ao atualizar status do estudante.";
}

// Redirect back to the course view
header("Location: ../../views/cursos/view.php?id={$curso_id}");
exit();
?>
