<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: ../../index.php"); exit(); }
include_once '../../config/database.php';
include_once '../../models/curso.php';
include_once '../../models/matricula.php';

$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) { header("Location: ../expiration.php"); exit(); }

$page_title = "Certificados";
$base_url = "../..";
include_once '../layouts/header.php';

// buscar cursos
$cursoObj = new Curso($db);
$stmtC = $cursoObj->readAll();
$selected = $_GET['curso'] ?? '';
?>
<div class="container mt-4">
    <h2><i class="fas fa-certificate"></i> Certificados</h2>
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
        <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-certificate"></i> Gerar</button>
    </form>

<?php if($selected): ?>
    <?php
    // obter dados da curso de inglês
    $cursoObj->id = $selected;
    $cursoObj->readOne();
    // buscar estudantes
    $mat = new Matricula($db);
    $mat->formacao_id = $selected;
    $stmt = $mat->getStudentsByCourse();
    ?>
    <style>
        .certificate { page-break-after: always; padding: 50px; border: 2px solid #000; margin-bottom: 20px; }
        .certificate h3 { text-align: center; margin-bottom: 40px; }
        .certificate p { text-align: justify; }
    </style>
    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="certificate">
            <h3>Certificado de Participação</h3>
            <p>Certificamos que <strong><?php echo htmlspecialchars($row['nome']); ?></strong>, matrícula <strong><?php echo htmlspecialchars($row['matricula']); ?></strong>, lotado em <strong><?php echo htmlspecialchars($row['forca']); ?></strong>, participou da curso de inglês <strong><?php echo htmlspecialchars($cursoObj->curso); ?></strong> (Turma <?php echo htmlspecialchars($cursoObj->turma); ?>), realizada de <?php echo htmlspecialchars($cursoObj->inicio); ?> a <?php echo htmlspecialchars($cursoObj->termino); ?> no local <?php echo htmlspecialchars($cursoObj->local); ?>.</p>
            <p style="text-align:right; margin-top:60px;">Data: <?php echo date('d/m/Y'); ?></p>
        </div>
    <?php endwhile; ?>
<?php endif; ?>
</div>
<?php include_once '../layouts/footer.php'; ?>