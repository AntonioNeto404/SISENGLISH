<?php
session_start();
// Access control
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php"); exit();
}

// Include dependencies
include_once '../../config/database.php';
include_once '../../models/estudante.php';

// Get DB connection
$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) {
    header("Location: ../expiration.php"); exit();
}

// Fetch estudante data
$id = isset($_GET['id']) ? $_GET['id'] : die("Erro: ID do estudante não especificado.");
$estudante = new Estudante($db);
$estudante->id = $id;
if(!$estudante->readOne()) {
    die("Estudante não encontrado.");
}

// Page settings
$page_title = "Detalhes Estudante - SISENGLISH";
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <h2><i class="fas fa-user"></i> Detalhes do Estudante</h2>
    <hr>    <dl class="row">
        <dt class="col-sm-3">Matrícula</dt><dd class="col-sm-9"><?php echo htmlspecialchars($estudante->matricula); ?></dd>
        <dt class="col-sm-3">Nome</dt><dd class="col-sm-9"><?php echo htmlspecialchars($estudante->nome); ?></dd>
        <dt class="col-sm-3">Nível de Inglês/Função</dt><dd class="col-sm-9"><?php echo htmlspecialchars($estudante->nível de inglês); ?></dd>
        <dt class="col-sm-3">Telefone/Instituição</dt><dd class="col-sm-9"><?php echo htmlspecialchars($estudante->forca); ?></dd>
    </dl>    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-file-alt"></i> Certidões</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <a href="../relatorios/certidao_discentes.php?estudante=<?php echo $estudante->id; ?>" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-user-graduate"></i> Certidão de Discência
                    </a>
                </div>
                <div class="col-md-6">
                    <?php
                    // Verificar se o estudante também é professor
                    $query = "SELECT id FROM professores WHERE matricula = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(1, $estudante->matricula);
                    $stmt->execute();
                    $professor = $stmt->fetch(PDO::FETCH_ASSOC);
                    $professor_id = $professor ? $professor['id'] : null;
                    ?>
                    <a href="../relatorios/certidao_professores.php?professor=<?php echo $professor_id ? $professor_id : 0; ?>" class="btn btn-success btn-block mb-2" <?php echo !$professor_id ? 'onclick="alert(\'Este estudante não está cadastrado como professor.\'); return false;"' : ''; ?>>
                        <i class="fas fa-chalkboard-teacher"></i> Certidão de Docência
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <a href="index.php" class="btn btn-secondary">Voltar</a>
</div>

<?php include_once '../layouts/footer.php'; ?>