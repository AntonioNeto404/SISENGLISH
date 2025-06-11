<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/curso.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

// Initialize curso object
$curso = new Curso($db);

// Get and sanitize filter parameters and pagination
$filter_curso    = filter_input(INPUT_GET, 'curso', FILTER_UNSAFE_RAW) ?: '';
$filter_ano      = filter_input(INPUT_GET, 'ano', FILTER_UNSAFE_RAW) ?: '';
$filter_turma    = filter_input(INPUT_GET, 'turma', FILTER_UNSAFE_RAW) ?: '';
$filter_situacao = filter_input(INPUT_GET, 'situacao', FILTER_UNSAFE_RAW) ?: '';
$page            = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$perPage         = 10;
$offset          = ($page - 1) * $perPage;

// Get total and paged results
$totalRows = $curso->countFilter($filter_curso, $filter_ano, $filter_turma, $filter_situacao);
$stmt      = $curso->filterPaged($filter_curso, $filter_ano, $filter_turma, $filter_situacao, $perPage, $offset);
$totalPages = $totalRows > 0 ? ceil($totalRows / $perPage) : 1;

// Define page title and base URL
$page_title = "Cursos de Inglês - SISENGLISH";
$base_url = "../..";

// Include header
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2><i class="fas fa-book"></i> Cursos de Inglês</h2>
            <hr>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Busca</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="index.php">
                        <div class="row">                            <div class="col-md-3 mb-3">
                                <label for="curso" class="form-label">Curso</label>
                                <input type="text" class="form-control" id="curso" name="curso" value="<?php echo $filter_curso; ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="ano" class="form-label">Ano</label>
                                <input type="text" class="form-control" id="ano" name="ano" value="<?php echo htmlspecialchars($filter_ano); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="turma" class="form-label">Turma</label>
                                <input type="text" class="form-control" id="turma" name="turma" value="<?php echo htmlspecialchars($filter_turma); ?>">
                            </div>                            <div class="col-md-3 mb-3">
                                <label for="situacao" class="form-label">Status do Curso</label>
                                <select class="form-select" id="situacao" name="situacao">
                                    <option value="">Todos os Status</option>
                                    <option value="EM ANDAMENTO" <?php echo ($filter_situacao == 'EM ANDAMENTO') ? 'selected' : ''; ?>>EM ANDAMENTO</option>
                                    <option value="CONCLUÍDO" <?php echo ($filter_situacao == 'CONCLUÍDO') ? 'selected' : ''; ?>>CONCLUÍDO</option>
                                    <option value="CANCELADO" <?php echo ($filter_situacao == 'CANCELADO') ? 'selected' : ''; ?>>CANCELADO</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary me-md-2">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-eraser"></i> Limpar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Cursos de Inglês</h5>
                    <?php if($_SESSION['tipo'] == 'ADMINISTRADOR'): ?>
                    <a href="create.php" class="btn btn-success btn-sm">
                        <i class="fas fa-plus-circle"></i> Cadastrar nova Curso
                    </a>
                    <?php endif; ?>
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
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th>Ano</th>
                                    <th>Turma</th>
                                    <th>Início</th>
                                    <th>Término</th>
                                    <th>Local</th>
                                    <th>Situação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if($stmt->rowCount() > 0) {
                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                            echo "<td>" . $row['curso'] . "</td>";
                                            echo "<td>" . $row['ano'] . "</td>";
                                            echo "<td>" . $row['turma'] . "</td>";
                                            echo "<td>" . date("d/m/Y", strtotime($row['inicio'])) . "</td>";
                                            echo "<td>" . date("d/m/Y", strtotime($row['termino'])) . "</td>";
                                            echo "<td>" . $row['local'] . "</td>";
                                            echo "<td>" . $row['situacao'] . "</td>";
                                            echo "<td>";
                                                echo "<a href='view.php?id={$row['id']}' class='btn btn-info btn-sm me-1'><i class='fas fa-eye'></i></a>";
                                                if($_SESSION['tipo'] == 'ADMINISTRADOR') {
                                                    echo "<a href='edit.php?id={$row['id']}' class='btn btn-primary btn-sm me-1'><i class='fas fa-edit'></i></a>";
                                                    echo "<a href='enroll.php?id={$row['id']}' class='btn btn-success btn-sm'><i class='fas fa-user-plus'></i> Estudante</a>";
                                                    echo "<a href='add_instructor.php?id={$row['id']}' class='btn btn-warning btn-sm'><i class='fas fa-chalkboard-teacher'></i> Professor</a>";
                                                    echo "<a href='$base_url/controllers/cursos/delete.php?id={$row['id']}' class='btn btn-danger btn-sm ms-1' onclick=\"return confirm('Tem certeza que deseja excluir esta curso de inglês?');\"><i class='fas fa-trash-alt'></i></a>";
                                                }
                                            echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center'>Nenhuma curso de inglês encontrada.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                      <ul class="pagination justify-content-center mt-3">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                          <li class="page-item <?php echo ($p === $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="index.php?curso=<?php echo urlencode($filter_curso); ?>&ano=<?php echo urlencode($filter_ano); ?>&turma=<?php echo urlencode($filter_turma); ?>&situacao=<?php echo urlencode($filter_situacao); ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a>
                          </li>
                        <?php endfor; ?>
                      </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for adding instructor -->
<div class="modal fade" id="addInstructorModal" tabindex="-1" aria-labelledby="addInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addInstructorModalLabel">Adicionar Professor à Disciplina</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="add_instructor.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="instructor" class="form-label">Professor</label>
                        <select class="form-select" id="instructor" name="instructor" required>
                            <option value="">Selecione um professor</option>
                            <!-- Options dynamically populated from the database -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="discipline" class="form-label">Disciplina</label>
                        <select class="form-select" id="discipline" name="discipline" required>
                            <option value="">Selecione uma disciplina</option>
                            <!-- Options dynamically populated from the database -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php
// Include footer
include_once '../layouts/footer.php';
?>
