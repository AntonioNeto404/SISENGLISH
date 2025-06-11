<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/csrf.php';

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
$curso_id = isset($_GET['curso_id']) ? $_GET['curso_id'] : die("Erro: ID do curso não especificado.");

// Initialize matricula object
$matricula = new Matricula($db);

// Set matricula ID
$matricula->id = $id;

// Delete matricula
if($matricula->delete()) {
    $GLOBALS['log']->info("Matricula removida: id={$id}, curso_id={$curso_id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['message'] = "Estudante removido da curso de inglês com sucesso.";
} else {
    $GLOBALS['log']->error("Falha ao remover matricula: id={$id}, curso_id={$curso_id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['error_message'] = "Erro ao remover estudante da curso de inglês.";
}

// Redirect back to the course view
header("Location: ../../views/cursos/view.php?id={$curso_id}");
exit();
?>
