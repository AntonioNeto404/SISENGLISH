<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';

// CSRF validation
require_once __DIR__ . '/../../config/csrf.php';
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

// Redirect - este controller foi desativado
$_SESSION['error_message'] = "O cadastro direto de professores foi desativado. Use o sistema de estudantes para cadastrar novos professores.";
header("Location: ../../views/professores/index.php");
exit();

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/professor.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../../views/expiration.php");
    exit();
}

// Initialize professor object
$professor = new Professor($db);

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {    // Sanitize and set professor properties
    $professor->nome = mb_strtoupper(trim(filter_input(INPUT_POST, 'nome', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $professor->cpf = preg_replace('/\D/', '', filter_input(INPUT_POST, 'cpf', FILTER_UNSAFE_RAW) ?: '');
    $professor->matricula = preg_replace('/\D/', '', filter_input(INPUT_POST, 'matricula', FILTER_UNSAFE_RAW) ?: '');
    $professor->rg = preg_replace('/\D/', '', filter_input(INPUT_POST, 'rg', FILTER_UNSAFE_RAW) ?: '');
    $professor->orgao_expedidor = mb_strtoupper(filter_input(INPUT_POST, 'orgao_expedidor', FILTER_UNSAFE_RAW) ?: '', 'UTF-8');
    $professor->lattes = filter_input(INPUT_POST, 'lattes', FILTER_SANITIZE_URL) ?: '';
    $professor->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '';
    $professor->fone_residencial = preg_replace('/\D/', '', filter_input(INPUT_POST, 'fone_residencial', FILTER_UNSAFE_RAW) ?: '');
    $professor->fone_profissional = preg_replace('/\D/', '', filter_input(INPUT_POST, 'fone_profissional', FILTER_UNSAFE_RAW) ?: '');
    $professor->fone_celular = preg_replace('/\D/', '', filter_input(INPUT_POST, 'fone_celular', FILTER_UNSAFE_RAW) ?: '');
    $professor->instituicao = mb_strtoupper(filter_input(INPUT_POST, 'instituicao', FILTER_UNSAFE_RAW) ?: '', 'UTF-8');
    $professor->data_ingresso = filter_input(INPUT_POST, 'data_ingresso', FILTER_UNSAFE_RAW) ?: '';
    $professor->cargo = mb_strtoupper(filter_input(INPUT_POST, 'cargo', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '', 'UTF-8');
    $professor->lotacao = mb_strtoupper(filter_input(INPUT_POST, 'lotacao', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '', 'UTF-8');
    $professor->cidade_lotacao = mb_strtoupper(filter_input(INPUT_POST, 'cidade_lotacao', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '', 'UTF-8');
    $professor->disciplinas_professor = filter_input(INPUT_POST, 'disciplinas_professor', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $professor->disciplinas_conteudista = filter_input(INPUT_POST, 'disciplinas_conteudista', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $professor->formacao_tecnologica_1 = filter_input(INPUT_POST, 'formacao_tecnologica_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $professor->formacao_tecnologica_2 = filter_input(INPUT_POST, 'formacao_tecnologica_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $professor->graduacao_1 = filter_input(INPUT_POST, 'graduacao_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $professor->graduacao_2 = filter_input(INPUT_POST, 'graduacao_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $professor->graduacao_3 = filter_input(INPUT_POST, 'graduacao_3', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $professor->especializacao_gestao = filter_input(INPUT_POST, 'especializacao_gestao', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $professor->especializacao_outros = filter_input(INPUT_POST, 'especializacao_outros', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $professor->mestrado = filter_input(INPUT_POST, 'mestrado', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $professor->doutorado = filter_input(INPUT_POST, 'doutorado', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $professor->pos_doutorado = filter_input(INPUT_POST, 'pos_doutorado', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';

    // Validação robusta
    $erros = [];
    if(empty($professor->nome) || mb_strlen($professor->nome) < 3) {
        $erros[] = 'Nome deve ter pelo menos 3 caracteres.';
    }
    if(empty($professor->cpf) || !ctype_digit($professor->cpf) || strlen($professor->cpf) != 11) {
        $erros[] = 'CPF deve conter 11 dígitos numéricos.';
    }
    if(empty($professor->matricula) || !ctype_digit($professor->matricula)) {
        $erros[] = 'Matrícula deve conter apenas números.';
    }
    if(empty($professor->email) || !filter_var($professor->email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'E-mail inválido.';
    }
    if($erros) {
        $_SESSION['error_message'] = implode('<br>', $erros);
        header("Location: ../../views/professores/create.php");
        exit();
    }
    
    // Validar URL do Lattes
    if (!empty($professor->lattes) && !preg_match('/lattes\.cnpq\.br\/\d+/i', $professor->lattes)) {
        $_SESSION['error_message'] = "Formato do Currículo Lattes inválido. Use o formato: lattes.cnpq.br/numeros";
        header("Location: ../../views/professores/create.php");
        exit();
    }
    
    // Create professor
    if($professor->create()) {
        $GLOBALS['log']->info("Professor cadastrado: id={$professor->id}, matricula={$professor->matricula}, by user_id={$_SESSION['user_id']}");
        $_SESSION['message'] = "Professor cadastrado com sucesso.";
        header("Location: ../../views/professores/index.php");
        exit();
    } else {
        $GLOBALS['log']->error("Falha ao cadastrar professor: matricula={$professor->matricula}, by user_id={$_SESSION['user_id']}");
        $_SESSION['error_message'] = "Erro ao cadastrar professor.";
        header("Location: ../../views/professores/create.php");
        exit();
    }
} else {
    // If not a POST request, redirect to the form
    header("Location: ../../views/professores/create.php");
    exit();
}
?>