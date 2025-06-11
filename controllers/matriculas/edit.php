<?php
// ... código existente ...

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitiza e define as propriedades da matrícula
    $matricula->formacao_id = filter_input(INPUT_POST, 'formacao_id', FILTER_SANITIZE_NUMBER_INT) ?: '';
    $matricula->estudante_id = filter_input(INPUT_POST, 'estudante_id', FILTER_SANITIZE_NUMBER_INT) ?: '';
    $matricula->situacao = trim(filter_input(INPUT_POST, 'situacao', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'ATIVO');

    // Validação robusta
    $erros = [];
    if(empty($matricula->formacao_id) || !ctype_digit($matricula->formacao_id)) {
        $erros[] = 'ID da curso de inglês inválido.';
    }
    if(empty($matricula->estudante_id) || !ctype_digit($matricula->estudante_id)) {
        $erros[] = 'ID do estudante inválido.';
    }
    if(empty($matricula->situacao)) {
        $erros[] = 'Situação deve ser informada.';
    }
    if($erros) {
        $_SESSION['error_message'] = implode('<br>', $erros);
        header('Location: ../../views/matriculas/edit.php?id=' . $matricula->id);
        exit();
    }

    // ... código existente ...
}