<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/professor.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

// Initialize professor object
$professor = new Professor($db);

// Get and sanitize search keyword and pagination parameters
$search_keyword = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Get professores with pagination
if (!empty($search_keyword)) {
    $totalRows = $professor->countSearch($search_keyword);
    $stmt = $professor->searchPaged($search_keyword, $perPage, $offset);
} else {
    $totalRows = $professor->countAll();
    $stmt = $professor->readPaged($perPage, $offset);
}
$totalPages = $totalRows > 0 ? ceil($totalRows / $perPage) : 1;

// Define page title and base URL
$page_title = "Professores - SISENGLISH";
$base_url = "../..";

// Include header
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2><i class="fas fa-chalkboard-teacher"></i> Professores</h2>
            <hr>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-search"></i> Buscar Professores</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="index.php" class="row g-3">
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Nome, matrícula ou CPF..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Professores</h5>
                    <?php if($_SESSION['tipo'] == 'ADMINISTRADOR'): ?>
                    <div>
                        <a href="../estudantes/create.php" class="btn btn-success btn-sm me-2">
                            <i class="fas fa-user-plus"></i> Novo Estudante
                        </a>
                        <small class="text-light">Agora os professores são primeiro cadastrados como estudantes</small>
                    </div>
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
                                    <th>Matrícula</th>
                                    <th>Nome</th>
                                    <th>Instituição</th>
                                    <th>Cargo</th>
                                    <th>E-mail</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if($stmt->rowCount() > 0) {
                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                            echo "<td>" . $row['matricula'] . "</td>";
                                            echo "<td>" . $row['nome'] . "</td>";
                                            echo "<td>" . $row['instituicao'] . "</td>";
                                            echo "<td>" . $row['cargo'] . "</td>";
                                            echo "<td>" . $row['email'] . "</td>";
                                            echo "<td>";
                                                echo "<a href='view.php?id={$row['id']}' class='btn btn-info btn-sm me-1'><i class='fas fa-eye'></i></a>";
                                                if($_SESSION['tipo'] == 'ADMINISTRADOR') {
                                                    echo "<a href='edit.php?id={$row['id']}' class='btn btn-primary btn-sm me-1'><i class='fas fa-edit'></i></a>";
                                                    echo "<a href='$base_url/controllers/professores/delete.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Tem certeza que deseja excluir este professor?');\"><i class='fas fa-trash-alt'></i></a>";
                                                }
                                            echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Nenhum professor encontrado.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                      <ul class="pagination justify-content-center mt-4">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                          <li class="page-item <?php echo ($p === $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="index.php?search=<?php echo urlencode($search_keyword); ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a>
                          </li>
                        <?php endfor; ?>
                      </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once '../layouts/footer.php';
?>