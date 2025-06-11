<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/estudante.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

// Initialize estudante object
$estudante = new Estudante($db);

// Get search keyword and pagination parameters
$search_keyword = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
// Decode HTML entities to properly handle accented characters
$search_keyword = html_entity_decode($search_keyword, ENT_QUOTES, 'UTF-8');
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Get students with pagination
if (!empty($search_keyword)) {
    $totalRows = $estudante->countSearch($search_keyword);
    $stmt = $estudante->searchPaged($search_keyword, $perPage, $offset);
} else {
    $totalRows = $estudante->countAll();
    $stmt = $estudante->readPaged($perPage, $offset);
}
$totalPages = ($totalRows > 0) ? ceil($totalRows / $perPage) : 1;

// Define page title and base URL
$page_title = "Estudantes - SISENGLISH";
$base_url = "../..";

// Include header
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2><i class="fas fa-user-graduate"></i> Estudantes</h2>
            <p class="text-muted">Gerencie os estudantes matriculados na escola de inglês</p>
            <hr>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-search"></i> Buscar Estudantes</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="index.php" class="row g-3" accept-charset="UTF-8">
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Digite o nome ou matrícula do estudante..." 
                                   value="<?php echo htmlspecialchars(html_entity_decode($search_keyword, ENT_QUOTES, 'UTF-8')); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Buscar
                            </button>
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
                    <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Estudantes</h5>
                    <?php if($_SESSION['tipo'] == 'ADMINISTRADOR'): ?>
                    <a href="create.php" class="btn btn-success btn-sm">
                        <i class="fas fa-user-plus"></i> Novo Estudante
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
                                    <th>Matrícula</th>
                                    <th>Nome</th>
                                    <th>Nível de Inglês/Função</th>
                                    <th>Telefone/Instituição</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>                                <?php
                                if($stmt->rowCount() > 0) {
                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['matricula']) . "</td>";
                                            echo "<td>" . html_entity_decode(htmlspecialchars($row['nome']), ENT_QUOTES, 'UTF-8') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nível de inglês']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['forca']) . "</td>";
                                            echo "<td>";
                                                echo "<a href='view.php?id={$row['id']}' class='btn btn-info btn-sm me-1'><i class='fas fa-eye'></i></a>";
                                                if($_SESSION['tipo'] == 'ADMINISTRADOR') {
                                                    echo "<a href='edit.php?id={$row['id']}' class='btn btn-primary btn-sm me-1'><i class='fas fa-edit'></i></a>";
                                                    echo "<a href='../../controllers/estudantes/delete.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Tem certeza que deseja excluir este estudante?');\"><i class='fas fa-trash-alt'></i></a>";
                                                }
                                            echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>Nenhum estudante encontrado.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation">                      <ul class="pagination justify-content-center mt-4">
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
