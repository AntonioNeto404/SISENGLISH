<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php"); exit();
}
// Include dependencies
include_once '../../config/database.php';
include_once '../../models/professor.php';

$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) { header("Location: ../expiration.php"); exit(); }

$id = isset($_GET['id']) ? $_GET['id'] : die("Erro: ID do professor não especificado.");
$professor = new Professor($db);
$professor->id = $id;
if(!$professor->readOne()) {
    die("Professor não encontrado.");
}

$page_title = "Detalhes Professor - SISENGLISH";
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <h2><i class="fas fa-chalkboard-teacher"></i> Detalhes do Professor</h2>
    <hr>
    <dl class="row">
        <dt class="col-sm-3">Nome</dt><dd class="col-sm-9"><?php echo htmlspecialchars($professor->nome); ?></dd>
        <dt class="col-sm-3">Matrícula</dt><dd class="col-sm-9"><?php echo htmlspecialchars($professor->matricula); ?></dd>
        <dt class="col-sm-3">CPF</dt><dd class="col-sm-9"><?php echo htmlspecialchars($professor->cpf); ?></dd>
        <dt class="col-sm-3">E-mail</dt><dd class="col-sm-9"><?php echo htmlspecialchars($professor->email); ?></dd>
        <dt class="col-sm-3">Instituição</dt><dd class="col-sm-9"><?php echo htmlspecialchars($professor->instituicao); ?></dd>
        <dt class="col-sm-3">Cargo</dt><dd class="col-sm-9"><?php echo htmlspecialchars($professor->cargo); ?></dd>
        <dt class="col-sm-3">Situação</dt><dd class="col-sm-9"><?php echo htmlspecialchars($professor->situacao); ?></dd>
        <!-- add other fields as needed -->
    </dl>
    <a href="index.php" class="btn btn-secondary">Voltar</a>
</div>

<?php include_once '../layouts/footer.php'; ?>