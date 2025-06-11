<?php
session_start();
require_once __DIR__ . '/../config/bootstrap.php';

if(!isset($_SESSION['user_id'])) { header('Location: ../index.php'); exit(); }
$page_title = 'Certidão de Discentes';
require_once __DIR__ . '/../models/Estudante.php';
require_once __DIR__ . '/../models/Matricula.php';
include_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/csrf.php';

// Define date
date_default_timezone_set('America/Recife');
setlocale(LC_TIME, 'pt_BR.UTF-8');
$today = strftime('%A, %d de %B de %Y');

// Get student ID
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: index.php'); exit; }

// Database
$database = new Database();
$db = $database->getConnection();

// Fetch student
$estudante = new Estudante($db);
$estudante->id = $id;
$estudante->readOne();

// Fetch courses attended
$mat = new Matricula($db);
$mat->estudante_id = $id;
$cursos = $mat->getCoursesByStudent();

// Log certificate generation
$GLOBALS['log']->info("Certidão de discentes gerada: estudante_id={$id}, by user_id={$_SESSION['user_id']}");

// Validate CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
}

// Render view
include __DIR__ . '/../views/relatorios/certidao_discentes.php';