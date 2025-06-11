<?php
// filepath: c:\xampp\htdocs\siscap03\controllers\cursos\search.php
session_start();
// Only allow admins
if(!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'ADMINISTRADOR') {
    http_response_code(403);
    echo json_encode([]);
    exit();
}

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/curso.php';

// Get search term
$term = filter_input(INPUT_GET, 'term', FILTER_UNSAFE_RAW) ?: '';
// Decode HTML entities to handle accented characters correctly
$term = html_entity_decode($term, ENT_QUOTES | ENT_HTML5, 'UTF-8');
// Minimum length
if(mb_strlen($term, 'UTF-8') < 2) {
    echo json_encode([]);
    exit();
}

// Database connection
$database = new Database();
$db = $database->getConnection();

// Prepare and execute query
$like = '%' . strip_tags($term) . '%';
$sql = "SELECT DISTINCT curso FROM formacoes WHERE curso LIKE ? ORDER BY curso LIMIT 10";
$stmt = $db->prepare($sql);
$stmt->bindParam(1, $like, PDO::PARAM_STR);
$stmt->execute();

// Fetch results
$results = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Return JSON
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($results, JSON_UNESCAPED_UNICODE);
exit();
