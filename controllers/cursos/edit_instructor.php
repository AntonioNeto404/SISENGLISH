<?php
// filepath: c:\xampp\htdocs\siscap03\controllers\cursos\edit_instructor.php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';

// CSRF validation
include_once __DIR__ . '/../../config/csrf.php';
csrf_validate();

// Auth check
if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'ADMINISTRADOR') {
    header('Location: ../../index.php'); exit();
}

// Include database and models
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/curso.php';
include_once __DIR__ . '/../../models/docente.php';
include_once __DIR__ . '/../../models/disciplina.php';
include_once __DIR__ . '/../../models/curso_instrutor.php';

// DB connection
$database = new Database();
$db = $database->getConnection();
if ($database->checkExpiration()) {
    header('Location: ../../views/expiration.php'); exit();
}

// Only handle POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/cursos/index.php'); exit();
}

// Sanitize inputs
$pivot_id      = filter_input(INPUT_POST, 'pivot_id', FILTER_VALIDATE_INT) ?: '';
$curso_id      = filter_input(INPUT_POST, 'curso_id', FILTER_VALIDATE_INT) ?: '';
$matricula     = preg_replace('/\D/', '', filter_input(INPUT_POST, 'matricula', FILTER_UNSAFE_RAW) ?: '');
$posto_cargo   = mb_strtoupper(strip_tags(filter_input(INPUT_POST, 'posto_cargo', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
$nome          = mb_strtoupper(strip_tags(filter_input(INPUT_POST, 'nome', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
$instituicao   = mb_strtoupper(strip_tags(filter_input(INPUT_POST, 'instituicao', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
$classificacao = mb_strtoupper(strip_tags(filter_input(INPUT_POST, 'classificacao', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
$disciplina    = strip_tags(filter_input(INPUT_POST, 'disciplina', FILTER_UNSAFE_RAW) ?: '');
$carga_horaria = filter_input(INPUT_POST, 'carga_horaria', FILTER_VALIDATE_INT) ?: '';

// Validation
$errors = [];
if (!$pivot_id || !$curso_id) {
    $errors[] = 'Dados inválidos.';
}
if (empty($matricula) || empty($nome) || empty($instituicao) || empty($disciplina) || !$carga_horaria) {
    $errors[] = 'Os campos Matrícula, Nome, Instituição, Disciplina e Carga Horária são obrigatórios.';
}
if ($errors) {
    $_SESSION['error_message'] = implode('<br>', $errors);
    header("Location: ../../views/cursos/edit_instructor.php?id={$pivot_id}&curso_id={$curso_id}");
    exit();
}

// Remove old pivot
$pivot = new CursoInstrutor($db);
$pivot->id = $pivot_id;
if (!$pivot->delete()) {
    $_SESSION['error_message'] = 'Erro ao remover registro antigo.';
    header("Location: ../../views/cursos/view.php?id={$curso_id}"); exit();
}

// Find or create docente
$docenteModel = new Docente($db);
$stmtDoc = $docenteModel->search($matricula);
$docRow = $stmtDoc->fetch(PDO::FETCH_ASSOC);
if (!$docRow) {
    $docenteModel->matricula = $matricula;
    $docenteModel->nome = $nome;
    $docenteModel->instituicao = $instituicao;
    $docenteModel->cargo = $posto_cargo;
    if ($docenteModel->create()) {
        $docId = $docenteModel->id;
    } else {
        $_SESSION['error_message'] = 'Erro ao cadastrar novo docente.';
        header("Location: ../../views/cursos/edit_instructor.php?id={$pivot_id}&curso_id={$curso_id}"); exit();
    }
} else {
    $docId = $docRow['id'];
    // Optional update
    $docenteModel->id = $docId;
    $docenteModel->nome = $nome;
    $docenteModel->instituicao = $instituicao;
    $docenteModel->cargo = $posto_cargo;
    $docenteModel->update();
}

// Find or create disciplina
$disciplinaModel = new Disciplina($db);
$stmtDis = $disciplinaModel->findByName($disciplina);
$disRow = $stmtDis->fetch(PDO::FETCH_ASSOC);
if (!$disRow) {
    $disciplinaModel->nome = $disciplina;
    $disciplinaModel->carga_horaria = $carga_horaria;
    if ($disciplinaModel->create()) {
        $disId = $disciplinaModel->id;
    } else {
        $_SESSION['error_message'] = 'Erro ao cadastrar nova disciplina.';
        header("Location: ../../views/cursos/edit_instructor.php?id={$pivot_id}&curso_id={$curso_id}"); exit();
    }
} else {
    $disId = $disRow['id'];
    // Optional update carga
    if ($disRow['carga_horaria'] != $carga_horaria) {
        $disciplinaModel->id = $disId;
        $disciplinaModel->carga_horaria = $carga_horaria;
        $disciplinaModel->update();
    }
}

// Create new pivot
$newPivot = new CursoInstrutor($db);
$newPivot->formacao_id = $curso_id;
$newPivot->docente_id = $docId;
$newPivot->disciplina_id = $disId;
$newPivot->created_by = $_SESSION['user_id'];
if ($newPivot->create()) {
    $GLOBALS['log']->info("Instrutor atualizado: antigo_pivot_id={$pivot_id}, novo_pivot_id={$newPivot->id}, curso_id={$curso_id}, by user_id={$_SESSION['user_id']}");
    $_SESSION['message'] = 'Instrutor atualizado com sucesso!';
} else {
    $_SESSION['error_message'] = 'Erro ao atualizar instrutor.';
}

header("Location: ../../views/cursos/view.php?id={$curso_id}");
exit();
?>
