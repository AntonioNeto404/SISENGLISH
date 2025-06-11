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
include_once '../../models/matricula.php';
include_once '../../models/curso.php';
include_once '../../models/estudante.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../../views/expiration.php");
    exit();
}

// Initialize objects
$matricula = new Matricula($db);
$curso = new Curso($db);
$estudante = new Estudante($db);

// Get params
$curso_id = isset($_GET['curso_id']) ? $_GET['curso_id'] : die("Erro: ID do curso não especificado.");
$estudante_id = isset($_GET['estudante_id']) ? $_GET['estudante_id'] : die("Erro: ID do estudante não especificado.");

// Set matricula properties
$matricula->formacao_id = $curso_id;
$matricula->estudante_id = $estudante_id;
$matricula->situacao = "ATIVO";

// Get curso and estudante details for the message
$curso->id = $curso_id;
$curso->readOne();
$estudante->id = $estudante_id;
$estudante->readOne();

// Create matricula
if($matricula->create()) {
    $GLOBALS['log']->info("Matricula criada: estudante_id={$estudante_id}, curso_id={$curso_id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['message'] = "Estudante " . $estudante->nome . " matriculado com sucesso no curso " . $curso->curso . " " . $curso->ano . ".";
    // Redirect to cursos index after enrollment
    header("Location: ../../views/cursos/index.php");
    exit();
} else {
    $GLOBALS['log']->error("Falha ao matricular estudante: estudante_id={$estudante_id}, curso_id={$curso_id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['error_message'] = "Erro ao matricular estudante. Talvez ele já esteja matriculado.";
    header("Location: ../../views/cursos/enroll.php?id={$curso_id}");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and set matricula properties
    $matricula->formacao_id = filter_input(INPUT_POST, 'formacao_id', FILTER_SANITIZE_NUMBER_INT) ?: '';
    $matricula->estudante_id = filter_input(INPUT_POST, 'estudante_id', FILTER_SANITIZE_NUMBER_INT) ?: '';
    $matricula->situacao = trim(filter_input(INPUT_POST, 'situacao', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'ATIVO');

    // Validação robusta
    $erros = [];
    if(empty($matricula->formacao_id) || !ctype_digit($matricula->formacao_id)) {
        $erros[] = 'ID da curso de inglês inválido.';
    }
    if(empty($matricula->estudante_id) || !ctype_digit($matricula->estudante_id)) {
        $erros[] = 'ID do estudante inválido.';
    }
    if(empty($matricula->situacao)) {
        $erros[] = 'Situação deve ser informada.';
    }
    if($erros) {
        $_SESSION['error_message'] = implode('<br>', $erros);
        header('Location: ../../views/matriculas/create.php');
        exit();
    }
}
?>
