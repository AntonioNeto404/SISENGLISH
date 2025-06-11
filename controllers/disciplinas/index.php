<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';

// Auth: only admin
if(!isset($_SESSION['user_id']) || $_SESSION['tipo'] != 'ADMINISTRADOR') {
    header('Location: ../../index.php');
    exit();
}

// Include database and model
include_once '../../config/database.php';
include_once '../../models/disciplina.php';

// Get DB connection
$database = new Database();
$db = $database->getConnection();

// Check expiration
if($database->checkExpiration()) {
    header('Location: ../../views/expiration.php');
    exit();
}

// Fetch all disciplinas
$discModel = new Disciplina($db);
$stmt = $discModel->readAll();

// Log access
$GLOBALS['log']->info("Lista de disciplinas acessada por user_id=" . $_SESSION['user_id']);

// Render view
include_once __DIR__ . '/../../views/disciplinas/index.php';
