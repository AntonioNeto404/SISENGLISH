<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Include database and object files
include_once '../config/database.php';
include_once '../models/curso.php';
include_once '../models/aluno.php';
include_once '../models/docente.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: expiration.php");
    exit();
}

// Initialize objects
$curso = new Curso($db);
$aluno = new Aluno($db);
$docente = new Docente($db);

// Get recent courses
$recent_courses = $curso->readAll();

// Get course count
$course_count = $recent_courses->rowCount();

// Get student count
$student_count = $aluno->readAll()->rowCount();

// Get docente count
$docente_count = $docente->readAll()->rowCount();

// Define page title
$page_title = "Painel Principal - SISENGLISH";
$base_url = "..";

// Include header
include_once 'layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2><i class="fas fa-tachometer-alt"></i> Painel Principal</h2>
            <p class="text-muted">Bem-vindo ao sistema de gestão da sua escola de inglês</p>
            <hr>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Cursos de Inglês</h6>
                            <h1 class="display-4"><?php echo $course_count; ?></h1>
                        </div>
                        <i class="fas fa-book fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="cursos/index.php" class="text-white">Ver todos os cursos</a>
                    <i class="fas fa-angle-right text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white h-100">                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Estudantes</h6>
                            <h1 class="display-4"><?php echo $student_count; ?></h1>
                        </div>
                        <i class="fas fa-user-graduate fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="estudantes/index.php" class="text-white">Ver todos os estudantes</a>
                    <i class="fas fa-angle-right text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Professores</h6>
                            <h1 class="display-4"><?php echo $professor_count; ?></h1>
                        </div>
                        <i class="fas fa-chalkboard-teacher fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="professores/index.php" class="text-white">Ver todos os professores</a>
                    <i class="fas fa-angle-right text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Relatórios</h6>
                            <h1 class="display-4"><i class="fas fa-file-alt"></i></h1>
                        </div>
                        <i class="fas fa-chart-bar fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="relatorios/" class="text-white">Ver relatórios</a>
                    <i class="fas fa-angle-right text-white"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-book-open"></i> Cursos de Inglês Recentes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nome do Curso</th>
                                    <th>Nível</th>
                                    <th>Turma</th>
                                    <th>Data de Início</th>
                                    <th>Data de Término</th>
                                    <th>Local</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 0;
                                while($row = $recent_courses->fetch(PDO::FETCH_ASSOC)) {
                                    if($count >= 5) break; // Mostrar apenas 5 cursos mais recentes
                                    
                                    echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['curso']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['ano']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['turma']) . "</td>";
                                        echo "<td>" . date("d/m/Y", strtotime($row['inicio'])) . "</td>";
                                        echo "<td>" . date("d/m/Y", strtotime($row['termino'])) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['local']) . "</td>";
                                        echo "<td><span class='badge bg-" . ($row['situacao'] == 'EM ANDAMENTO' ? 'success' : ($row['situacao'] == 'CONCLUÍDO' ? 'primary' : 'secondary')) . "'>" . $row['situacao'] . "</span></td>";
                                        echo "<td>";
                                            echo "<a href='cursos/view.php?id={$row['id']}' class='btn btn-info btn-sm me-1' title='Visualizar'><i class='fas fa-eye'></i></a>";
                                            if($_SESSION['tipo'] == 'ADMINISTRADOR') {
                                                echo "<a href='cursos/edit.php?id={$row['id']}' class='btn btn-primary btn-sm' title='Editar'><i class='fas fa-edit'></i></a>";
                                            }
                                        echo "</td>";
                                    echo "</tr>";
                                    
                                    $count++;
                                }
                                
                                if($count == 0) {
                                    echo "<tr><td colspan='8' class='text-center text-muted'>Nenhum curso cadastrado ainda.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>                    </div>
                    <div class="text-end mt-3">
                        <a href="cursos/index.php" class="btn btn-primary">
                            <i class="fas fa-list"></i> Ver todos os cursos
                        </a>
                        <?php if($_SESSION['tipo'] == 'ADMINISTRADOR'): ?>
                        <a href="cursos/create.php" class="btn btn-success">
                            <i class="fas fa-plus"></i> Novo curso
                        </a>
                        <a href="../fix_accents.php" class="btn btn-warning" onclick="return confirm('Esta operação irá corrigir problemas de acentuação nos registros. Deseja continuar?')">
                            <i class="fas fa-tools"></i> Corrigir Acentuação
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'layouts/footer.php';
?>
