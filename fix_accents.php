<?php
// filepath: c:\xampp\htdocs\siscap03\fix_accents.php
// Script para corrigir registros com acentuação codificada no banco de dados

session_start();
require_once __DIR__ . '/config/bootstrap.php';

// Verificar se o usuário está logado e é administrador
if(!isset($_SESSION['user_id']) || $_SESSION['tipo'] != 'ADMINISTRADOR') {
    header("Location: index.php");
    exit();
}

// Incluir arquivos de banco de dados
include_once 'config/database.php';

// Obter conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();

// Função para decodificar entidades HTML
function decode_html_entities($string) {
    return html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Corrigir acentuação na tabela formacoes (cursos)
$sql_cursos = "SELECT id, curso, local, instituicao, municipio FROM formacoes";
$stmt_cursos = $db->prepare($sql_cursos);
$stmt_cursos->execute();

$updated_courses = 0;
while ($row = $stmt_cursos->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $curso_original = $row['curso'];
    $local_original = $row['local'];
    $instituicao_original = $row['instituicao'];
    $municipio_original = $row['municipio'];
    
    $curso_decoded = decode_html_entities($curso_original);
    $local_decoded = decode_html_entities($local_original);
    $instituicao_decoded = decode_html_entities($instituicao_original);
    $municipio_decoded = decode_html_entities($municipio_original);
    
    // Verificar se houve alteração
    if ($curso_decoded !== $curso_original || 
        $local_decoded !== $local_original || 
        $instituicao_decoded !== $instituicao_original || 
        $municipio_decoded !== $municipio_original) {
        
        $update_sql = "UPDATE formacoes SET curso = ?, local = ?, instituicao = ?, municipio = ? WHERE id = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->bindParam(1, $curso_decoded);
        $update_stmt->bindParam(2, $local_decoded);
        $update_stmt->bindParam(3, $instituicao_decoded);
        $update_stmt->bindParam(4, $municipio_decoded);
        $update_stmt->bindParam(5, $id);
        
        if ($update_stmt->execute()) {
            $updated_courses++;
        }
    }
}

// Corrigir acentuação na tabela alunos
$sql_alunos = "SELECT id, nome, posto FROM alunos";
$stmt_alunos = $db->prepare($sql_alunos);
$stmt_alunos->execute();

$updated_alunos = 0;
while ($row = $stmt_alunos->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $nome_original = $row['nome'];
    $posto_original = $row['posto'];
    
    $nome_decoded = decode_html_entities($nome_original);
    $posto_decoded = decode_html_entities($posto_original);
    
    // Verificar se houve alteração
    if ($nome_decoded !== $nome_original || $posto_decoded !== $posto_original) {
        $update_sql = "UPDATE alunos SET nome = ?, posto = ? WHERE id = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->bindParam(1, $nome_decoded);
        $update_stmt->bindParam(2, $posto_decoded);
        $update_stmt->bindParam(3, $id);
        
        if ($update_stmt->execute()) {
            $updated_alunos++;
        }
    }
}

// Corrigir acentuação na tabela docentes
$sql_docentes = "SELECT id, nome, orgao_expedidor, instituicao FROM docentes";
$stmt_docentes = $db->prepare($sql_docentes);
$stmt_docentes->execute();

$updated_docentes = 0;
while ($row = $stmt_docentes->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $nome_original = $row['nome'];
    $orgao_original = $row['orgao_expedidor'];
    $instituicao_original = $row['instituicao'];
    
    $nome_decoded = decode_html_entities($nome_original);
    $orgao_decoded = decode_html_entities($orgao_original);
    $instituicao_decoded = decode_html_entities($instituicao_original);
    
    // Verificar se houve alteração
    if ($nome_decoded !== $nome_original || 
        $orgao_decoded !== $orgao_original || 
        $instituicao_decoded !== $instituicao_original) {
        
        $update_sql = "UPDATE docentes SET nome = ?, orgao_expedidor = ?, instituicao = ? WHERE id = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->bindParam(1, $nome_decoded);
        $update_stmt->bindParam(2, $orgao_decoded);
        $update_stmt->bindParam(3, $instituicao_decoded);
        $update_stmt->bindParam(4, $id);
        
        if ($update_stmt->execute()) {
            $updated_docentes++;
        }
    }
}

// Corrigir acentuação na tabela disciplinas
$sql_disciplinas = "SELECT id, nome, descricao FROM disciplinas";
$stmt_disciplinas = $db->prepare($sql_disciplinas);
$stmt_disciplinas->execute();

$updated_disciplinas = 0;
while ($row = $stmt_disciplinas->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $nome_original = $row['nome'];
    $descricao_original = $row['descricao'];
    
    $nome_decoded = decode_html_entities($nome_original);
    $descricao_decoded = decode_html_entities($descricao_original);
    
    // Verificar se houve alteração
    if ($nome_decoded !== $nome_original || $descricao_decoded !== $descricao_original) {
        $update_sql = "UPDATE disciplinas SET nome = ?, descricao = ? WHERE id = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->bindParam(1, $nome_decoded);
        $update_stmt->bindParam(2, $descricao_decoded);
        $update_stmt->bindParam(3, $id);
        
        if ($update_stmt->execute()) {
            $updated_disciplinas++;
        }
    }
}

// Redirecionamento com mensagem de sucesso
$total_updated = $updated_courses + $updated_alunos + $updated_docentes + $updated_disciplinas;
$_SESSION['message'] = "Correção de acentuação concluída! Registros atualizados: $total_updated (Cursos: $updated_courses, Alunos: $updated_alunos, Docentes: $updated_docentes, Disciplinas: $updated_disciplinas)";
header("Location: views/dashboard.php");
exit();
?>
