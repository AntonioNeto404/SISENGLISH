<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';

// CSRF validation
require_once __DIR__ . '/../../config/csrf.php';
csrf_validate();

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

// Ensure ID provided
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) {
    $_SESSION['error_message'] = 'ID da disciplina não especificado.';
    header('Location: ../../controllers/disciplinas/index.php'); exit();
}

// Load existing record
$disc = new Disciplina($db);
$disc->id = $id;
if(!$disc->readOne()) {
    $_SESSION['error_message'] = 'Disciplina não encontrada.';
    header('Location: ../../controllers/disciplinas/index.php'); exit();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {    $disc->nome = mb_strtoupper(trim(filter_input(INPUT_POST, 'nome', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $disc->descricao = trim(filter_input(INPUT_POST, 'descricao', FILTER_UNSAFE_RAW) ?: '');
    $disc->carga_horaria = filter_input(INPUT_POST, 'carga_horaria', FILTER_SANITIZE_NUMBER_INT) ?: '';

    // Validação robusta
    $erros = [];
    if(empty($disc->nome) || mb_strlen($disc->nome) < 3) {
        $erros[] = 'Nome da disciplina deve ter pelo menos 3 caracteres.';
    }
    if($disc->carga_horaria === '' || !ctype_digit($disc->carga_horaria) || $disc->carga_horaria <= 0) {
        $erros[] = 'Carga horária deve ser um número positivo.';
    }
    if($erros) {
        $_SESSION['error_message'] = implode('<br>', $erros);
        header("Location: ../../views/disciplinas/edit.php?id={$id}");
        exit();
    }

    if($disc->update()) {
        $GLOBALS['log']->info("Disciplina atualizada: id={$id}, nome={$disc->nome}, by user_id={$_SESSION['user_id']}");
        $_SESSION['message'] = 'Disciplina atualizada com sucesso.';
        header('Location: ../../controllers/disciplinas/index.php'); exit();
    } else {
        $GLOBALS['log']->error("Falha ao atualizar disciplina: id={$id}, nome={$disc->nome}, by user_id={$_SESSION['user_id']}");
        $_SESSION['error_message'] = 'Erro ao atualizar disciplina.';
        header("Location: ../../views/disciplinas/edit.php?id={$id}"); exit();
    }
}

// Direct access redirect
header('Location: ../../controllers/disciplinas/index.php'); exit();