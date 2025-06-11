<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';

// CSRF validation
require_once __DIR__ . '/../../config/csrf.php';
csrf_validate();

// Auth & expiration
if(!isset($_SESSION['user_id']) || $_SESSION['tipo'] != 'ADMINISTRADOR') {
    header('Location: ../../index.php'); exit();
}

include_once '../../config/database.php';
include_once '../../models/professor.php';

$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) {
    header('Location: ../expiration.php'); exit();
}

// Ensure ID provided
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: die('ID não especificado.');
$professor = new Professor($db);
$professor->id = $id;

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {    // Sanitize and set professor properties
    $professor->nome = mb_strtoupper(trim(filter_input(INPUT_POST, 'nome', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $professor->cpf = preg_replace('/\D/', '', filter_input(INPUT_POST, 'cpf', FILTER_UNSAFE_RAW) ?: '');
    $professor->matricula = preg_replace('/\D/', '', filter_input(INPUT_POST, 'matricula', FILTER_UNSAFE_RAW) ?: '');
    $professor->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '';
    foreach(['situacao','rg','orgao_expedidor','lattes','fone_residencial','fone_profissional','fone_celular','instituicao','data_ingresso','cargo','lotacao','cidade_lotacao','disciplinas_professor','disciplinas_conteudista','formacao_tecnologica_1','formacao_tecnologica_2','graduacao_1','graduacao_2','graduacao_3','especializacao_gestao','especializacao_outros','mestrado','doutorado','pos_doutorado'] as $f) {
        $professor->$f = $_POST[$f] ?? '';
    }
    // Validação robusta
    $erros = [];
    if(empty($professor->nome) || mb_strlen($professor->nome) < 3) {
        $erros[] = 'Nome deve ter pelo menos 3 caracteres.';
    }
    if(empty($professor->matricula) || !ctype_digit($professor->matricula)) {
        $erros[] = 'Matrícula deve conter apenas números.';
    }
    if($erros) {
        $_SESSION['error_message'] = implode('<br>', $erros);
        header("Location: ../../views/professores/edit.php?id={$id}");
        exit();
    }
    if($professor->update()) {
        $GLOBALS['log']->info("Professor atualizado: id={$id}, by user_id={$_SESSION['user_id']}");
        $_SESSION['message'] = "Professor atualizado com sucesso.";
        header("Location: ../../views/professores/index.php");
    } else {
        $GLOBALS['log']->error("Falha ao atualizar professor: id={$id}, by user_id={$_SESSION['user_id']}");
        $_SESSION['error_message'] = "Erro ao atualizar professor.";
        header("Location: ../../views/professores/edit.php?id={$id}");
    }
    exit();
} else {
    header("Location: ../../views/professores/index.php"); exit();
}
?>