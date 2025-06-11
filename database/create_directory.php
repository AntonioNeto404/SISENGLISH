<?php
// Criar o diretório database se não existir
if (!file_exists(__DIR__ . '/database')) {
    mkdir(__DIR__ . '/database', 0777, true);
    echo "Diretório 'database' criado com sucesso!";
} else {
    echo "O diretório 'database' já existe.";
}
?>
