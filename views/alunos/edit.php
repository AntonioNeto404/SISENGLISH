<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php"); exit();
}
if($_SESSION['tipo'] != 'ADMINISTRADOR') {
    header("Location: ../dashboard.php"); exit();
}

include_once '../../config/database.php';
include_once '../../models/estudante.php';

$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) {
    header("Location: ../expiration.php"); exit();
}

// Get and load estudante
$id = isset($_GET['id']) ? $_GET['id'] : die("Erro: ID do estudante não especificado.");
$estudante = new Estudante($db);
$estudante->id = $id;
if(!$estudante->readOne()) {
    die("Estudante não encontrado.");
}

$page_title = "Editar Estudante - SISENGLISH";
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
        <li class="breadcrumb-item"><a href="index.php">Estudantes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar Estudante</li>
      </ol>
    </nav>
    <a href="index.php" class="btn btn-secondary mb-3" aria-label="Voltar para lista de estudantes">Voltar</a>
    <h2><i class="fas fa-edit"></i> Editar Estudante</h2>
    <hr>
    <form action="../../controllers/estudantes/edit.php?id=<?php echo $id; ?>" method="post" id="estudante-form">
        <?php require_once __DIR__ . '/../../config/csrf.php'; echo csrf_input(); ?>
        <div class="mb-3">
            <label for="matricula" class="form-label">Matrícula *</label>
            <input type="text" class="form-control" id="matricula" name="matricula" value="<?php echo htmlspecialchars($estudante->matricula); ?>" required>
        </div>
        <div class="mb-3">
            <label for="nome" class="form-label">Nome Completo *</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($estudante->nome); ?>" required>
        </div>
        <div class="mb-3">
            <label for="nível de inglês" class="form-label">Nível de Inglês/Função *</label>
            <input type="text" class="form-control" id="nível de inglês" name="nível de inglês" value="<?php echo htmlspecialchars($estudante->nível de inglês); ?>" required>
        </div>
        <div class="mb-3">
            <label for="forca" class="form-label">Instituição *</label>
            <input type="text" class="form-control" id="forca" name="forca" value="<?php echo htmlspecialchars($estudante->forca); ?>" required>
        </div>
        <div class="mb-3">
            <label for="condicao" class="form-label">Condição *</label>
            <select class="form-select" id="condicao" name="condicao" required>
                <option value="" disabled selected>Selecione a condição</option>
                <?php $condicoes = ['AGUARDANDO REG','APROVADO','APTO','DESISTENTE','DESLIGADO','INAPTO','REPROVADO','SEM CONCEITO'];
                foreach($condicoes as $opt): ?>
                    <option value="<?php echo $opt; ?>"><?php echo $opt; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
</div>

<script>
// Validação Frontend para campos obrigatórios, e-mail, CPF e datas (exemplo para formulário de edição de estudantes)
function validarFormularioEstudanteEdit() {
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
    if (!validarFormularioEstudanteEdit()) e.preventDefault();
});
</script>

<?php include_once '../layouts/footer.php'; ?>