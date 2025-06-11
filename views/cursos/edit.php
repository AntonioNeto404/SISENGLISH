<?php
session_start();

// Check if user is logged in and is admin
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}
if($_SESSION['tipo'] != 'ADMINISTRADOR') {
    header("Location: ../dashboard.php");
    exit();
}

// Include database and object files
include_once '../../config/database.php';
include_once '../../models/curso.php';

// Get database connection and check expiration
$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

// Initialize and fetch curso data
$curso = new Curso($db);
$curso->id = isset($_GET['id']) ? $_GET['id'] : die("Erro: ID da curso de inglês não especificado.");
if(!$curso->readOne()) {
    die("Erro: Curso de Inglês não encontrada.");
}

// Define page title and base URL
$page_title = "Editar Curso de Inglês - SISENGLISH";
$base_url = "../..";

// Include header
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2><i class="fas fa-edit"></i> Editar Curso de Inglês</h2>
            <hr>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
        <li class="breadcrumb-item"><a href="index.php">Cursos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar Curso de Inglês</li>
      </ol>
    </nav>
    <a href="index.php" class="btn btn-secondary mb-3" aria-label="Voltar para lista de cursos">Voltar</a>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detalhes da Curso de Inglês</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Show messages
                    if(isset($_SESSION['message'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
                        unset($_SESSION['message']);
                    }
                    if(isset($_SESSION['error_message'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                        unset($_SESSION['error_message']);
                    }
                    ?>
                    <form action="../../controllers/cursos/edit.php?id=<?php echo $curso->id; ?>" method="post" id="curso-form">
                        <?php require_once __DIR__ . '/../../config/csrf.php'; echo csrf_input(); ?>
                        <input type="hidden" name="id" value="<?php echo $curso->id; ?>">
                        <div class="row">                            <div class="col-md-6 mb-3">
                                <label for="curso" class="form-label">Nome do Curso *</label>
                                <input type="text" class="form-control" id="curso" name="curso" value="<?php echo $curso->curso; ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="ano" class="form-label">Ano *</label>
                                <input type="text" class="form-control" id="ano" name="ano" value="<?php echo htmlspecialchars($curso->ano); ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="turma" class="form-label">Turma *</label>
                                <input type="text" class="form-control" id="turma" name="turma" value="<?php echo htmlspecialchars($curso->turma); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inicio" class="form-label">Data de Início *</label>
                                <input type="date" class="form-control" id="inicio" name="inicio" value="<?php echo $curso->inicio; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="termino" class="form-label">Data de Término *</label>
                                <input type="date" class="form-control" id="termino" name="termino" value="<?php echo $curso->termino; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="local" class="form-label">Local *</label>
                                <input type="text" class="form-control" id="local" name="local" value="<?php echo htmlspecialchars($curso->local); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="situacao" class="form-label">Situação *</label>
                                <select class="form-select" id="situacao" name="situacao" required>
                                    <option value="">Selecione</option>
                                    <option value="EM ANDAMENTO" <?php echo ($curso->situacao == 'EM ANDAMENTO') ? 'selected' : ''; ?>>EM ANDAMENTO</option>
                                    <option value="CONCLUÍDO" <?php echo ($curso->situacao == 'CONCLUÍDO') ? 'selected' : ''; ?>>CONCLUÍDO</option>
                                    <option value="CANCELADO" <?php echo ($curso->situacao == 'CANCELADO') ? 'selected' : ''; ?>>CANCELADO</option>
                                </select>
                            </div>
                        </div> <!-- end situacao row -->
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="tipo_capacitacao" class="form-label">Tipo Curso de Inglês *</label>
                                <select class="form-select" id="tipo_capacitacao" name="tipo_capacitacao" required>
                                    <option value="">Selecione</option>
                                    <option value="BÁSICO" <?php echo ($curso->tipo_capacitacao == 'BÁSICO') ? 'selected' : ''; ?>>BÁSICO</option>
                                    <option value="BÁSICO CONTINUADA" <?php echo ($curso->tipo_capacitacao == 'BÁSICO CONTINUADA') ? 'selected' : ''; ?>>BÁSICO CONTINUADA</option>
                                    <option value="AVANÇADO" <?php echo ($curso->tipo_capacitacao == 'AVANÇADO') ? 'selected' : ''; ?>>AVANÇADO</option>
                                    <option value="AVANÇADO INTEGRADA" <?php echo ($curso->tipo_capacitacao == 'AVANÇADO INTEGRADA') ? 'selected' : ''; ?>>AVANÇADO INTEGRADA</option>
                                    <option value="CURSO FORA DO AMBITO SDS" <?php echo ($curso->tipo_capacitacao == 'CURSO FORA DO AMBITO SDS') ? 'selected' : ''; ?>>CURSO FORA DO ÂMBITO SDS</option>
                                    <option value="CURSO FORA DO ESTADO" <?php echo ($curso->tipo_capacitacao == 'CURSO FORA DO ESTADO') ? 'selected' : ''; ?>>CURSO FORA DO ESTADO</option>
                                    <option value="SEMINARIO ESTADUAL" <?php echo ($curso->tipo_capacitacao == 'SEMINARIO ESTADUAL') ? 'selected' : ''; ?>>PREPARATÓRIO ESTADUAL</option>
                                    <option value="SEMINARIO NACIONAL" <?php echo ($curso->tipo_capacitacao == 'SEMINARIO NACIONAL') ? 'selected' : ''; ?>>PREPARATÓRIO NACIONAL</option>
                                    <option value="SEMINARIO INTERNACIONAL" <?php echo ($curso->tipo_capacitacao == 'SEMINARIO INTERNACIONAL') ? 'selected' : ''; ?>>PREPARATÓRIO INTERNACIONAL</option>
                                    <option value="TÉCNICO PRATICA" <?php echo ($curso->tipo_capacitacao == 'TÉCNICO PRATICA') ? 'selected' : ''; ?>>TÉCNICO PRÁTICA</option>
                                    <option value="WORKSHOP" <?php echo ($curso->tipo_capacitacao == 'WORKSHOP') ? 'selected' : ''; ?>>WORKSHOP</option>
                                    <option value="CONVERSAÇÃO" <?php echo ($curso->tipo_capacitacao == 'CONVERSAÇÃO') ? 'selected' : ''; ?>>CONVERSAÇÃO</option>
                                    <option value="REUNIAO DIDATICA" <?php echo ($curso->tipo_capacitacao == 'REUNIAO DIDATICA') ? 'selected' : ''; ?>>REUNIÃO DIDÁTICA</option>
                                    <option value="NÃO DEFINIDA" <?php echo ($curso->tipo_capacitacao == 'NÃO DEFINIDA') ? 'selected' : ''; ?>>NÃO DEFINIDA</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="modalidade" class="form-label">Modalidade *</label>
                                <select class="form-select" id="modalidade" name="modalidade" required>
                                    <option value="">Selecione</option>
                                    <option value="PRESENCIAL" <?php echo ($curso->modalidade == 'PRESENCIAL') ? 'selected' : ''; ?>>PRESENCIAL</option>
                                    <option value="HÍBRIDO" <?php echo ($curso->modalidade == 'HÍBRIDO') ? 'selected' : ''; ?>>HÍBRIDO</option>
                                    <option value="ONLINE" <?php echo ($curso->modalidade == 'ONLINE') ? 'selected' : ''; ?>>ONLINE</option>
                                    <option value="NÃO DEFINIDA" <?php echo ($curso->modalidade == 'NÃO DEFINIDA') ? 'selected' : ''; ?>>NÃO DEFINIDA</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="unidade" class="form-label">Unidade *</label>
                                <input type="text" class="form-control" id="unidade" name="unidade" value="<?php echo htmlspecialchars($curso->unidade); ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="carga_horaria" class="form-label">Carga Horária *</label>
                                <input type="number" class="form-control" id="carga_horaria" name="carga_horaria" value="<?php echo htmlspecialchars($curso->carga_horaria); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="instituicao" class="form-label">Instituição *</label>
                                <input type="text" class="form-control" id="instituicao" name="instituicao" value="<?php echo htmlspecialchars($curso->instituicao); ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="municipio" class="form-label">Município</label>
                                <input type="text" class="form-control" id="municipio" name="municipio" value="<?php echo htmlspecialchars($curso->municipio); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="autorização" class="form-label">Autorização(s) Conc.</label>
                                <input type="text" class="form-control" id="autorização" name="autorização" value="<?php echo htmlspecialchars($curso->autorização); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="parecer" class="form-label">Parecer Tec/ano</label>
                                <input type="text" class="form-control" id="parecer" name="parecer" value="<?php echo htmlspecialchars($curso->parecer); ?>">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="index.php" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Atualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validação Frontend para campos obrigatórios, datas e números (exemplo para formulário de edição de cursos)
function validarFormularioCursoEdit() {
    let curso = document.getElementById('curso');
    let ano = document.getElementById('ano');
    let turma = document.getElementById('turma');
    let inicio = document.getElementById('inicio');
    let termino = document.getElementById('termino');
    let local = document.getElementById('local');
    let situacao = document.getElementById('situacao');
    let tipo_capacitacao = document.getElementById('tipo_capacitacao');
    let modalidade = document.getElementById('modalidade');
    let unidade = document.getElementById('unidade');
    let carga_horaria = document.getElementById('carga_horaria');
    let instituicao = document.getElementById('instituicao');
    let erros = [];
    if (curso.value.trim().length < 3) erros.push('Nome do curso deve ter pelo menos 3 caracteres.');
    if (!ano.value.match(/^\d{4}$/)) erros.push('Ano deve conter 4 dígitos numéricos.');
    if (turma.value.trim() === '') erros.push('Turma deve ser preenchida.');
    if (!inicio.value || !termino.value) erros.push('Datas de início e término são obrigatórias.');
    if (inicio.value && termino.value && new Date(termino.value) < new Date(inicio.value)) erros.push('A data de término não pode ser anterior à data de início.');
    if (local.value.trim() === '') erros.push('Local deve ser preenchido.');
    if (!situacao.value) erros.push('Situação deve ser selecionada.');
    if (!tipo_capacitacao.value) erros.push('Tipo de curso de inglês deve ser selecionado.');
    if (!modalidade.value) erros.push('Modalidade deve ser selecionada.');
    if (unidade.value.trim() === '') erros.push('Unidade deve ser preenchido.');
    if (!carga_horaria.value.match(/^\d+$/) || parseInt(carga_horaria.value) <= 0) erros.push('Carga horária deve ser um número positivo.');
    if (instituicao.value.trim() === '') erros.push('Instituição deve ser preenchida.');
    if (erros.length > 0) {
        alert(erros.join('\n'));
        return false;
    }
    return true;
}
document.getElementById('curso-form')?.addEventListener('submit', function(e) {
    if (!validarFormularioCursoEdit()) e.preventDefault();
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

<?php
// Include footer
include_once '../layouts/footer.php';
?>