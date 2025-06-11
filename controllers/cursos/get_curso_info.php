<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit();
}

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/curso.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    http_response_code(403);
    echo json_encode(['error' => 'Sistema expirado']);
    exit();
}

// Get course ID from GET parameter
$curso_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if(!$curso_id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID do curso não especificado ou inválido']);
    exit();
}

// Initialize curso object
$curso = new Curso($db);
$curso->id = $curso_id;

// Get course details
if($curso->readOne()) {
    // Return course info as JSON
    $response = [
        'success' => true,
        'data' => [
            'curso' => $curso->curso,
            'ano' => $curso->ano,
            'turma' => $curso->turma,
            'carga_horaria' => $curso->carga_horaria,
            // Use course name as default discipline name, but user can change it
            'disciplina_padrao' => $curso->curso
        ]
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Curso não encontrado'], JSON_UNESCAPED_UNICODE);
}
?>
