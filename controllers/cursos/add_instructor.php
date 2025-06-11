<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';

// CSRF validation
include_once '../../config/csrf.php';
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
include_once '../../models/professor.php';
include_once '../../models/disciplina.php';
include_once '../../models/estudante.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../../views/expiration.php");
    exit();
}

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the mode type
    $mode = filter_input(INPUT_POST, 'mode', FILTER_UNSAFE_RAW) ?: 'registration';
    
    // Sanitize and retrieve common form data
    $curso_id     = filter_input(INPUT_POST, 'curso_id', FILTER_VALIDATE_INT) ?: '';
    $disciplina   = strip_tags(filter_input(INPUT_POST, 'disciplina', FILTER_UNSAFE_RAW) ?: '');
    $carga_horaria= filter_input(INPUT_POST, 'carga_horaria', FILTER_VALIDATE_INT) ?: '';
    
    // Initialize variables that will be populated based on mode
    $matricula = '';
    $nome = '';
    $nível de inglês_cargo = '';
    $instituicao = '';
    $classificacao = '';
    
    if ($mode === 'participant') {
        // Participant mode - get data from selected participant
        $estudante_id = filter_input(INPUT_POST, 'estudante_id', FILTER_VALIDATE_INT);
        
        if (empty($estudante_id)) {
            $_SESSION['error_message'] = "Selecione um estudante.";
            header("Location: ../../views/cursos/add_instructor.php?id={$curso_id}");
            exit();
        }
          // Get participant data
        $estudanteModel = new Estudante($db);
        $estudanteModel->id = $estudante_id;
        $result = $estudanteModel->readOne();
        
        if (!$result || !$estudanteModel->id) {
            $_SESSION['error_message'] = "Estudante não encontrado.";
            header("Location: ../../views/cursos/add_instructor.php?id={$curso_id}");
            exit();
        }
          // Use participant data for instructor creation
        $matricula = $estudanteModel->matricula;
        $nome = $estudanteModel->nome;
        $nível de inglês_cargo = $estudanteModel->nível de inglês; // 'nível de inglês' field in estudantes table
        $instituicao = $estudanteModel->forca; // 'forca' field in estudantes table (Telefone/Instituição)
        $classificacao = ''; // Not available in estudantes table, will be empty
        
    } else {
        // Registration or name mode - get data from form fields
        $matricula    = preg_replace('/\D/', '', filter_input(INPUT_POST, 'matricula', FILTER_UNSAFE_RAW) ?: '');
        $nível de inglês_cargo  = mb_strtoupper(strip_tags(filter_input(INPUT_POST, 'nível de inglês_cargo', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
        $nome         = mb_strtoupper(strip_tags(filter_input(INPUT_POST, 'nome', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
        $instituicao  = mb_strtoupper(strip_tags(filter_input(INPUT_POST, 'instituicao', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
        $classificacao= mb_strtoupper(strip_tags(filter_input(INPUT_POST, 'classificacao', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    }

    // Validate required fields
    if(empty($curso_id) || empty($matricula) || empty($nome) || empty($instituicao) || empty($disciplina) || empty($carga_horaria)) {
        $_SESSION['error_message'] = "Os campos Matrícula, Nome, Instituição, Disciplina e Carga Horária são obrigatórios.";
        header("Location: ../../views/cursos/add_instructor.php?id={$curso_id}");
        exit();
    }

    // Insert instructor into course
    include_once '../../models/curso_professor.php';
    // Find professor by matricula
    $professorModel = new Professor($db);
    $stmtDoc = $professorModel->search($matricula);
    $docRow = $stmtDoc->fetch(PDO::FETCH_ASSOC);    // changed code: if not found, create a new Professor record
    if(!$docRow) {
        // Populate all available fields for new professor
        $professorModel->matricula = $matricula;
        $professorModel->nome = $nome;
        $professorModel->cargo = $nível de inglês_cargo; // cargo field in professores table
        $professorModel->instituicao = $instituicao;
        $professorModel->classificacao = $classificacao;
        // Note: professores table doesn't have created_by field, so we don't set it
        
        if($professorModel->create()) {
            // Use newly created professor id
            $docRow = ['id' => $professorModel->id];
            
            // Log the creation for audit trail
            $GLOBALS['log']->info("Professor criado automaticamente: id={$professorModel->id}, matricula={$matricula}, modo={$mode}, by user_id={$_SESSION['user_id']}");
        } else {
            $_SESSION['error_message'] = "Erro ao cadastrar novo professor.";
            header("Location: ../../views/cursos/add_instructor.php?id={$curso_id}");
            exit();
        }
    }
    // Find or create disciplina
    $disciplinaModel = new Disciplina($db);
    $stmtDis = $disciplinaModel->findByName($disciplina);
    $disRow = $stmtDis->fetch(PDO::FETCH_ASSOC);
    if(!$disRow) {
        $disciplinaModel->nome = $disciplina;
        $disciplinaModel->carga_horaria = $carga_horaria;
        if($disciplinaModel->create()) {
            $disRow = ['id' => $disciplinaModel->id];
        } else {
            $_SESSION['error_message'] = "Erro ao cadastrar nova disciplina.";
            header("Location: ../../views/cursos/add_instructor.php?id={$curso_id}");
            exit();
        }
    }

    $pivot = new CursoProfessor($db);
    $pivot->formacao_id = $curso_id;
    $pivot->professor_id = $docRow['id'];
    $pivot->disciplina_id = $disRow['id'];
    $pivot->created_by = $_SESSION['user_id'];    if($pivot->create()) {
        $GLOBALS['log']->info("Professor vinculado: pivot_id={$pivot->id}, curso_id={$curso_id}, professor_id={$pivot->professor_id}, disciplina_id={$disciplina}, modo={$mode}, by user_id={$_SESSION['user_id']}");
        
        if ($mode === 'participant') {
            $_SESSION['message'] = "Estudante '{$nome}' designado como professor com sucesso!";
        } else {
            $_SESSION['message'] = "Professor '{$nome}' adicionado com sucesso!";
        }
    } else {
        $GLOBALS['log']->error("Falha ao vincular professor: curso_id={$curso_id}, matricula={$matricula}, disciplina_id={$disciplina}, modo={$mode}, by user_id={$_SESSION['user_id']}");
        $_SESSION['error_message'] = "Erro ao adicionar professor à curso de inglês.";
    }
    header("Location: ../../views/cursos/view.php?id={$curso_id}");
    exit();
}

// If not POST request, redirect back to form
// This is when someone accesses the controller directly without POST
// Need to redirect to a safe page since we don't have the course ID here
header("Location: ../../views/cursos/index.php");
exit();
?>