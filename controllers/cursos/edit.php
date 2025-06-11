<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/csrf.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] != 'ADMINISTRADOR') {
    header("Location: ../../index.php");
    exit();
}

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/curso.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if ($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    
    $id = $_POST['id'];
    $curso = $_POST['curso'];
    $ano = $_POST['ano'];
    $turma = $_POST['turma'];
    $inicio = $_POST['inicio'];
    $termino = $_POST['termino'];
    $local = $_POST['local'];
    $situacao = $_POST['situacao'];
    $tipo_capacitacao = $_POST['tipo_capacitacao'];
    $modalidade = $_POST['modalidade'];
    $unidade = $_POST['unidade'];
    $carga_horaria = $_POST['carga_horaria'];
    $instituicao = $_POST['instituicao'];
    $municipio = $_POST['municipio'] ?? '';
    $autorização = $_POST['autorização'] ?? '';
    $parecer = $_POST['parecer'] ?? '';    // Sanitize and set curso properties
    $curso_obj = new Curso($db);
    $curso_obj->id = $id;
    $curso_obj->curso = mb_strtoupper(trim(filter_input(INPUT_POST, 'curso', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso_obj->ano = filter_input(INPUT_POST, 'ano', FILTER_SANITIZE_NUMBER_INT) ?: '';
    $curso_obj->turma = mb_strtoupper(trim(filter_input(INPUT_POST, 'turma', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso_obj->inicio = filter_input(INPUT_POST, 'inicio', FILTER_UNSAFE_RAW) ?: '';
    $curso_obj->termino = filter_input(INPUT_POST, 'termino', FILTER_UNSAFE_RAW) ?: '';
    $curso_obj->local = mb_strtoupper(trim(filter_input(INPUT_POST, 'local', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso_obj->situacao = trim(filter_input(INPUT_POST, 'situacao', FILTER_UNSAFE_RAW) ?: '');
    $curso_obj->tipo_capacitacao = trim(filter_input(INPUT_POST, 'tipo_capacitacao', FILTER_UNSAFE_RAW) ?: '');
    $curso_obj->modalidade = trim(filter_input(INPUT_POST, 'modalidade', FILTER_UNSAFE_RAW) ?: '');
    $curso_obj->unidade = mb_strtoupper(trim(filter_input(INPUT_POST, 'unidade', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso_obj->carga_horaria = filter_input(INPUT_POST, 'carga_horaria', FILTER_SANITIZE_NUMBER_INT) ?: '';
    $curso_obj->instituicao = mb_strtoupper(trim(filter_input(INPUT_POST, 'instituicao', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso_obj->municipio = mb_strtoupper(trim(filter_input(INPUT_POST, 'municipio', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $curso_obj->autorização = trim(filter_input(INPUT_POST, 'autorização', FILTER_UNSAFE_RAW) ?: '');
    $curso_obj->parecer = trim(filter_input(INPUT_POST, 'parecer', FILTER_UNSAFE_RAW) ?: '');

    // Validação robusta
    $erros = [];
    if(empty($curso_obj->curso) || mb_strlen($curso_obj->curso) < 3) {
        $erros[] = 'Nome do curso deve ter pelo menos 3 caracteres.';
    }
    if(empty($curso_obj->ano) || !ctype_digit($curso_obj->ano) || strlen($curso_obj->ano) != 4) {
        $erros[] = 'Ano deve conter 4 dígitos numéricos.';
    }
    if(empty($curso_obj->turma)) {
        $erros[] = 'Turma deve ser preenchida.';
    }
    if(empty($curso_obj->inicio) || empty($curso_obj->termino)) {
        $erros[] = 'Datas de início e término são obrigatórias.';
    }
    if(empty($curso_obj->local)) {
        $erros[] = 'Local deve ser preenchido.';
    }
    if(empty($curso_obj->situacao)) {
        $erros[] = 'Situação deve ser selecionada.';
    }
    if(empty($curso_obj->tipo_capacitacao)) {
        $erros[] = 'Tipo de curso de inglês deve ser selecionado.';
    }
    if(empty($curso_obj->modalidade)) {
        $erros[] = 'Modalidade deve ser selecionada.';
    }
    if(empty($curso_obj->unidade)) {
        $erros[] = 'Unidade deve ser preenchido.';
    }
    if(empty($curso_obj->carga_horaria) || !ctype_digit($curso_obj->carga_horaria) || $curso_obj->carga_horaria <= 0) {
        $erros[] = 'Carga horária deve ser um número positivo.';
    }
    if(empty($curso_obj->instituicao)) {
        $erros[] = 'Instituição deve ser preenchida.';
    }
    if($erros) {
        $_SESSION['error_message'] = implode('<br>', $erros);
        header("Location: ../../views/cursos/edit.php?id={$id}");
        exit();
    }

    // Update course
    if ($curso_obj->update()) {
        $GLOBALS['log']->info("Curso atualizado: id={$id}, curso={$curso_obj->curso}, by user_id={$_SESSION['user_id']}");
        $_SESSION['message'] = 'Curso atualizado com sucesso!';
        header("Location: ../../views/cursos/index.php");
    } else {
        $GLOBALS['log']->error("Falha ao atualizar curso: id={$id}, curso={$curso_obj->curso}, by user_id={$_SESSION['user_id']}");
        $_SESSION['error_message'] = 'Erro ao atualizar o curso. Tente novamente.';
        header("Location: ../../views/cursos/edit.php?id=$id");
    }
    exit();
}

// Redirect if accessed directly
header("Location: ../../views/cursos/index.php");
exit();
?>