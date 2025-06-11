<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

include_once '../../config/database.php';
include_once '../../models/curso.php';

$database = new Database();
$db = $database->getConnection();

if($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

$page_title = "Cursos de Inglês por Período";
$base_url = "../..";
include_once '../layouts/header.php';

$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';
?>

<div class="container mt-4">
    <h2><i class="fas fa-calendar-alt"></i> Cursos de Inglês por Período</h2>
    <hr>
    <form method="get" class="form-inline">
        <div class="form-group mb-2">
            <label class="mr-2">Início:</label>
            <input type="date" name="start" class="form-control" value="<?php echo htmlspecialchars($start); ?>">
        </div>
        <div class="form-group mb-2 ml-3">
            <label class="mr-2">Fim:</label>
            <input type="date" name="end" class="form-control" value="<?php echo htmlspecialchars($end); ?>">
        </div>
        <button type="submit" class="btn btn-primary mb-2 ml-3"><i class="fas fa-filter"></i> Filtrar</button>
    </form>

<?php if($start && $end): ?>
    <?php
    $curso = new Curso($db);
    $stmt = $curso->filterByPeriodo($start, $end);
    ?>
    <table class="table table-bordered mt-3">
        <thead class="thead-light">
            <tr>
                <th>Curso</th>
                <th>Ano</th>
                <th>Turma</th>
                <th>Início</th>
                <th>Término</th>
                <th>Local</th>
                <th>Situação</th>
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
                <td><?php echo htmlspecialchars($row['local']); ?></td>
                <td><?php echo htmlspecialchars($row['situacao']); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>

<?php include_once '../layouts/footer.php'; ?>