<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/csrf.php';

// CSRF validation and sanitization
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
include_once '../../models/estudante.php';
include_once '../../models/matricula.php';

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

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {    // Sanitize and set estudante properties
    $estudante->matricula = filter_input(INPUT_POST, 'matricula', FILTER_SANITIZE_NUMBER_INT) ?: '';
    $estudante->nome      = mb_strtoupper(trim(filter_input(INPUT_POST, 'nome', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $estudante->nível de inglês     = mb_strtoupper(trim(filter_input(INPUT_POST, 'nível de inglês', FILTER_UNSAFE_RAW) ?: ''), 'UTF-8');
    $estudante->forca     = trim(filter_input(INPUT_POST, 'forca', FILTER_UNSAFE_RAW) ?: '');
    
    // Get condition for enrollment (used when matriculating automatically)
    $condicao = trim(filter_input(INPUT_POST, 'condicao', FILTER_UNSAFE_RAW) ?: 'ATIVO');

    // Validação robusta
    $erros = [];
    if(empty($estudante->matricula) || !ctype_digit($estudante->matricula)) {
        $erros[] = 'Matrícula deve conter apenas números.';
    }    if(empty($estudante->nome) || mb_strlen($estudante->nome) < 3) {
        $erros[] = 'Nome deve ter pelo menos 3 caracteres.';
    }    // Campo Nível de Inglês/Função é opcional - removida validação
    if(empty($estudante->forca)) {
        $erros[] = 'Telefone/Instituição deve ser selecionada.';
    }
    // Validar condição se vier do contexto de curso
    if(isset($_GET['redirect']) && $_GET['redirect'] == 'curso' && empty($condicao)) {
        $erros[] = 'Condição deve ser selecionada.';
    }
    if($erros) {
        $_SESSION['error_message'] = implode('<br>', $erros);
        if(isset($_GET['redirect']) && $_GET['redirect'] == 'curso' && isset($_GET['curso_id'])) {
            header("Location: ../../views/cursos/enroll.php?id=" . $_GET['curso_id']);
        } else {
            header("Location: ../../views/estudantes/create.php");
        }
        exit();
    }
      // Create estudante
    if($estudante->create()) {
        $GLOBALS['log']->info("Estudante cadastrad0: matricula={$estudante->matricula}, nome={$estudante->nome}, by user_id={$_SESSION['user_id']}");
          // If coming from curso enroll context, automatically enroll the student
        if(isset($_GET['redirect']) && $_GET['redirect'] == 'curso' && isset($_GET['curso_id'])) {
            $matricula = new Matricula($db);
            $matricula->formacao_id = $_GET['curso_id'];
            $matricula->estudante_id = $estudante->id; // Get the ID of the newly created student
            $matricula->situacao = $condicao; // Use the selected condition
            
            if($matricula->create()) {
                $_SESSION['message'] = "Estudante cadastrado e matriculado no curso com sucesso.";
                $GLOBALS['log']->info("Estudante automaticamente matriculado: estudante_id={$estudante->id}, curso_id={$_GET['curso_id']}, situacao={$condicao}, by user_id={$_SESSION['user_id']}");
            } else {
                $_SESSION['message'] = "Estudante cadastrado com sucesso, mas houve um erro na matrícula automática.";
                $GLOBALS['log']->warning("Falha na matrícula automática: estudante_id={$estudante->id}, curso_id={$_GET['curso_id']}, by user_id={$_SESSION['user_id']}");
            }
            
            header("Location: ../../views/cursos/view.php?id=" . $_GET['curso_id']);
        } else {
            $_SESSION['message'] = "Estudante cadastrado com sucesso.";
            header("Location: ../../views/estudantes/index.php");
        }
        exit();    } else {
        $GLOBALS['log']->error("Falha ao cadastrar estudante: matricula={$estudante->matricula}, by user_id={$_SESSION['user_id']}");
        $_SESSION['error_message'] = "Ocorreu um erro ao cadastrar o estudante.";
        if(isset($_GET['redirect']) && $_GET['redirect'] == 'curso' && isset($_GET['curso_id'])) {
            header("Location: ../../views/cursos/enroll.php?id=" . $_GET['curso_id']);
        } else {
            header("Location: ../../views/estudantes/create.php");
        }
        exit();
    }
} else {
    // If not a POST request, redirect to the form
    if(isset($_GET['redirect']) && $_GET['redirect'] == 'curso' && isset($_GET['curso_id'])) {
        header("Location: ../../views/cursos/enroll.php?id=" . $_GET['curso_id']);
    } else {
        header("Location: ../../views/estudantes/create.php");
    }
    exit();
}
?>
