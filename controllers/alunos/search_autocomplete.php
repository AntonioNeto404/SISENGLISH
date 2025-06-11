<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/estudante.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    http_response_code(403);
    echo json_encode(['error' => 'System expired']);
    exit();
}

// Get search parameters
$query = $_GET['query'] ?? '';
$searchType = $_GET['type'] ?? 'all'; // 'matricula', 'nome', or 'all'
$limit = (int)($_GET['limit'] ?? 10);

if(empty($query) || strlen($query) < 2) {
    echo json_encode([]);
    exit();
}

try {
    $estudante = new Estudante($db);
    
    // Build search query based on type
    $sql = "SELECT id, matricula, nome, nível de inglês, forca FROM estudantes WHERE ";
    $params = [];
    
    switch($searchType) {
        case 'matricula':
            $sql .= "matricula LIKE :query";
            $params[':query'] = $query . '%';
            break;
        case 'nome':
            $sql .= "nome LIKE :query";
            $params[':query'] = '%' . $query . '%';
            break;
        default: // 'all'
            $sql .= "(matricula LIKE :query_mat OR nome LIKE :query_nome)";
            $params[':query_mat'] = $query . '%';
            $params[':query_nome'] = '%' . $query . '%';
            break;
    }
    
    $sql .= " ORDER BY nome ASC LIMIT :limit";
    
    $stmt = $db->prepare($sql);
    
    // Bind parameters
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format results for autocomplete
    $formatted_results = [];
    foreach($results as $row) {
        $formatted_results[] = [
            'id' => $row['id'],
            'matricula' => $row['matricula'],
            'nome' => $row['nome'],
            'nível de inglês' => $row['nível de inglês'],
            'forca' => $row['forca'],
            'display_text' => $row['matricula'] . ' - ' . $row['nome'] . ' (' . $row['nível de inglês'] . ')',
            'search_text' => $row['matricula'] . ' ' . $row['nome']
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($formatted_results);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
