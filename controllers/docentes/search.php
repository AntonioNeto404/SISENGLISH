<?php
session_start();
// Verify user session
if(!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit();
}

// Include database and model
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/professor.php';

// Get DB connection and check expiration
$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) {
    http_response_code(403);
    exit();
}

// Get search parameters
$query = filter_input(INPUT_GET, 'query', FILTER_UNSAFE_RAW) ?: '';
$mode  = filter_input(INPUT_GET, 'mode', FILTER_UNSAFE_RAW) ?: 'nome';

header('Content-Type: application/json; charset=UTF-8');
if(empty($query)) {
    echo json_encode([]);
    exit();
}

// Determine SQL based on mode
if($mode === 'matricula') {
    $sql = "SELECT * FROM professores WHERE matricula LIKE ? LIMIT 1";
} else {
    $sql = "SELECT * FROM professores WHERE nome LIKE ? LIMIT 1";
}

$stmt = $db->prepare($sql);
$param = "%{$query}%";
$stmt->bindParam(1, $param);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row) {
    // Return only fields needed for auto-fill
    echo json_encode([
        'id'             => $row['id'],
        'matricula'      => $row['matricula'],
        'nome'           => $row['nome'],
        'cargo'          => $row['cargo'],
        'instituicao'    => $row['instituicao'],
        'classificacao'  => $row['classificacao'],
        'carga_horaria'  => $row['carga_horaria'],
    ]);
} else {
    echo json_encode([]);
}
