<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Include database and object files
include_once '../../config/database.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

// Define page title and base URL
$page_title = "Relatórios - SISENGLISH";
$base_url = "../..";

// Include header
include_once '../layouts/header.php';
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
    <li class="breadcrumb-item active" aria-current="page">Relatórios</li>
  </ol>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2><i class="fas fa-chart-bar"></i> Relatórios</h2>
            <p class="text-muted">Gerencie relatórios e documentos da escola de inglês</p>
            <hr>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-book"></i> Relatórios de Cursos</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <a href="cursos_periodo.php" class="text-decoration-none">
                                <i class="fas fa-calendar-alt"></i> Cursos de Inglês por Período
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="cursos_tipo.php" class="text-decoration-none">
                                <i class="fas fa-list-ul"></i> Cursos de Inglês por Tipo
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="cursos_situacao.php" class="text-decoration-none">
                                <i class="fas fa-tasks"></i> Cursos de Inglês por Situação
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Relatórios de Estudantes</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <a href="estudantes_forca.php" class="text-decoration-none">
                                <i class="fas fa-shield-alt"></i> Estudantes por Telefone/Instituição
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="estudantes_capacitacao.php" class="text-decoration-none">
                                <i class="fas fa-user-graduate"></i> Estudantes por Curso de Inglês
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="estudantes_historico.php" class="text-decoration-none">
                                <i class="fas fa-history"></i> Histórico de Estudantes
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-print"></i> Imprimir Documentos</h5>
                </div>
                <div class="card-body">                    <ul class="list-group">
                        <li class="list-group-item">
                            <a href="lista_presenca.php" class="text-decoration-none">
                                <i class="fas fa-clipboard-list"></i> Lista de Presença
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="certificados.php" class="text-decoration-none">
                                <i class="fas fa-certificate"></i> Certificados
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="certidao_professores.php" class="text-decoration-none">
                                <i class="fas fa-chalkboard-teacher"></i> Certidão de Docência
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="certidao_discentes.php" class="text-decoration-none">
                                <i class="fas fa-user-graduate"></i> Certidão de Discência
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="relatorio_geral.php" class="text-decoration-none">
                                <i class="fas fa-file-alt"></i> Relatório Geral
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <a href="../dashboard.php" class="btn btn-secondary mb-3" aria-label="Voltar para o painel">Voltar</a>
</div>

<?php if(isset($_SESSION['message'])): ?>
    <div class="alert alert-success" role="alert" aria-live="polite"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
<?php endif; ?>
<?php if(isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger" role="alert" aria-live="assertive"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<?php
// Include footer
include_once '../layouts/footer.php';
?>
