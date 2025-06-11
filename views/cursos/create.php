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

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

// Define page title and base URL
$page_title = "Novo Curso de Inglês - SISENGLISH";
$base_url = "../..";

// Include header
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2><i class="fas fa-plus-circle"></i> Novo Curso de Inglês</h2>
            <hr>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detalhes do Curso</h5>
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
                    ?>                    <nav aria-label="breadcrumb">
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Cursos de Inglês</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Novo Curso</li>
                      </ol>
                    </nav>
                    <a href="index.php" class="btn btn-secondary mb-3" aria-label="Voltar para lista de cursos">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <form action="../../controllers/cursos/create.php" method="post" id="curso-form">
                        <?php require_once __DIR__ . '/../../config/csrf.php'; echo csrf_input(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="curso" class="form-label">Nome do Curso *</label>
                                <input type="text" class="form-control" id="curso" name="curso" list="cursos-list" 
                                       placeholder="Ex: Inglês Básico, Inglês para Negócios..." required>
                                <datalist id="cursos-list"></datalist>
                                <small class="form-text text-muted">Digite o nome do curso de inglês</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="ano" class="form-label">Ano *</label>
                                <input type="text" class="form-control" id="ano" name="ano" value="<?php echo date('Y'); ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="turma" class="form-label">Turma *</label>
                                <input type="text" class="form-control" id="turma" name="turma" 
                                       placeholder="Ex: A, B, 01, 02..." required>
                                <small class="form-text text-muted">Identificação da turma</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inicio" class="form-label">Data de Início *</label>
                                <input type="date" class="form-control" id="inicio" name="inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="termino" class="form-label">Data de Término *</label>
                                <input type="date" class="form-control" id="termino" name="termino" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="local" class="form-label">Local *</label>
                                <input type="text" class="form-control" id="local" name="local" required>
                            </div>                            <div class="col-md-6 mb-3">
                                <label for="situacao" class="form-label">Status do Curso *</label>
                                <select class="form-select" id="situacao" name="situacao" required>
                                    <option value="">Selecione o Status</option>
                                    <option value="EM ANDAMENTO">EM ANDAMENTO</option>
                                    <option value="CONCLUÍDO">CONCLUÍDO</option>
                                    <option value="CANCELADO">CANCELADO</option>
                                    <option value="SUSPENSO">SUSPENSO</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tipo_capacitacao" class="form-label">Nível do Curso *</label>
                                <select class="form-select" id="tipo_capacitacao" name="tipo_capacitacao" required>
                                    <option value="">Selecione o Nível</option>
                                    <option value="BÁSICO">BÁSICO</option>
                                    <option value="INTERMEDIÁRIO">INTERMEDIÁRIO</option>
                                    <option value="AVANÇADO">AVANÇADO</option>
                                    <option value="CONVERSAÇÃO">CONVERSAÇÃO</option>
                                    <option value="BUSINESS ENGLISH">BUSINESS ENGLISH</option>
                                    <option value="PREPARATÓRIO TOEFL">PREPARATÓRIO TOEFL</option>
                                    <option value="PREPARATÓRIO IELTS">PREPARATÓRIO IELTS</option>
                                    <option value="PREPARATÓRIO CAMBRIDGE">PREPARATÓRIO CAMBRIDGE</option>
                                    <option value="INGLÊS PARA CRIANÇAS">INGLÊS PARA CRIANÇAS</option>
                                    <option value="INGLÊS PARA ADOLESCENTES">INGLÊS PARA ADOLESCENTES</option>
                                    <option value="INGLÊS TÉCNICO">INGLÊS TÉCNICO</option>
                                    <option value="REFORÇO ESCOLAR">REFORÇO ESCOLAR</option>
                                </select>
                            </div>                            <div class="col-md-4 mb-3">
                                <label for="modalidade" class="form-label">Modalidade *</label>
                                <select class="form-select" id="modalidade" name="modalidade" required>
                                    <option value="">Selecione a Modalidade</option>
                                    <option value="PRESENCIAL">PRESENCIAL</option>
                                    <option value="ONLINE">ONLINE</option>
                                    <option value="HÍBRIDO">HÍBRIDO</option>
                                    <option value="SEMI-PRESENCIAL">SEMI-PRESENCIAL</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="carga_horaria" class="form-label">Carga Horária (horas) *</label>
                                <input type="number" class="form-control" id="carga_horaria" name="carga_horaria" 
                                       placeholder="Ex: 60, 120, 180..." required>
                                <small class="form-text text-muted">Total de horas do curso</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="unidade" class="form-label">Unidade/Unidade *</label>
                                <input type="text" class="form-control" id="unidade" name="unidade" 
                                       placeholder="Ex: Centro, Zona Norte..." required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="instituicao" class="form-label">Escola/Instituição *</label>
                                <input type="text" class="form-control" id="instituicao" name="instituicao" 
                                       placeholder="Nome da escola" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="municipio" class="form-label">Cidade *</label>
                                <input type="text" class="form-control" id="municipio" name="municipio" 
                                       placeholder="Cidade onde será ministrado" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="autorização" class="form-label">Autorização/Registro</label>
                                <input type="text" class="form-control" id="autorização" name="autorização" 
                                       placeholder="Ex: Autorização MEC nº...">
                                <small class="form-text text-muted">Número de autorização ou registro oficial (opcional)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="parecer" class="form-label">Observações</label>
                                <input type="text" class="form-control" id="parecer" name="parecer" 
                                       placeholder="Observações adicionais sobre o curso">
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="index.php" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validação Frontend para campos obrigatórios, datas e números (exemplo para formulário de criação de cursos)
function validarFormularioCursoCreate() {
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
    let autorização = document.getElementById('autorização');
    let parecer = document.getElementById('parecer');
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
    if (autorização.value.trim() === '') erros.push('Autorização(s) Conc. deve ser preenchida.');
    if (parecer.value.trim() === '') erros.push('Parecer Tec/ano deve ser preenchido.');
    if (erros.length > 0) {
        alert(erros.join('\n'));
        return false;
    }
    return true;
}
document.getElementById('curso-form')?.addEventListener('submit', function(e) {
    if (!validarFormularioCursoCreate()) e.preventDefault();
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
<script>
// Autocomplete for curso field
document.addEventListener('DOMContentLoaded', function() {
    const cursoInput = document.getElementById('curso');
    if (cursoInput) {
        cursoInput.addEventListener('input', function() {
            const term = this.value;
            if (term.length < 2) return;
            fetch('../../controllers/cursos/search.php?term=' + encodeURIComponent(term))
                .then(res => res.json())
                .then(names => {
                    const datalist = document.getElementById('cursos-list');
                    datalist.innerHTML = '';
                    names.forEach(name => {
                        const option = document.createElement('option');
                        option.value = name;
                        datalist.appendChild(option);
                    });
                });
        });
    }
});
</script>

<?php
// Include footer
include_once '../layouts/footer.php';
?>
