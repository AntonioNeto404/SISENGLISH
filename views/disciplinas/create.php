<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['tipo'] != 'ADMINISTRADOR') {
    header('Location: ../../index.php'); exit();
}
include_once '../layouts/header.php';
?>
<div class="container mt-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
        <li class="breadcrumb-item"><a href="index.php">Disciplinas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nova Disciplina</li>
      </ol>
    </nav>
    <a href="index.php" class="btn btn-secondary mb-3" aria-label="Voltar para lista de disciplinas">Voltar</a>
    <h2>Nova Disciplina</h2>
    <hr>
    <form id="disciplina-form" action="../../controllers/disciplinas/create.php" method="post">
        <?php require_once __DIR__ . '/../../config/csrf.php'; echo csrf_input(); ?>
        <div class="mb-3">
            <label for="nome" class="form-label">Nome *</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="carga_horaria" class="form-label">Carga Horária (horas) *</label>
            <input type="number" class="form-control" id="carga_horaria" name="carga_horaria" min="0" required>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>
<script>
// Validação Frontend para campos obrigatórios e números (exemplo para formulário de criação de disciplinas)
function validarFormularioDisciplinaCreate() {
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
    if (!validarFormularioDisciplinaCreate()) e.preventDefault();
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
