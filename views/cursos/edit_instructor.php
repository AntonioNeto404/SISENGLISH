<?php
// filepath: c:\xampp\htdocs\siscap03\views\cursos\edit_instructor.php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}
// Only admin
if($_SESSION['tipo']!='ADMINISTRADOR') {
    header("Location: ../dashboard.php");
    exit();
}

// Include database and models
include_once '../../config/database.php';
include_once '../../models/curso.php';

// Get IDs
$curso_id = filter_input(INPUT_GET, 'curso_id', FILTER_VALIDATE_INT) ?: die("Erro: ID do curso não especificado.");
$pivot_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)       ?: die("Erro: ID do professor não especificado.");

// Database connection
$database = new Database();
$db = $database->getConnection();

// Fetch course info
$curso = new Curso($db);
$curso->id = $curso_id;
$curso->readOne();

// Fetch pivot details
$stmt = $db->prepare("SELECT ci.id, ci.formacao_id, d.matricula, d.nome AS professor_nome, d.instituicao, di.nome AS disciplina_nome, di.carga_horaria, ci.nível de inglês_cargo, ci.classificacao FROM curso_professores ci JOIN professores d ON ci.professor_id=d.id JOIN disciplinas di ON ci.disciplina_id=di.id WHERE ci.id=?");
$stmt->bindParam(1, $pivot_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$row) {
    die('Professor não encontrado.');
}

// Pre-fill values
$matricula     = $row['matricula'];
$nível de inglês_cargo   = $row['nível de inglês_cargo'];
$nome          = $row['professor_nome'];
$instituicao   = $row['instituicao'];
$classificacao = $row['classificacao'];
$disciplina    = $row['disciplina_nome'];
$carga_horaria = $row['carga_horaria'];

$page_title = "Editar Professor";
include_once '../layouts/header.php';
?>
<div class="container mt-4">
    <h2><i class="fas fa-chalkboard-teacher"></i> Editar Professor</h2>
    <h5 class="text-muted"><?php echo htmlspecialchars($curso->curso.' ('.$curso->ano.' - Turma '.$curso->turma.')'); ?></h5>
    <hr>
    <form action="../../controllers/cursos/edit_instructor.php" method="post">
        <?php echo csrf_input(); ?>
        <input type="hidden" name="pivot_id" value="<?php echo $pivot_id; ?>">
        <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">

        <div class="row mb-3">
            <label for="nível de inglês_cargo" class="col-sm-2 col-form-label text-end">Nível de Inglês/cargo:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="nível de inglês_cargo" name="nível de inglês_cargo" value="<?php echo htmlspecialchars($nível de inglês_cargo); ?>">
            </div>
        </div>

        <div class="row mb-3">
            <label for="nome" class="col-sm-2 col-form-label text-end">Nome:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>">
            </div>
        </div>

        <div class="row mb-3">
            <label for="instituicao" class="col-sm-2 col-form-label text-end">Instituição:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="instituicao" name="instituicao" value="<?php echo htmlspecialchars($instituicao); ?>">
            </div>
        </div>

        <div class="row mb-3">
            <label for="classificacao" class="col-sm-2 col-form-label text-end">Classificação:</label>
            <div class="col-sm-10">
                <select class="form-select" id="classificacao" name="classificacao">
                    <option value="CONTEUDISTA" <?php echo ($classificacao=='CONTEUDISTA')?'selected':''; ?>>CONTEUDISTA</option>
                    <option value="COORDENADOR" <?php echo ($classificacao=='COORDENADOR')?'selected':''; ?>>COORDENADOR</option>
                    <option value="INSTRUTOR TITULAR" <?php echo ($classificacao=='INSTRUTOR TITULAR')?'selected':''; ?>>INSTRUTOR TITULAR</option>
                    <option value="TUTOR" <?php echo ($classificacao=='TUTOR')?'selected':''; ?>>TUTOR</option>
                    <option value="DESENHISTA DE PRODUTOS GRÁFICOS" <?php echo ($classificacao=='DESENHISTA DE PRODUTOS GRÁFICOS')?'selected':''; ?>>DESENHISTA DE PRODUTOS GRÁFICOS</option>
                    <option value="DIAGRAMADOR" <?php echo ($classificacao=='DIAGRAMADOR')?'selected':''; ?>>DIAGRAMADOR</option>
                    <option value="REVISOR" <?php echo ($classificacao=='REVISOR')?'selected':''; ?>>REVISOR</option>
                    <option value="INSTRUTOR SECUNDÁRIO" <?php echo ($classificacao=='INSTRUTOR SECUNDÁRIO')?'selected':''; ?>>INSTRUTOR SECUNDÁRIO</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label for="disciplina" class="col-sm-2 col-form-label text-end">Disciplina:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="disciplina" name="disciplina" value="<?php echo htmlspecialchars($disciplina); ?>">
            </div>
        </div>

        <div class="row mb-3">
            <label for="carga_horaria" class="col-sm-2 col-form-label text-end">Carga Hor:</label>
            <div class="col-sm-2">
                <div class="input-group">
                    <input type="number" class="form-control" id="carga_horaria" name="carga_horaria" value="<?php echo htmlspecialchars($carga_horaria); ?>">
                    <span class="input-group-text">h/aulas</span>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="matricula" class="col-sm-2 col-form-label text-end">Matrícula:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="matricula" name="matricula" value="<?php echo htmlspecialchars($matricula); ?>">
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">Atualizar Professor</button>
            <a href="view.php?id=<?php echo $curso_id; ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include_once '../layouts/footer.php'; ?>
