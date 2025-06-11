<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: ../../index.php"); exit(); }
if($_SESSION['tipo'] != 'ADMINISTRADOR') { header("Location: ../dashboard.php"); exit(); }

// Verificar se o ID do curso foi fornecido
$curso_id = isset($_GET['id']) ? $_GET['id'] : die("Erro: ID do curso não especificado.");

// Incluir arquivos necessários
include_once '../../config/database.php';
include_once '../../models/curso.php';
include_once '../../models/estudante.php';
include_once '../../config/csrf.php';

// Obter conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) { header("Location: ../expiration.php"); exit(); }

// Inicializar objetos
$curso = new Curso($db);
$estudante = new Estudante($db);

// Obter detalhes do curso
$curso->id = $curso_id;
$curso->readOne();

// Get search and pagination params
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Count and fetch not-enrolled students with pagination
if (!empty($search)) {
    $totalRows = $estudante->countSearchNotEnrolled($curso_id, $search);
    $stmt = $estudante->searchNotEnrolledPaged($curso_id, $search, $perPage, $offset);
} else {
    $totalRows = $estudante->countNotEnrolled($curso_id);
    $stmt = $estudante->readNotEnrolledPaged($curso_id, $perPage, $offset);
}
$totalPages = $totalRows > 0 ? ceil($totalRows / $perPage) : 1;

$page_title = "Matricular Estudante";
$base_url = "../..";
include_once '../layouts/header.php';
?>
<div class="container mt-4">
    <h2><i class="fas fa-user-plus"></i> Matricular Estudante</h2>
    <h5 class="text-muted"><?php echo htmlspecialchars($curso->curso . ' (' . $curso->ano . ' - Turma ' . $curso->turma . ')'); ?></h5>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Selecionar Estudante Existente</h5>
                    <a href="view.php?id=<?php echo $curso_id; ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar para o Curso
                    </a>
                </div>
                <div class="card-body">
                    <?php
                    if(isset($_SESSION['message'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
                        unset($_SESSION['message']);
                    }
                    if(isset($_SESSION['error_message'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                        unset($_SESSION['error_message']);
                    }
                    ?>
                    
                    <!-- Formulário de busca -->
                    <form method="get" action="enroll.php" class="mb-4">
                        <input type="hidden" name="id" value="<?php echo $curso_id; ?>">
                        <div class="row">
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="search" name="search" placeholder="Buscar estudante por nome ou matrícula..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Buscar</button>
                            </div>
                        </div>
                    </form>

                    <!-- Tabela de estudantes -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nome</th>
                                    <th>Nível de Inglês/Função</th>
                                    <th>Telefone/Instituição</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                        echo "<td>" . $row['matricula'] . "</td>";
                                        echo "<td>" . $row['nome'] . "</td>";
                                        echo "<td>" . $row['nível de inglês'] . "</td>";
                                        echo "<td>" . $row['forca'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='../../controllers/matriculas/create.php?curso_id={$curso_id}&estudante_id={$row['id']}' class='btn btn-success btn-sm'>";
                                            echo "<i class='fas fa-plus-circle'></i> Matricular";
                                            echo "</a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination navigation -->
                    <nav aria-label="Page navigation">
                      <ul class="pagination justify-content-center mt-3">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                          <li class="page-item <?php echo ($p === $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="enroll.php?id=<?php echo $curso_id; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a>
                          </li>
                        <?php endfor; ?>
                      </ul>
                    </nav>
                    
                    <hr class="my-4">
                    
                    <div class="card mt-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Cadastrar Novo Estudante</h5>
                        </div>
                        <div class="card-body">
                            <form action="../../controllers/estudantes/create.php?redirect=curso&curso_id=<?php echo $curso_id; ?>" method="post" id="new-estudante-form">
                                <?php echo csrf_input(); ?>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="matricula" class="form-label">Matrícula *</label>
                                        <input type="text" class="form-control" id="matricula" name="matricula" required>
                                    </div>
                                    <div class="col-md-9">
                                        <label for="nome" class="form-label">Nome Completo *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" required>
                                    </div>
                                </div>                                <div class="row mb-3">                                    <div class="col-md-6">
                                        <label for="nível de inglês" class="form-label">Nível de Inglês/Função (opcional)</label>
                                        <input type="text" class="form-control" id="nível de inglês" name="nível de inglês" placeholder="Campo opcional - pode ser preenchido depois">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="forca" class="form-label">Instituição *</label>
                                        <input type="text" class="form-control" id="forca" name="forca" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="condicao" class="form-label">Condição *</label>
                                        <select class="form-select" id="condicao" name="condicao" required>
                                            <option value="" disabled selected>Selecione uma condição</option>
                                            <option value="AGUARDANDO REG">AGUARDANDO REG</option>
                                            <option value="APROVADO">APROVADO</option>
                                            <option value="APTO">APTO</option>
                                            <option value="DESISTENTE">DESISTENTE</option>
                                            <option value="DESLIGADO">DESLIGADO</option>
                                            <option value="INAPTO">INAPTO</option>
                                            <option value="REPROVADO">REPROVADO</option>
                                            <option value="SEM CONCEITO">SEM CONCEITO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <button type="submit" class="btn btn-success">Cadastrar e Matricular</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('new-estudante-form').addEventListener('submit', function(event) {
    document.getElementById('nome').value = document.getElementById('nome').value.toUpperCase();
    
    // Converter Nível de Inglês/Função para maiúsculas se não estiver vazio
    const nível de inglêsElement = document.getElementById('nível de inglês');
    if (nível de inglêsElement.value.trim() !== '') {
        nível de inglêsElement.value = nível de inglêsElement.value.toUpperCase();
    }
    
    // Verificar se a matrícula contém apenas números
    const matricula = document.getElementById('matricula').value;
    if(!/^\d+$/.test(matricula)) {
        alert('A matrícula deve conter apenas números.');
        event.preventDefault();
    }
});
</script>
<?php include_once '../layouts/footer.php'; ?>
