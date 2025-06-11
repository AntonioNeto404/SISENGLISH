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
include_once '../../models/matricula.php';
include_once '../../models/curso_professor.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

// Get ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : die("Erro: ID não especificado.");

// Initialize objects
$curso = new Curso($db);
$matricula = new Matricula($db);

// Set IDs
$curso->id = $id;
$matricula->formacao_id = $id;

// Get curso details
$curso->readOne();

// Get enrolled students
$stmt_students = $matricula->getStudentsByCourse();
$stmt_instr = (new CursoProfessor($db))->readByCourse($id);

// Define page title and base URL
$page_title = $curso->curso . " " . $curso->ano . " - SISENGLISH";
$base_url = "../..";

// Include header
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
                    <li class="breadcrumb-item"><a href="index.php">Cursos de Inglês</a></li>
                    <li class="breadcrumb-item active"><?php echo $curso->curso . " " . $curso->ano; ?></li>
                </ol>
            </nav>
            <h2><i class="fas fa-graduation-cap"></i> <?php echo $curso->curso; ?></h2>
            <h5 class="text-muted"><?php echo $curso->ano . " - Turma " . $curso->turma; ?></h5>
            <hr>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Detalhes da Curso de Inglês</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Curso:</strong> <?php echo $curso->curso; ?></p>
                            <p><strong>Ano:</strong> <?php echo $curso->ano; ?></p>
                            <p><strong>Turma:</strong> <?php echo $curso->turma; ?></p>
                            <p><strong>Local:</strong> <?php echo $curso->local; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Data de Início:</strong> <?php echo date("d/m/Y", strtotime($curso->inicio)); ?></p>
                            <p><strong>Data de Término:</strong> <?php echo date("d/m/Y", strtotime($curso->termino)); ?></p>
                            <p><strong>Situação:</strong> <?php echo $curso->situacao; ?></p>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="index.php" class="btn btn-secondary me-md-2">Voltar</a>
                        <?php if($_SESSION['tipo'] == 'ADMINISTRADOR'): ?>
                        <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-primary me-md-2">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="enroll.php?id=<?php echo $id; ?>" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Matricular Estudantes
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Estudantes Matriculados</h5>
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
                                    <th>Situação</th>
                                    <?php if($_SESSION['tipo'] == 'ADMINISTRADOR'): ?>
                                    <th>Ações</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if($stmt_students->rowCount() > 0) {
                                    while($row = $stmt_students->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                            echo "<td>" . $row['matricula'] . "</td>";
                                            echo "<td>" . $row['nome'] . "</td>";
                                            echo "<td>" . $row['nível de inglês'] . "</td>";
                                            echo "<td>" . $row['forca'] . "</td>";
                                            echo "<td>" . $row['situacao'] . "</td>";
                                            if($_SESSION['tipo'] == 'ADMINISTRADOR') {
                                                echo "<td>";
                                                    echo "<a href='../estudantes/view.php?id={$row['estudante_id']}' class='btn btn-info btn-sm me-1'>";
                                                    echo "<i class='fas fa-eye'></i>";
                                                    echo "</a>";
                                                    echo "<a href='../../controllers/matriculas/update_status.php?id={$row['id']}&status=" . ($row['situacao'] == 'ATIVO' ? 'INATIVO' : 'ATIVO') . "&curso_id={$id}' class='btn " . ($row['situacao'] == 'ATIVO' ? 'btn-warning' : 'btn-success') . " btn-sm me-1'>";
                                                    echo "<i class='fas fa-" . ($row['situacao'] == 'ATIVO' ? 'times' : 'check') . "'></i>";
                                                    echo "</a>";
                                                    echo "<a href='../../controllers/matriculas/delete.php?id={$row['id']}&curso_id={$id}' class='btn btn-danger btn-sm' onclick='return confirm(\"Tem certeza que deseja remover este estudante da curso de inglês?\")'>";
                                                    echo "<i class='fas fa-trash'></i>";
                                                    echo "</a>";
                                                echo "</td>";
                                            }
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='" . ($_SESSION['tipo'] == 'ADMINISTRADOR' ? '6' : '5') . "' class='text-center'>Nenhum estudante matriculado.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Linked Instructors -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chalkboard-teacher"></i> Professores Vinculados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Professor</th>
                                    <th>Disciplina</th>
                                    <th>Vinculado em</th>
                                    <?php if($_SESSION['tipo']=='ADMINISTRADOR'): ?><th>Ações</th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($stmt_instr->rowCount()>0) {
                                    while($rowInstr = $stmt_instr->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<tr>';
                                        echo '<td>'.htmlspecialchars($rowInstr['professor']).'</td>';
                                        echo '<td>'.htmlspecialchars($rowInstr['disciplina']).'</td>';
                                        echo '<td>'.date('d/m/Y H:i', strtotime($rowInstr['created_at'])).'</td>';
                                        if($_SESSION['tipo']=='ADMINISTRADOR') {
                                            echo '<td>';
                                            echo '<a href="edit_instructor.php?id='.$rowInstr['id'].'&curso_id='.$id.'" class="btn btn-primary btn-sm me-1"><i class="fas fa-edit"></i></a>';
                                            echo '<a href="../../controllers/cursos/delete_instructor.php?id='.$rowInstr['id'].'&curso_id='.$id.'" ';
                                            echo 'class="btn btn-danger btn-sm" onclick="return confirm(\'Remover professor vinculado?\')">';
                                            echo '<i class="fas fa-trash-alt"></i>';
                                            echo '</a>';
                                            echo '</td>';
                                        }
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="'.(($_SESSION['tipo']=='ADMINISTRADOR')?4:3).'" class="text-center">Nenhum professor vinculado.</td></tr>';
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once '../layouts/footer.php';
?>
