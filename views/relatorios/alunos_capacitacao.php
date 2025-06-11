<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: ../../index.php"); exit(); }
include_once '../../config/database.php';
include_once '../../models/curso.php';
include_once '../../models/matricula.php';

$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) { header("Location: ../expiration.php"); exit(); }

$page_title = "Estudantes por Curso de Inglês";
$base_url = "../..";
include_once '../layouts/header.php';

// Buscar todas as cursos de inglês
$cursoObj = new Curso($db);
$stmtC = $cursoObj->readAll();
$selected = $_GET['curso'] ?? '';
?>
<div class="container mt-4">
    <h2><i class="fas fa-user-graduate"></i> Estudantes por Curso de Inglês</h2>
    <hr>
    <form method="get" class="form-inline mb-3">
        <label class="mr-2">Curso de Inglês:</label>
        <select name="curso" class="form-control">
            <option value="">Selecione</option>
            <?php while($c = $stmtC->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?php echo $c['id']; ?>" <?php if($selected==$c['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars("{$c['curso']} ({$c['ano']} - Turma {$c['turma']})"); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-filter"></i> Filtrar</button>
    </form>

<?php if($selected): ?>
    <?php
    $mat = new Matricula($db);
    $mat->formacao_id = $selected;
    $stmt = $mat->getStudentsByCourse();
    ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Matrícula</th><th>Nome</th><th>Nível de Inglês</th><th>Telefone</th><th>Situação</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['matricula']); ?></td>
                <td><?php echo htmlspecialchars($row['nome']); ?></td>
                <td><?php echo htmlspecialchars($row['nível de inglês']); ?></td>
                <td><?php echo htmlspecialchars($row['forca']); ?></td>
                <td><?php echo htmlspecialchars($row['situacao']); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>
<?php include_once '../layouts/footer.php'; ?>