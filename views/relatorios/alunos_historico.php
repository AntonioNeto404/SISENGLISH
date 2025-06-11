<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: ../../index.php"); exit(); }
include_once '../../config/database.php';
include_once '../../models/estudante.php';
include_once '../../models/matricula.php';

$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) { header("Location: ../expiration.php"); exit(); }

$page_title = "Histórico de Estudantes";
$base_url = "../..";
include_once '../layouts/header.php';

// buscar todos estudantes
$estudanteObj = new Estudante($db);
$stmtA = $estudanteObj->readAll();
$selected = $_GET['estudante'] ?? '';
?>
<div class="container mt-4">
    <h2><i class="fas fa-history"></i> Histórico de Estudantes</h2>
    <hr>
    <form method="get" class="form-inline mb-3">
        <label class="mr-2">Estudante:</label>
        <select name="estudante" class="form-control">
            <option value="">Selecione</option>
            <?php while($a = $stmtA->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?php echo $a['id']; ?>" <?php if($selected==$a['id']) echo 'selected';?>>
                    <?php echo htmlspecialchars("{$a['nome']} ({$a['matricula']})"); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-history"></i> Carregar Histórico</button>
    </form>

<?php if($selected): ?>
    <?php
    $mat = new Matricula($db);
    $mat->estudante_id = $selected;
    $stmt = $mat->getCoursesByStudent();
    ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Curso</th><th>Ano</th><th>Turma</th><th>Início</th><th>Término</th><th>Situação</th>
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
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>
<?php include_once '../layouts/footer.php'; ?>