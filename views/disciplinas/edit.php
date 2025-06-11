<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['tipo'] != 'ADMINISTRADOR') {
    header('Location: ../../index.php'); exit();
}

// Include DB and model to load existing data
include_once '../../config/database.php';
include_once '../../models/disciplina.php';

date_default_timezone_set('America/Sao_Paulo');

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) : null;
if(!$id) {
    $_SESSION['error_message'] = 'ID da disciplina não especificado.';
    header('Location: index.php'); exit();
}

$disc = new Disciplina($db);
$disc->id = $id;
if(!$disc->readOne()) {
    $_SESSION['error_message'] = 'Disciplina não encontrada.';
    header('Location: index.php'); exit();
}

include_once '../layouts/header.php';
?>
<div class="container mt-4">
    <h2>Editar Disciplina</h2>
    <hr>
    <?php if(isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
        <li class="breadcrumb-item"><a href="index.php">Disciplinas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar Disciplina</li>
      </ol>
    </nav>
    <a href="index.php" class="btn btn-secondary mb-3" aria-label="Voltar para lista de disciplinas">Voltar</a>
    <form id="disciplina-form" action="../../controllers/disciplinas/edit.php?id=<?php echo $disc->id; ?>" method="post">
        <?php include_once __DIR__ . '/../../config/csrf.php'; echo csrf_input(); ?>
        <div class="mb-3">
            <label for="nome" class="form-label">Nome *</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($disc->nome); ?>" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($disc->descricao); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="carga_horaria" class="form-label">Carga Horária (horas) *</label>
            <input type="number" class="form-control" id="carga_horaria" name="carga_horaria" value="<?php echo htmlspecialchars($disc->carga_horaria); ?>" min="0" required>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="index.php" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>
<script>
// Validação Frontend para campos obrigatórios e números (exemplo para formulário de edição de disciplinas)
function validarFormularioDisciplinaEdit() {
    let nome = document.getElementById('nome');
    let carga_horaria = document.getElementById('carga_horaria');
    let erros = [];
    if (nome.value.trim().length < 3) erros.push('Nome da disciplina deve ter pelo menos 3 caracteres.');
    if (!carga_horaria.value.match(/^\d+$/) || parseInt(carga_horaria.value) <= 0) erros.push('Carga horária deve ser um número positivo.');
    if (erros.length > 0) {
        alert(erros.join('\n'));
        return false;
    }
    return true;
}
document.getElementById('disciplina-form')?.addEventListener('submit', function(e) {
    if (!validarFormularioDisciplinaEdit()) e.preventDefault();
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
<?php include_once '../layouts/footer.php';
