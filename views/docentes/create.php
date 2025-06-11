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

// Redirect to index - este formulário foi desativado
$_SESSION['message'] = "O cadastro direto de professores foi desativado. Agora os professores devem ser primeiro cadastrados como estudantes e depois vinculados como professores nas disciplinas.";
header("Location: index.php");
exit();

// Include database and object files
include_once '../../config/database.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

// Define page title and base URL
$page_title = "Cadastro de Professores - SISENGLISH";
$base_url = "../..";

// Include header
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2><i class="fas fa-chalkboard-teacher"></i> Cadastro de Professores</h2>
            <hr>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Cadastro de Especialistas</h5>
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
                    <nav aria-label="breadcrumb">
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Início</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Professores</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Novo Professor</li>
                      </ol>
                    </nav>
                    <a href="index.php" class="btn btn-secondary mb-3" aria-label="Voltar para lista de professores">Voltar</a>
                    <form action="../../controllers/professores/create.php" method="post" id="professor-form">
                        <?php require_once __DIR__ . '/../../config/csrf.php'; echo csrf_input(); ?>
                        <!-- Situação -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="situacao" class="form-label">Situação *</label>
                                <select class="form-select" id="situacao" name="situacao" required>
                                    <option value="1º Cadastro" selected>1º Cadastro</option>
                                    <option value="Complemento de dados cadastrais">Complemento de dados cadastrais</option>
                                </select>
                            </div>
                        </div>

                        <h4 class="mt-4 mb-3">I - Identificação pessoal</h4>

                        <!-- Nome e CPF -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label">Nome completo *</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cpf" class="form-label">CPF *</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" placeholder="000.000.000-00" required>
                            </div>
                        </div>

                        <!-- Matrícula e RG -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="matricula" class="form-label">Matrícula *</label>
                                <input type="text" class="form-control" id="matricula" name="matricula" required>
                            </div>
                            <div class="col-md-6">
                                <label for="rg" class="form-label">Registro Geral/Órgão Expedidor/UF</label>
                                <input type="text" class="form-control" id="rg" name="rg">
                            </div>
                        </div>

                        <!-- Lattes e Email -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="lattes" class="form-label">Endereço do Currículo Lattes *</label>
                                <input type="text" class="form-control" id="lattes" name="lattes" placeholder="Exemplo: lattes.cnpq.br/5341547826537994" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <!-- Telefones -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="fone_residencial" class="form-label">Fone residencial</label>
                                <input type="text" class="form-control" id="fone_residencial" name="fone_residencial">
                            </div>
                            <div class="col-md-4">
                                <label for="fone_profissional" class="form-label">Fone Profissional</label>
                                <input type="text" class="form-control" id="fone_profissional" name="fone_profissional">
                            </div>
                            <div class="col-md-4">
                                <label for="fone_celular" class="form-label">Fone Celular</label>
                                <input type="text" class="form-control" id="fone_celular" name="fone_celular">
                            </div>
                        </div>

                        <h4 class="mt-4 mb-3">II - Dados Profissionais</h4>

                        <!-- Instituição e Data de Ingresso -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="instituicao" class="form-label">Instituição</label>
                                <select class="form-select" id="instituicao" name="instituicao">
                                    <option value="">Escolher</option>
                                    <option value="Corpo de Bombeiros">Corpo de Bombeiros</option>
                                    <option value="Polícia Científica">Polícia Científica</option>
                                    <option value="Polícia Civil">Polícia Civil</option>
                                    <option value="Polícia Educacional">Polícia Educacional</option>
                                    <option value="Academia Integrada de Defesa Social">Academia Integrada de Defesa Social</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="data_ingresso" class="form-label">Data de ingresso na Instituição</label>
                                <input type="date" class="form-control" id="data_ingresso" name="data_ingresso">
                            </div>
                        </div>

                        <!-- Cargo e Lotação -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="cargo" class="form-label">Cargo</label>
                                <input type="text" class="form-control" id="cargo" name="cargo">
                            </div>
                            <div class="col-md-4">
                                <label for="lotacao" class="form-label">Unidade de lotação</label>
                                <input type="text" class="form-control" id="lotacao" name="lotacao" placeholder="Batalhão, Delegacia, Seção, Diretoria etc">
                            </div>
                            <div class="col-md-4">
                                <label for="cidade_lotacao" class="form-label">Cidade de lotação</label>
                                <input type="text" class="form-control" id="cidade_lotacao" name="cidade_lotacao">
                            </div>
                        </div>

                        <h4 class="mt-4 mb-3">III - Experiência professor</h4>

                        <!-- Disciplinas -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="disciplinas_professor" class="form-label">Disciplina(s) habilitado a ministrar como INSTRUTOR</label>
                                <textarea class="form-control" id="disciplinas_professor" name="disciplinas_professor" rows="3" placeholder="No caso de mais de uma, separar por ítens (Ex.: 1)...; 2)...)"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="disciplinas_conteudista" class="form-label">Disciplina(s) habilitado a trabalhar como CONTEUDISTA</label>
                                <textarea class="form-control" id="disciplinas_conteudista" name="disciplinas_conteudista" rows="3"></textarea>
                            </div>
                        </div>

                        <h4 class="mt-4 mb-3">Curso Acadêmica</h4>

                        <!-- Curso Tecnológica -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="formacao_tecnologica_1" class="form-label">1.1. Superior de Tecnologia (Tecnólogo)</label>
                                <input type="text" class="form-control" id="formacao_tecnologica_1" name="formacao_tecnologica_1">
                            </div>
                            <div class="col-md-6">
                                <label for="formacao_tecnologica_2" class="form-label">1.1. 2º Superior de Tecnologia (Tecnólogo)</label>
                                <input type="text" class="form-control" id="formacao_tecnologica_2" name="formacao_tecnologica_2">
                            </div>
                        </div>

                        <!-- Graduações -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="graduacao_1" class="form-label">1.2. 1ª Graduação</label>
                                <input type="text" class="form-control" id="graduacao_1" name="graduacao_1">
                            </div>
                            <div class="col-md-4">
                                <label for="graduacao_2" class="form-label">1.2. 2ª Graduação</label>
                                <input type="text" class="form-control" id="graduacao_2" name="graduacao_2">
                            </div>
                            <div class="col-md-4">
                                <label for="graduacao_3" class="form-label">1.2. 3ª Graduação</label>
                                <input type="text" class="form-control" id="graduacao_3" name="graduacao_3">
                            </div>
                        </div>

                        <!-- Especializações -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="especializacao_gestao" class="form-label">1.3. Especializações na área de Gestão Pública, Segurança Pública, Defesa Civil ou Ensino</label>
                                <input type="text" class="form-control" id="especializacao_gestao" name="especializacao_gestao">
                            </div>
                            <div class="col-md-6">
                                <label for="especializacao_outros" class="form-label">1.4. Especialização em outras áreas</label>
                                <input type="text" class="form-control" id="especializacao_outros" name="especializacao_outros">
                            </div>
                        </div>

                        <!-- Mestrado, Doutorado, Pós-Doutorado -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="mestrado" class="form-label">1.5. Mestrado</label>
                                <input type="text" class="form-control" id="mestrado" name="mestrado">
                            </div>
                            <div class="col-md-4">
                                <label for="doutorado" class="form-label">1.6. Doutorado</label>
                                <input type="text" class="form-control" id="doutorado" name="doutorado">
                            </div>
                            <div class="col-md-4">
                                <label for="pos_doutorado" class="form-label">1.7. Pós-doutorado</label>
                                <input type="text" class="form-control" id="pos_doutorado" name="pos_doutorado">
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
// Validação Frontend para campos obrigatórios, e-mail e CPF (exemplo para formulário de criação de professores)
function validarFormularioProfessorCreate() {
    let nome = document.getElementById('nome');
    let cpf = document.getElementById('cpf');
    let matricula = document.getElementById('matricula');
    let email = document.getElementById('email');
    let erros = [];
    if (nome.value.trim().length < 3) erros.push('Nome deve ter pelo menos 3 caracteres.');
    if (!cpf.value.match(/^\d{11}$/)) erros.push('CPF deve conter 11 dígitos numéricos.');
    if (!matricula.value.match(/^\d+$/)) erros.push('Matrícula deve conter apenas números.');
    if (!email.value.match(/^\S+@\S+\.\S+$/)) erros.push('E-mail inválido.');
    if (erros.length > 0) {
        alert(erros.join('\n'));
        return false;
    }
    return true;
}
document.getElementById('professor-form')?.addEventListener('submit', function(e) {
    if (!validarFormularioProfessorCreate()) e.preventDefault();
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