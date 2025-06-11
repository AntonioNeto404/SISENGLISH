<?php
session_start();
if(!isset($_SESSION['user_id'])) { header('Location: ../index.php'); exit(); }
$page_title = 'Certidão de Professores';
require_once __DIR__ . '/../models/Professor.php';
require_once __DIR__ . '/../models/Curso.php';
include_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/csrf.php';

// Define timezone and current date in Portuguese
date_default_timezone_set('America/Recife');
setlocale(LC_TIME, 'pt_BR.UTF-8');
$today = strftime('%A, %d de %B de %Y');

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Initialize DB
$database = new Database();
$db = $database->getConnection();

// Fetch professor record
$professor = new Professor($db);
$professor->id = $id;
$professor->readOne();

// Fetch courses taught by professor
$cursoObj = new Curso($db);
$cursos = $cursoObj->readAll();

// Log certificate generation
$GLOBALS['log']->info("Certidão de professores gerada: professor_id={$id}, by user_id={$_SESSION['user_id']}");

// Render view
include __DIR__ . '/../views/relatorios/certidao_professores.php';