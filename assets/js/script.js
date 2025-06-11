/**
 * SISENGLISH - Sistema de Gestão para Escola de Inglês
 * Main JavaScript file
 */

// Enable Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Enable date validation for forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const startDateInput = form.querySelector('[name="inicio"]');
        const endDateInput = form.querySelector('[name="termino"]');
        
        if (startDateInput && endDateInput) {
            form.addEventListener('submit', function(event) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                
                if (endDate < startDate) {
                    event.preventDefault();
                    alert('A data de término não pode ser anterior à data de início.');
                }
            });
        }
    });
    
    // Convert text inputs to uppercase
    const textInputs = document.querySelectorAll('input[type="text"]');
    textInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Skip inputs that should not be converted
            const skipInputs = ['search', 'host', 'username', 'database'];
            if (!skipInputs.includes(input.name)) {
                input.value = input.value.toUpperCase();
            }
        });
    });
});

/**
 * Function to confirm deletion
 * @param {string} message - Confirmation message
 * @returns {boolean} - True if confirmed, false otherwise
 */
function confirmDelete(message = 'Tem certeza que deseja excluir este item?') {
    return confirm(message);
}

/**
 * Function to filter table rows
 * @param {string} inputId - ID of the input element
 * @param {string} tableId - ID of the table element
 */
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 0; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < td.length; j++) {
            const cell = td[j];
            if (cell) {
                const txtValue = cell.textContent || cell.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        if (found || tr[i].getElementsByTagName('th').length > 0) {
            tr[i].style.display = '';
        } else {
            tr[i].style.display = 'none';
        }
    }
}

// Validação Frontend para campos obrigatórios, e-mail, CPF e datas
function validarFormularioEstudante() {
    let nome = document.getElementById('nome');
    let matricula = document.getElementById('matricula');
    let nível de inglês = document.getElementById('nível de inglês');
    let forca = document.getElementById('forca');
    let erros = [];    if (!matricula.value.match(/^\d+$/)) erros.push('Matrícula deve conter apenas números.');
    if (nome.value.trim().length < 3) erros.push('Nome deve ter pelo menos 3 caracteres.');
    // Campo Nível de Inglês/Função é opcional - removida validação
    if (!forca.value) erros.push('Telefone/Instituição deve ser selecionada.');
    if (erros.length > 0) {
        alert(erros.join('\n'));
        return false;
    }
    return true;
}
document.getElementById('estudante-form')?.addEventListener('submit', function(e) {
    if (!validarFormularioEstudante()) e.preventDefault();
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
