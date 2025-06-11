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

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/curso.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../../views/expiration.php");
    exit();
}

// Initialize curso object
$curso = new Curso($db);

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {    // Sanitize and set curso properties - usando FILTER_UNSAFE_RAW para preservar caracteres acentuados
    $curso->curso = mb_strtoupper(trim(filter_input(INPUT_POST, 'curso', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso->ano = filter_input(INPUT_POST, 'ano', FILTER_SANITIZE_NUMBER_INT) ?: '';
    $curso->turma = mb_strtoupper(trim(filter_input(INPUT_POST, 'turma', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso->inicio = filter_input(INPUT_POST, 'inicio', FILTER_UNSAFE_RAW) ?: '';
    $curso->termino = filter_input(INPUT_POST, 'termino', FILTER_UNSAFE_RAW) ?: '';
    $curso->local = mb_strtoupper(trim(filter_input(INPUT_POST, 'local', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso->situacao = trim(filter_input(INPUT_POST, 'situacao', FILTER_UNSAFE_RAW) ?: '');
    $curso->tipo_capacitacao = trim(filter_input(INPUT_POST, 'tipo_capacitacao', FILTER_UNSAFE_RAW) ?: '');
    $curso->modalidade = trim(filter_input(INPUT_POST, 'modalidade', FILTER_UNSAFE_RAW) ?: '');
    $curso->unidade = mb_strtoupper(trim(filter_input(INPUT_POST, 'unidade', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso->carga_horaria = filter_input(INPUT_POST, 'carga_horaria', FILTER_SANITIZE_NUMBER_INT) ?: '';
    $curso->instituicao = mb_strtoupper(trim(filter_input(INPUT_POST, 'instituicao', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso->municipio = mb_strtoupper(trim(filter_input(INPUT_POST, 'municipio', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso->autorização = trim(filter_input(INPUT_POST, 'autorização', FILTER_UNSAFE_RAW) ?: '');
    $curso->parecer = trim(filter_input(INPUT_POST, 'parecer', FILTER_UNSAFE_RAW) ?: '');

    // Validação robusta
    $erros = [];
    if(empty($curso->curso) || mb_strlen($curso->curso) < 3) {
        $erros[] = 'Nome do curso deve ter pelo menos 3 caracteres.';
    }
    if(empty($curso->ano) || !ctype_digit($curso->ano) || strlen($curso->ano) != 4) {
        $erros[] = 'Ano deve conter 4 dígitos numéricos.';
    }
    if(empty($curso->turma)) {
        $erros[] = 'Turma deve ser preenchida.';
    }
    if(empty($curso->inicio) || empty($curso->termino)) {
        $erros[] = 'Datas de início e término são obrigatórias.';
    }
    if(empty($curso->local)) {
        $erros[] = 'Local deve ser preenchido.';
    }
    if(empty($curso->situacao)) {
        $erros[] = 'Situação deve ser selecionada.';
    }
    if(empty($curso->tipo_capacitacao)) {
        $erros[] = 'Tipo de curso de inglês deve ser selecionado.';
    }
    if(empty($curso->modalidade)) {
        $erros[] = 'Modalidade deve ser selecionada.';
    }
    if(empty($curso->unidade)) {
        $erros[] = 'Unidade deve ser preenchido.';
    }
    if(empty($curso->carga_horaria) || !ctype_digit($curso->carga_horaria) || $curso->carga_horaria <= 0) {
        $erros[] = 'Carga horária deve ser um número positivo.';
    }
    if(empty($curso->instituicao)) {
        $erros[] = 'Instituição deve ser preenchida.';
    }
    if($erros) {
        $_SESSION['error_message'] = implode('<br>', $erros);
        header("Location: ../../views/cursos/create.php");
        exit();
    }
    
    // Validate dates
    $inicio_date = new DateTime($curso->inicio);
    $termino_date = new DateTime($curso->termino);
    
    if($termino_date < $inicio_date) {
        $_SESSION['error_message'] = "A data de término não pode ser anterior à data de início.";
        header("Location: ../../views/cursos/create.php");
        exit();
    }
    
    // Validate carga_horaria
    if(!is_numeric($curso->carga_horaria) || $curso->carga_horaria <= 0) {
        $_SESSION['error_message'] = "A carga horária deve ser um número positivo.";
        header("Location: ../../views/cursos/create.php");
        exit();
    }
    
    // Create curso
    if($curso->create()) {
        $GLOBALS['log']->info("Curso cadastrado: id={$curso->id}, curso={$curso->curso}, by user_id={$_SESSION['user_id']}");
        $_SESSION['message'] = "Curso de Inglês cadastrada com sucesso.";
        header("Location: ../../views/cursos/index.php");
        exit();
    } else {
        $GLOBALS['log']->error("Falha ao cadastrar curso: curso={$curso->curso}, by user_id={$_SESSION['user_id']}");
        $_SESSION['error_message'] = "Erro ao cadastrar a curso de inglês.";
        header("Location: ../../views/cursos/create.php");
        exit();
    }
} else {
    // If not a POST request, redirect to the form
    header("Location: ../../views/cursos/create.php");
    exit();
}
?>
