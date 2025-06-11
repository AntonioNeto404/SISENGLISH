<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: ../../index.php"); exit(); }
include_once '../../config/database.php';
include_once '../../models/curso.php';

$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) { header("Location: ../expiration.php"); exit(); }

$page_title = "Cursos de Inglês por Situação";
$base_url = "../..";
include_once '../layouts/header.php';

// obter situações distintas
$situacoes = [];
$query = "SELECT DISTINCT situacao FROM formacoes ORDER BY situacao";
$stmt = $db->prepare($query);
$stmt->execute();
$situacoes = $stmt->fetchAll(PDO::FETCH_COLUMN);
$selected = $_GET['situacao'] ?? '';
?>
<div class="container mt-4">
    <h2><i class="fas fa-tasks"></i> Cursos de Inglês por Situação</h2>
    <hr>
    <form method="get" class="form-inline mb-3">
        <label class="mr-2">Situação:</label>
        <select name="situacao" class="form-control">
            <option value="">Selecione</option>
            <?php foreach($situacoes as $sit): ?>
                <option value="<?php echo htmlspecialchars($sit); ?>" <?php if($sit===$selected) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($sit); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-filter"></i> Filtrar</button>
    </form>

<?php if($selected): ?>
    <?php
    $curso = new Curso($db);
    $stmt = $curso->filter(null, null, null, $selected);
    ?>
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr><th>Curso</th><th>Ano</th><th>Turma</th><th>Início</th><th>Término</th><th>Situação</th></tr>
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
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>
<?php include_once '../layouts/footer.php'; ?>