<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php"); exit();
}
include_once '../../config/database.php';
include_once '../../models/estudante.php';

$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) { header("Location: ../expiration.php"); exit(); }

$page_title = "Estudantes por Telefone/Instituição";
$base_url = "../..";
include_once '../layouts/header.php';

// obter telefones distintas
$forces = [];
$query = "SELECT DISTINCT forca FROM estudantes ORDER BY forca";
$stmt = $db->prepare($query);
$stmt->execute();
$forces = $stmt->fetchAll(PDO::FETCH_COLUMN);
$selected = $_GET['forca'] ?? '';
?>
<div class="container mt-4">
    <h2><i class="fas fa-shield-alt"></i> Estudantes por Telefone/Instituição</h2>
    <hr>
    <form method="get" class="form-inline mb-3">
        <label class="mr-2">Telefone/Instituição:</label>
        <select name="forca" class="form-control">
            <option value="">Selecione</option>
            <?php foreach($forces as $forca): ?>
                <option value="<?php echo htmlspecialchars($forca); ?>" <?php if($forca===$selected) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($forca); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-filter"></i> Filtrar</button>
    </form>

<?php if($selected): ?>
    <?php
        $query = "SELECT a.matricula, a.nome, a.nível de inglês, a.forca, f.curso, f.ano, f.turma, f.inicio, f.termino
                  FROM matriculas m
                  JOIN estudantes a ON m.estudante_id = a.id
                  JOIN formacoes f ON m.formacao_id = f.id
                  WHERE a.forca = :forca
                  ORDER BY a.nome";
        $stmt2 = $db->prepare($query);
        $stmt2->bindParam(':forca', $selected);
        $stmt2->execute();
    ?>
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Matricula</th><th>Nome</th><th>Nível de Inglês</th><th>Telefone</th>
                <th>Curso</th><th>Ano</th><th>Turma</th><th>Início</th><th>Término</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $stmt2->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['matricula']); ?></td>
                <td><?php echo htmlspecialchars($row['nome']); ?></td>
                <td><?php echo htmlspecialchars($row['nível de inglês']); ?></td>
                <td><?php echo htmlspecialchars($row['forca']); ?></td>
                <td><?php echo htmlspecialchars($row['curso']); ?></td>
                <td><?php echo htmlspecialchars($row['ano']); ?></td>
                <td><?php echo htmlspecialchars($row['turma']); ?></td>
                <td><?php echo htmlspecialchars($row['inicio']); ?></td>
                <td><?php echo htmlspecialchars($row['termino']); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>
<?php include_once '../layouts/footer.php'; ?>
