<?php
session_start();

// Check if user is logged in and is admin
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

if($_SESSION['tipo'] != 'ADMINISTRADOR') {
    header("Location: ../dashboard.php");
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
$page_title = "Novo Estudante - SISENGLISH";
$base_url = "../..";

// Include header
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2><i class="fas fa-user-plus"></i> Novo Estudante</h2>
            <hr>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
        <li class="breadcrumb-item"><a href="index.php">Estudantes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Novo Estudante</li>
      </ol>
    </nav>
    <a href="index.php" class="btn btn-secondary mb-3" aria-label="Voltar para lista de estudantes">Voltar</a>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detalhes do Estudante</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Check for messages
                    if(isset($_SESSION['message'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
                        unset($_SESSION['message']);
                    }
                    if(isset($_SESSION['error_message'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                        unset($_SESSION['error_message']);
                    }
                    ?>
                    <form action="../../controllers/estudantes/create.php" method="post" id="estudante-form">
                        <?php require_once __DIR__ . '/../../config/csrf.php'; echo csrf_input(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="matricula" class="form-label">Matrícula *</label>
                                <input type="text" class="form-control" id="matricula" name="matricula" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nome" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nível de inglês" class="form-label">Nível de Inglês/Função *</label>
                                <input type="text" class="form-control" id="nível de inglês" name="nível de inglês" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="forca" class="form-label">Telefone/Instituição *</label>
                                <select class="form-select" id="forca" name="forca" required>
                                    <option value="">Selecione</option>
                                    <option value="CBMPE">CBMPE</option>
                                    <option value="PMPE">PMPE</option>
                                    <option value="PCPE">PCPE</option>
                                    <option value="SDS">SDS</option>
                                    <option value="OUTRO">OUTRO</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="index.php" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validação Frontend para campos obrigatórios, matrícula, nome, nível de inglês e telefone (formulário de criação de estudantes)
function validarFormularioEstudanteCreate() {
    let nome = document.getElementById('nome');
    let matricula = document.getElementById('matricula');
    let nível de inglês = document.getElementById('nível de inglês');
    let forca = document.getElementById('forca');
    let erros = [];    if (!matricula.value.match(/^\d+$/)) erros.push('Matrícula deve conter apenas números.');
    if (nome.value.trim().length < 3) erros.push('Nome deve ter pelo menos 3 caracteres.');
    // Campo Nível de Inglês/Função é opcional - removida validação
    if (!forca.value) erros.push('Telefone/Instituição deve ser selecionada.');
    if (erros.length > 0) {
        alert(erros.join('\n'));
        return false;
    }
    return true;
}
document.getElementById('estudante-form')?.addEventListener('submit', function(e) {
    if (!validarFormularioEstudanteCreate()) e.preventDefault();
});
// Confirmação de exclusão
const formsDelete = document.querySelectorAll('form[action*="delete.php"]');
formsDelete.forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm('Tem certeza que deseja excluir este registro? Esta ação não poderá ser desfeita.')) {
            e.preventDefault();
        }
    });
});
</script>

<?php
// Include footer
include_once '../layouts/footer.php';
?>
