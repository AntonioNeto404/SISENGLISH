<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
    <li class="breadcrumb-item"><a href="index.php">Matrículas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Excluir Matrícula</li>
  </ol>
</nav>
<a href="index.php" class="btn btn-secondary mb-3" aria-label="Voltar para lista de matrículas">Voltar</a>
<form method="post" action="delete.php">
    <?php require_once __DIR__ . '/../../config/csrf.php'; echo csrf_input(); ?>
    <!-- Restante do formulário para exclusão de matrículas -->
</form>
<script>
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