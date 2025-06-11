<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php"); exit();
}
if($_SESSION['tipo'] != 'ADMINISTRADOR') {
    header("Location: ../dashboard.php"); exit();
}

include_once '../../config/database.php';
include_once '../../models/professor.php';

$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) {
    header("Location: ../expiration.php"); exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : die("Erro: ID do professor não especificado.");
$professor = new Professor($db);
$professor->id = $id;
if(!$professor->readOne()) {
    die("Professor não encontrado.");
}

$page_title = "Editar Professor - SISENGLISH";
$base_url = "../..";
include_once '../layouts/header.php';
?>
<div class="container mt-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
        <li class="breadcrumb-item"><a href="index.php">Professores</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar Professor</li>
      </ol>
    </nav>
    <a href="index.php" class="btn btn-secondary mb-3" aria-label="Voltar para lista de professores">Voltar</a>
    <h2><i class="fas fa-edit"></i> Editar Professor</h2>
    <hr>
    <form action="../../controllers/professores/edit.php?id=<?php echo $id; ?>" method="post" id="professor-form">
        <?php require_once __DIR__ . '/../../config/csrf.php'; echo csrf_input(); ?>
        <div class="row mb-3">
            <label for="nível de inglês_cargo" class="col-sm-2 col-form-label text-end">Nível de Inglês/cargo:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="nível de inglês_cargo" name="nível de inglês_cargo" value="<?php echo htmlspecialchars($professor->cargo); ?>">
            </div>
        </div>
        <div class="row mb-3">
            <label for="nome" class="col-sm-2 col-form-label text-end">Nome:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($professor->nome); ?>">
            </div>
        </div>
        <div class="row mb-3">
            <label for="instituicao" class="col-sm-2 col-form-label text-end">Instituição:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="instituicao" name="instituicao" value="<?php echo htmlspecialchars($professor->instituicao); ?>">
            </div>
        </div>
        <div class="row mb-3">
            <label for="classificacao" class="col-sm-2 col-form-label text-end">Classificação:</label>
            <div class="col-sm-10">
                <select class="form-select" id="classificacao" name="classificacao">
                    <option value="CONTEUDISTA" <?php if($professor->classificacao == 'CONTEUDISTA') echo 'selected'; ?>>CONTEUDISTA</option>
                    <option value="COORDENADOR" <?php if($professor->classificacao == 'COORDENADOR') echo 'selected'; ?>>COORDENADOR</option>
                    <option value="INSTRUTOR TITULAR" <?php if($professor->classificacao == 'INSTRUTOR TITULAR') echo 'selected'; ?>>INSTRUTOR TITULAR</option>
                    <option value="TUTOR" <?php if($professor->classificacao == 'TUTOR') echo 'selected'; ?>>TUTOR</option>
                    <option value="DESENHISTA DE PRODUTOS GRÁFICOS" <?php if($professor->classificacao == 'DESENHISTA DE PRODUTOS GRÁFICOS') echo 'selected'; ?>>DESENHISTA DE PRODUTOS GRÁFICOS</option>
                    <option value="DIAGRAMADOR" <?php if($professor->classificacao == 'DIAGRAMADOR') echo 'selected'; ?>>DIAGRAMADOR</option>
                    <option value="REVISOR" <?php if($professor->classificacao == 'REVISOR') echo 'selected'; ?>>REVISOR</option>
                    <option value="INSTRUTOR SECUNDÁRIO" <?php if($professor->classificacao == 'INSTRUTOR SECUNDÁRIO') echo 'selected'; ?>>INSTRUTOR SECUNDÁRIO</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <label for="disciplina" class="col-sm-2 col-form-label text-end">Disciplina:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="disciplina" name="disciplina" value="<?php echo htmlspecialchars($professor->disciplinas_professor); ?>">
            </div>
        </div>
        <div class="row mb-3">
            <label for="carga_horaria" class="col-sm-2 col-form-label text-end">Carga Hor:</label>
            <div class="col-sm-2">
                <div class="input-group">
                    <input type="number" class="form-control" id="carga_horaria" name="carga_horaria" value="<?php echo htmlspecialchars($professor->carga_horaria); ?>">
                    <span class="input-group-text">h/aulas</span>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label for="matricula" class="col-sm-2 col-form-label text-end">Matrícula:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="matricula" name="matricula" value="<?php echo htmlspecialchars($professor->matricula); ?>">
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
<?php include_once '../layouts/footer.php'; ?>
