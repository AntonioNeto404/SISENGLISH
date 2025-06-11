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
include_once '../../models/estudante.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../../views/expiration.php");
    exit();
}

// Initialize estudante object
$estudante = new Estudante($db);

// Get ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : die("Erro: ID do estudante não especificado.");

if($_SERVER['REQUEST_METHOD'] == 'POST') {    // Sanitize and set estudante properties
    $estudante->matricula = filter_input(INPUT_POST, 'matricula', FILTER_SANITIZE_NUMBER_INT) ?: '';
    $estudante->nome      = mb_strtoupper(trim(filter_input(INPUT_POST, 'nome', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $estudante->nível de inglês     = mb_strtoupper(trim(filter_input(INPUT_POST, 'nível de inglês', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $estudante->forca     = trim(filter_input(INPUT_POST, 'forca', FILTER_UNSAFE_RAW) ?: '');

    // Validação robusta
    $erros = [];
    if(empty($estudante->matricula) || !ctype_digit($estudante->matricula)) {
        $erros[] = 'Matrícula deve conter apenas números.';
    }    if(empty($estudante->nome) || mb_strlen($estudante->nome) < 3) {
        $erros[] = 'Nome deve ter pelo menos 3 caracteres.';
    }
    // Campo Nível de Inglês/Função é opcional - removida validação
    if(empty($estudante->forca)) {
        $erros[] = 'Telefone/Instituição deve ser selecionada.';
    }
    if($erros) {
        $_SESSION['error_message'] = implode('<br>', $erros);
        header("Location: ../../views/estudantes/edit.php?id={$id}");
        exit();
    }

    // Update estudante
    if($estudante->update()) {
        $GLOBALS['log']->info("Estudante atualizado: id={$id}, matricula={$estudante->matricula}, by user_id={$_SESSION['user_id']}");
        $_SESSION['message'] = "Estudante atualizado com sucesso.";
        header("Location: ../../views/estudantes/index.php");
    } else {
        $GLOBALS['log']->error("Falha ao atualizar estudante: id={$id}, matricula={$estudante->matricula}, by user_id={$_SESSION['user_id']}");
        $_SESSION['error_message'] = "Ocorreu um erro ao atualizar o estudante.";
        header("Location: ../../views/estudantes/edit.php?id={$id}");
    }
} else {
    // If not a POST request, redirect to the form
    header("Location: ../../views/estudantes/edit.php?id={$id}");
    exit();
}
?>