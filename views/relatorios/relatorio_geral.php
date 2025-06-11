<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: ../../index.php"); exit(); }
include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) { header("Location: ../expiration.php"); exit(); }

$page_title = "Relatório Geral";
$base_url = "../..";
include_once '../layouts/header.php';

// consulta relatorio geral
$query = "SELECT f.id, f.curso, f.ano, f.turma, f.inicio, f.termino, f.situacao, COUNT(m.id) AS total_estudantes
          FROM formacoes f
          LEFT JOIN matriculas m ON f.id = m.formacao_id
          GROUP BY f.id, f.curso, f.ano, f.turma, f.inicio, f.termino, f.situacao
          ORDER BY f.inicio DESC";
$stmt = $db->prepare($query);
$stmt->execute();
?>
<div class="container mt-4">
    <h2><i class="fas fa-file-alt"></i> Relatório Geral</h2>
    <hr>
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Curso</th><th>Ano</th><th>Turma</th><th>Início</th><th>Término</th><th>Situação</th><th>Total Estudantes</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['curso']); ?></td>
                <td><?php echo htmlspecialchars($row['ano']); ?></td>
                <td><?php echo htmlspecialchars($row['turma']); ?></td>
                <td><?php echo htmlspecialchars($row['inicio']); ?></td>
                <td><?php echo htmlspecialchars($row['termino']); ?></td>
                <td><?php echo htmlspecialchars($row['situacao']); ?></td>
                <td><?php echo htmlspecialchars($row['total_estudantes']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include_once '../layouts/footer.php'; ?>