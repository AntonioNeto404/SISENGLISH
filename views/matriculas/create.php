<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
    <li class="breadcrumb-item"><a href="index.php">Matrículas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Nova Matrícula</li>
  </ol>
</nav>
<a href="index.php" class="btn btn-secondary mb-3" aria-label="Voltar para lista de matrículas">Voltar</a>
<form method="post" action="create.php" id="matricula-form">
    <?php require_once __DIR__ . '/../../config/csrf.php'; echo csrf_input(); ?>
    <!-- Restante do formulário -->
</form>
<script>
// Validação Frontend para campos obrigatórios e números (exemplo para formulário de criação de matrículas)
function validarFormularioMatriculaCreate() {
    let formacao_id = document.getElementById('formacao_id');
    let estudante_id = document.getElementById('estudante_id');
    let situacao = document.getElementById('situacao');
    let erros = [];
    if (!formacao_id.value.match(/^\d+$/)) erros.push('ID da curso de inglês inválido.');
    if (!estudante_id.value.match(/^\d+$/)) erros.push('ID do estudante inválido.');
    if (!situacao.value) erros.push('Situação deve ser informada.');
    if (erros.length > 0) {
        alert(erros.join('\n'));
        return false;
    }
    return true;
}
document.getElementById('matricula-form')?.addEventListener('submit', function(e) {
    if (!validarFormularioMatriculaCreate()) e.preventDefault();
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