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
include_once '../../models/disciplina.php';
include_once '../../models/curso.php';
include_once '../../models/estudante.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if system has expired
if($database->checkExpiration()) {
    header("Location: ../expiration.php");
    exit();
}

// Get course ID from URL
$curso_id = isset($_GET['id']) ? $_GET['id'] : die("Erro: ID do curso não especificado.");

// Initialize objects
$professor = new Professor($db);
$disciplina = new Disciplina($db);
$curso = new Curso($db);
$estudante = new Estudante($db);

// Get course details
$curso->id = $curso_id;
$curso->readOne();

// Fetch professores, disciplinas and estudantes
$stmtProfessores = $professor->readAll();
$stmtDisciplinas = $disciplina->readAll();
$stmtEstudantes = $estudante->readAll();

// Define page title and base URL
$page_title = "Adicionar Professor à Disciplina";
$base_url = "../..";

// Include header
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2><i class="fas fa-chalkboard-teacher"></i> Cadastro de Professores</h2>
            <h5 class="text-muted"><?php echo htmlspecialchars($curso->curso . ' (' . $curso->ano . ' - Turma ' . $curso->turma . ')'); ?></h5>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Vincular Professor</h5>
                    <div>
                        <button type="button" id="btn-by-participant" class="btn btn-primary btn-sm me-2">Estudante Existente</button>
                        <button type="button" id="btn-by-matricula" class="btn btn-light btn-sm me-2">Por Matrícula</button>
                        <button type="button" id="btn-by-nome" class="btn btn-light btn-sm">Por Nome</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nova funcionalidade:</strong> Agora você pode vincular estudantes existentes como professores ou ainda criar novos registros usando matrícula/nome.
                    </div>
                    <?php
                    // Check for messages
                    if(isset($_SESSION['message'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
                        unset($_SESSION['message']);
                    }
                    if(isset($_SESSION['error_message'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                        unset($_SESSION['error_message']);
                    }                    ?>
                    
                    <!-- Seletor de Estudante -->
                    <div class="row mb-3" id="row-participant" style="display: block;">
                        <label for="estudante" class="col-sm-2 col-form-label text-end">Estudante:</label>
                        <div class="col-sm-10">
                            <select class="form-select" id="estudante" name="estudante">
                                <option value="">Selecione um estudante existente</option>
                                <?php 
                                $stmtEstudantes->execute();
                                while($estudante_row = $stmtEstudantes->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . $estudante_row['id'] . '" data-matricula="' . htmlspecialchars($estudante_row['matricula']) . '" data-nome="' . htmlspecialchars($estudante_row['nome']) . '" data-nível de inglês="' . htmlspecialchars($estudante_row['nível de inglês']) . '" data-forca="' . htmlspecialchars($estudante_row['forca']) . '">';
                                    echo htmlspecialchars($estudante_row['matricula'] . ' - ' . $estudante_row['nome'] . ' (' . $estudante_row['nível de inglês'] . ')');
                                    echo '</option>';
                                }
                                ?>
                            </select>
                            <div class="form-text">Selecione um estudante para promover a professor nesta disciplina.</div>
                        </div>
                    </div>
                      <form action="../../controllers/cursos/add_instructor.php" method="post">
                        <?php require_once __DIR__ . '/../../config/csrf.php'; echo csrf_input(); ?>
                        <!-- Hidden field for course ID -->
                        <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
                        <!-- Hidden field for mode -->
                        <input type="hidden" name="mode" id="mode" value="participant">
                        <!-- Hidden field for participant ID when in participant mode -->
                        <input type="hidden" name="estudante_id" id="estudante_id" value="">>
                        
                        <div class="row mb-3" id="row-buttons">
                            <div class="col-md-6 offset-md-6 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-2">Inserir Professor</button>
                                <a href="view.php?id=<?php echo $curso_id; ?>" class="btn btn-secondary">Voltar para o Curso</a>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="nível de inglês_cargo" class="col-sm-2 col-form-label text-end">Nível de Inglês/cargo:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nível de inglês_cargo" name="nível de inglês_cargo">
                            </div>
                        </div>                        <div class="row mb-3" id="row-nome">
                            <label for="nome" class="col-sm-2 col-form-label text-end">Nome:</label>
                            <div class="col-sm-10 position-relative">
                                <input type="text" class="form-control" id="nome" name="nome" autocomplete="off">
                                <div class="autocomplete-dropdown" id="nome-dropdown"></div>
                                <div class="form-text">Digite o nome para buscar estudantes existentes</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="instituicao" class="col-sm-2 col-form-label text-end">Instituição:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="instituicao" name="instituicao">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="classificacao" class="col-sm-2 col-form-label text-end">Classificação:</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="classificacao" name="classificacao">
                                    <option value="CONTEUDISTA">CONTEUDISTA</option>
                                    <option value="COORDENADOR">COORDENADOR</option>                        
                                                                        
                                    <option value="INSTRUTOR TITULAR">INSTRUTOR TITULAR</option>
                                    <option value="TUTOR">TUTOR</option>
                                    <option value="DESENHISTA DE PRODUTOS GRÁFICOS">DESENHISTA DE PRODUTOS GRÁFICOS</option>
                                    <option value="DIAGRAMADOR">DIAGRAMADOR</option>
                                    <option value="REVISOR">REVISOR</option>
                                    <option value="INSTRUTOR SECUNDÁRIO">INSTRUTOR SECUNDÁRIO</option>
                                </select>
                            </div>
                        </div>                        <!-- Select discipline -->
                        <div class="row mb-3">
                            <label for="disciplina" class="col-sm-2 col-form-label text-end">Disciplina:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="disciplina" name="disciplina" required>
                                <div class="form-text">Será pré-preenchido com o nome da curso de inglês, mas pode ser alterado conforme necessário.</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="carga_horaria" class="col-sm-2 col-form-label text-end">Carga Hor:</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="carga_horaria" name="carga_horaria">
                                    <span class="input-group-text">h/aulas</span>
                                </div>
                                <div class="form-text">Será pré-preenchido com a carga horária da curso de inglês.</div>
                            </div>                        </div>

                        <div class="row mb-3" id="row-matricula">
                            <label for="matricula" class="col-sm-2 col-form-label text-end">Matrícula:</label>
                            <div class="col-sm-10 position-relative">
                                <input type="text" class="form-control" id="matricula" name="matricula" required autocomplete="off">
                                <div class="autocomplete-dropdown" id="matricula-dropdown"></div>
                                <div class="form-text">Digite a matrícula para buscar estudantes existentes</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once '../layouts/footer.php';
?>
<!-- Auto-fill script for professor lookup and course info preload -->
<style>
.pre-filled {
    background-color: #f8f9fa !important;
    border-left: 3px solid #007bff;
}
.fade-in {
    animation: fadeIn 0.5s ease-in;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Autocomplete Styles */
.autocomplete-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.autocomplete-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.2s;
}

.autocomplete-item:hover {
    background-color: #e9ecef;
}

.autocomplete-item.active {
    background-color: #007bff;
    color: white;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-loading {
    padding: 8px 12px;
    text-align: center;
    font-style: italic;
    color: #6c757d;
}

.autocomplete-no-results {
    padding: 8px 12px;
    text-align: center;
    color: #6c757d;
    font-style: italic;
}

.highlighted-match {
    background-color: #fff3cd;
    font-weight: bold;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let mode = 'participant'; // Mudança: modo padrão agora é estudante
    const btnParticipant = document.getElementById('btn-by-participant');
    const btnMat = document.getElementById('btn-by-matricula');
    const btnNome = document.getElementById('btn-by-nome');
    const form = document.querySelector('form');
    const rowButtons = document.getElementById('row-buttons');
    const rowNome = document.getElementById('row-nome');
    const rowMat = document.getElementById('row-matricula');
    const rowParticipant = document.getElementById('row-participant');

    // Pre-load course information for discipline and workload
    loadCourseInfo();

    function reorder() {
        if(mode === 'participant') {
            // Mostrar apenas seletor de estudante
            rowParticipant.style.display = 'block';
            rowMat.style.display = 'none';
            rowNome.style.display = 'none';
        } else if(mode === 'matricula') {
            rowParticipant.style.display = 'none';
            rowMat.style.display = 'block';
            rowNome.style.display = 'block';
            form.insertBefore(rowMat, rowButtons.nextElementSibling);
            form.insertBefore(rowNome, rowMat.nextElementSibling);
        } else {
            rowParticipant.style.display = 'none';
            rowMat.style.display = 'block';
            rowNome.style.display = 'block';
            form.insertBefore(rowNome, rowButtons.nextElementSibling);
            form.insertBefore(rowMat, rowNome.nextElementSibling);
        }
    }
    reorder();    // Adicionar controle para botão de estudante
    function updateButtons() {
        btnParticipant.className = mode === 'participant' ? 'btn btn-primary btn-sm me-2' : 'btn btn-light btn-sm me-2';
        btnMat.className = mode === 'matricula' ? 'btn btn-primary btn-sm me-2' : 'btn btn-light btn-sm me-2';
        btnNome.className = mode === 'nome' ? 'btn btn-primary btn-sm' : 'btn btn-light btn-sm';
        
        // Update hidden mode field
        document.getElementById('mode').value = mode;
    }    btnParticipant.addEventListener('click', function() {
        mode = 'participant';
        updateButtons();
        reorder();
        // Clear other mode data
        document.getElementById('estudante').selectedIndex = 0;
        document.getElementById('estudante_id').value = '';
    });

    // Atualizar os event listeners existentes
    btnMat.addEventListener('click', function() {
        mode = 'matricula';
        updateButtons();
        reorder();
        document.getElementById('matricula').focus();
        // Clear participant mode data
        document.getElementById('estudante_id').value = '';
    });
    btnNome.addEventListener('click', function() {
        mode = 'nome';
        updateButtons();
        reorder();
        document.getElementById('nome').focus();
        // Clear participant mode data
        document.getElementById('estudante_id').value = '';
    });function loadCourseInfo() {
        const cursoId = <?php echo $curso_id; ?>;
        fetch(`../../controllers/cursos/get_curso_info.php?id=${cursoId}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // Pre-fill discipline field with course name (user can change if needed)
                    const disciplinaField = document.getElementById('disciplina');
                    const cargaHorariaField = document.getElementById('carga_horaria');
                      if(disciplinaField && !disciplinaField.value) {
                        disciplinaField.value = data.data.disciplina_padrao;
                        // Add visual feedback
                        disciplinaField.classList.add('pre-filled');
                        disciplinaField.setAttribute('title', 'Pré-preenchido com o nome da curso de inglês');
                    }
                    
                    if(cargaHorariaField && !cargaHorariaField.value) {
                        cargaHorariaField.value = data.data.carga_horaria;
                        // Add visual feedback
                        cargaHorariaField.classList.add('pre-filled');
                        cargaHorariaField.setAttribute('title', 'Pré-preenchido com a carga horária da curso de inglês');
                    }

                    // Show a small notification
                    showNotification('Campos disciplina e carga horária foram pré-preenchidos baseados na curso de inglês selecionada.', 'info');
                }
            })
            .catch(error => {
                console.log('Erro ao carregar incursos do curso:', error);
            });
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show mt-2`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert after the course title
        const courseTitle = document.querySelector('h5.text-muted');
        if(courseTitle) {
            courseTitle.parentNode.insertBefore(notification, courseTitle.nextSibling);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if(notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
    }    // Remove visual feedback when user starts typing
    document.getElementById('disciplina').addEventListener('input', function() {
        this.classList.remove('pre-filled');
        this.removeAttribute('title');
    });
    
    document.getElementById('carga_horaria').addEventListener('input', function() {
        this.classList.remove('pre-filled');
        this.removeAttribute('title');
    });

    function searchProfessor(val) {
        if(!val) return;
        fetch(`../../controllers/professores/search.php?query=${encodeURIComponent(val)}&mode=${mode}`)
            .then(res => res.json())
            .then(data => {
                if(data.id) {
                    document.getElementById('nível de inglês_cargo').value = data.cargo || '';
                    document.getElementById('nome').value = data.nome || '';
                    document.getElementById('instituicao').value = data.instituicao || '';
                    document.getElementById('classificacao').value = data.classificacao || '';
                    // Don't overwrite carga_horaria if already pre-filled from course
                    const cargaField = document.getElementById('carga_horaria');
                    if(!cargaField.value) {
                        cargaField.value = data.carga_horaria || '';
                    }
                    document.getElementById('matricula').value = data.matricula || '';
                }
            });
    }    document.getElementById('matricula').addEventListener('blur', function() { if(mode==='matricula') searchProfessor(this.value); });
    document.getElementById('nome').addEventListener('blur', function() { if(mode==='nome') searchProfessor(this.value); });
      // Adicionar controle para preenchimento automático quando um estudante é selecionado
    document.getElementById('estudante').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if(selectedOption.value) {
            // Set hidden field for participant ID
            document.getElementById('estudante_id').value = selectedOption.value;
            
            // Preencher campos com dados do estudante
            document.getElementById('matricula').value = selectedOption.dataset.matricula || '';
            document.getElementById('nome').value = selectedOption.dataset.nome || '';
            document.getElementById('nível de inglês_cargo').value = selectedOption.dataset.nível de inglês || '';
            document.getElementById('instituicao').value = selectedOption.dataset.forca || '';
            
            // Mostrar notificação
            showNotification('Dados do estudante preenchidos automaticamente. Ajuste conforme necessário.', 'success');
        } else {
            // Clear hidden field if no participant selected
            document.getElementById('estudante_id').value = '';
        }    });

    // ========== AUTOCOMPLETE FUNCTIONALITY ==========
    let autocompleteTimeout = null;
    let currentActiveIndex = -1;
    let currentResults = [];
    
    // Initialize autocomplete for matricula and nome fields
    setupAutocomplete('matricula', 'matricula');
    setupAutocomplete('nome', 'nome');
    
    function setupAutocomplete(fieldId, searchType) {
        const field = document.getElementById(fieldId);
        const dropdown = document.getElementById(fieldId + '-dropdown');
        
        field.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Clear previous timeout
            if (autocompleteTimeout) {
                clearTimeout(autocompleteTimeout);
            }
            
            if (query.length < 2) {
                hideDropdown(dropdown);
                return;
            }
            
            // Debounce the search
            autocompleteTimeout = setTimeout(() => {
                searchParticipants(query, searchType, dropdown, field);
            }, 300);
        });
        
        // Handle keyboard navigation
        field.addEventListener('keydown', function(e) {
            if (!dropdown.style.display || dropdown.style.display === 'none') {
                return;
            }
            
            const items = dropdown.querySelectorAll('.autocomplete-item:not(.autocomplete-loading):not(.autocomplete-no-results)');
            
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    currentActiveIndex = Math.min(currentActiveIndex + 1, items.length - 1);
                    updateActiveItem(items);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    currentActiveIndex = Math.max(currentActiveIndex - 1, -1);
                    updateActiveItem(items);
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (currentActiveIndex >= 0 && items[currentActiveIndex]) {
                        selectParticipant(currentResults[currentActiveIndex]);
                        hideDropdown(dropdown);
                    }
                    break;
                case 'Escape':
                    hideDropdown(dropdown);
                    break;
            }
        });
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!field.contains(e.target) && !dropdown.contains(e.target)) {
                hideDropdown(dropdown);
            }
        });
        
        // Show dropdown when field is focused and has content
        field.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                searchParticipants(this.value.trim(), searchType, dropdown, field);
            }
        });
    }
    
    function searchParticipants(query, searchType, dropdown, field) {
        showLoading(dropdown);
        
        fetch(`../../controllers/estudantes/search_autocomplete.php?query=${encodeURIComponent(query)}&type=${searchType}&limit=10`)
            .then(response => response.json())
            .then(data => {
                currentResults = data;
                currentActiveIndex = -1;
                
                if (data.length === 0) {
                    showNoResults(dropdown, query);
                } else {
                    showResults(dropdown, data, query);
                }
            })
            .catch(error => {
                console.error('Error searching participants:', error);
                showError(dropdown, 'Erro ao buscar estudantes');
            });
    }
    
    function showLoading(dropdown) {
        dropdown.innerHTML = '<div class="autocomplete-loading">Buscando...</div>';
        dropdown.style.display = 'block';
    }
    
    function showNoResults(dropdown, query) {
        dropdown.innerHTML = `<div class="autocomplete-no-results">Nenhum estudante encontrado para "${query}"</div>`;
        dropdown.style.display = 'block';
    }
    
    function showError(dropdown, message) {
        dropdown.innerHTML = `<div class="autocomplete-no-results">${message}</div>`;
        dropdown.style.display = 'block';
    }
    
    function showResults(dropdown, results, query) {
        let html = '';
        
        results.forEach((participant, index) => {
            // Highlight matching text
            let displayText = participant.display_text;
            const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
            displayText = displayText.replace(regex, '<span class="highlighted-match">$1</span>');
            
            html += `<div class="autocomplete-item" data-index="${index}">${displayText}</div>`;
        });
        
        dropdown.innerHTML = html;
        dropdown.style.display = 'block';
        
        // Add click event listeners to items
        dropdown.querySelectorAll('.autocomplete-item').forEach((item, index) => {
            item.addEventListener('click', function() {
                selectParticipant(results[index]);
                hideDropdown(dropdown);
            });
        });
    }
    
    function updateActiveItem(items) {
        items.forEach((item, index) => {
            if (index === currentActiveIndex) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }
    
    function selectParticipant(participant) {
        // Fill form fields with participant data
        document.getElementById('matricula').value = participant.matricula;
        document.getElementById('nome').value = participant.nome;
        document.getElementById('nível de inglês_cargo').value = participant.nível de inglês || '';
        document.getElementById('instituicao').value = participant.forca || '';
        
        // Set participant ID for reference
        document.getElementById('estudante_id').value = participant.id;
        
        // Show notification
        showNotification(`Dados de ${participant.nome} preenchidos automaticamente.`, 'success');
        
        // Clear classificacao if it was pre-filled
        document.getElementById('classificacao').selectedIndex = 0;
    }
    
    function hideDropdown(dropdown) {
        dropdown.style.display = 'none';
        currentActiveIndex = -1;
    }
    
    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Inicializar com estado correto
    updateButtons();
});
</script>