<?php
/**
 * Script de Tradução Automática - SISCAP para SISENGLISH
 * 
 * Este script automatiza a tradução de termos específicos do contexto militar
 * para o contexto de escola de inglês em todo o sistema.
 * 
 * IMPORTANTE: Execute este script APENAS UMA VEZ e após fazer backup dos arquivos!
 */

echo "=== SCRIPT DE TRADUÇÃO SISENGLISH ===\n";
echo "Iniciando tradução dos arquivos...\n\n";

// Diretório raiz do projeto
$rootDir = __DIR__;

// Mapeamento de traduções
$translations = [
    // Títulos e nomes do sistema
    'SISCAP' => 'SISENGLISH',
    'Sistema de Capacitação' => 'Sistema de Gestão para Escola de Inglês',
    'Sistema de Gestão de Capacitação' => 'Sistema de Gestão para Escola de Inglês',
    
    // Termos relacionados a cursos
    'Capacitações' => 'Cursos de Inglês',
    'Capacitação' => 'Curso de Inglês',
    'capacitação' => 'curso de inglês',
    'capacitações' => 'cursos de inglês',
    'Formação' => 'Curso',
    'formação' => 'curso',
    'Formações' => 'Cursos',
    'formações' => 'cursos',
    'Nova Capacitação' => 'Novo Curso de Inglês',
    'Nova Formação' => 'Novo Curso de Inglês',
    'Editar Capacitação' => 'Editar Curso de Inglês',
    
    // Termos relacionados a pessoas
    'Participantes' => 'Estudantes',
    'participantes' => 'estudantes',
    'Participante' => 'Estudante',
    'participante' => 'estudante',
    'Novo Participante' => 'Novo Estudante',
    'Alunos' => 'Estudantes',
    'alunos' => 'estudantes',
    'Aluno' => 'Estudante',
    'aluno' => 'estudante',
    'Docentes' => 'Professores',
    'docentes' => 'professores',
    'Docente' => 'Professor',
    'docente' => 'professor',
    'Instrutores' => 'Professores',
    'instrutores' => 'professores',
    'Instrutor' => 'Professor',
    'instrutor' => 'professor',
    
    // Termos militares específicos
    'Posto' => 'Nível de Inglês',
    'posto' => 'nível de inglês',
    'Força' => 'Telefone',
    'força' => 'telefone',
    'Exército' => 'Escola',
    'exército' => 'escola',
    'Militar' => 'Educacional',
    'militar' => 'educacional',
    'Comando' => 'Coordenação',
    'comando' => 'coordenação',
    'Portaria' => 'Autorização',
    'portaria' => 'autorização',
    'Parecer Técnico' => 'Observações',
    'parecer técnico' => 'observações',
    
    // Situações e status
    'FINALIZADA - Com Portaria' => 'CONCLUÍDO',
    'FINALIZADA - Com Relatório' => 'CONCLUÍDO',
    'FINALIZADA - Sem dados' => 'CONCLUÍDO',
    'CONCLUIDO' => 'CONCLUÍDO',
    
    // Tipos de capacitação para níveis de inglês
    'FORMAÇÃO' => 'BÁSICO',
    'FORMAÇÃO CONTINUADA' => 'INTERMEDIÁRIO',
    'CAPACITAÇÃO' => 'AVANÇADO',
    'CAPACITAÇÃO INTEGRADA' => 'BUSINESS ENGLISH',
    'TREINAMENTO' => 'CONVERSAÇÃO',
    'SEMINÁRIO' => 'PREPARATÓRIO',
    'OFICINA' => 'TÉCNICO',
    'PALESTRA' => 'WORKSHOP',
    
    // Modalidades
    'SEMI PRESENCIAL' => 'HÍBRIDO',
    'EAD' => 'ONLINE',
    
    // Outros termos
    'Campus' => 'Unidade',
    'campus' => 'unidade',
    'Gestores' => 'Coordenadores',
    'gestores' => 'coordenadores',
    'Gestor' => 'Coordenador',
    'gestor' => 'coordenador',
];

// Extensões de arquivo para processar
$extensions = ['php', 'html', 'js', 'css', 'md'];

// Diretórios para processar
$dirsToProcess = [
    'views',
    'controllers',
    'models',
    'assets',
    'config'
];

// Arquivos individuais na raiz
$rootFiles = [
    'index.php',
    'install.php',
    'README.md'
];

$processedFiles = 0;
$totalReplacements = 0;

/**
 * Função para processar um arquivo
 */
function processFile($filePath, $translations) {
    global $totalReplacements;
    
    if (!file_exists($filePath) || !is_readable($filePath)) {
        return false;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $fileReplacements = 0;
    
    foreach ($translations as $search => $replace) {
        $count = 0;
        $content = str_replace($search, $replace, $content, $count);
        $fileReplacements += $count;
    }
    
    if ($fileReplacements > 0) {
        file_put_contents($filePath, $content);
        echo "✓ {$filePath} - {$fileReplacements} substituições\n";
        $totalReplacements += $fileReplacements;
        return true;
    }
    
    return false;
}

/**
 * Função para processar diretório recursivamente
 */
function processDirectory($dir, $translations, $extensions) {
    global $processedFiles;
    
    if (!is_dir($dir)) {
        return;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, $extensions)) {
                if (processFile($file->getPathname(), $translations)) {
                    $processedFiles++;
                }
            }
        }
    }
}

// Processar arquivos da raiz
echo "Processando arquivos da raiz...\n";
foreach ($rootFiles as $file) {
    $filePath = $rootDir . DIRECTORY_SEPARATOR . $file;
    if (processFile($filePath, $translations)) {
        $processedFiles++;
    }
}

// Processar diretórios
echo "\nProcessando diretórios...\n";
foreach ($dirsToProcess as $dir) {
    $dirPath = $rootDir . DIRECTORY_SEPARATOR . $dir;
    echo "Processando diretório: {$dir}\n";
    processDirectory($dirPath, $translations, $extensions);
}

echo "\n=== TRADUÇÃO CONCLUÍDA ===\n";
echo "Arquivos processados: {$processedFiles}\n";
echo "Total de substituições: {$totalReplacements}\n";
echo "\nIMPORTANTE:\n";
echo "1. Verifique se o sistema está funcionando corretamente\n";
echo "2. Execute os testes automatizados\n";
echo "3. Faça backup desta versão traduzida\n";
echo "4. Execute a migração do banco de dados se necessário\n\n";

// Criar arquivo de log da tradução
$logContent = "=== LOG DE TRADUÇÃO SISENGLISH ===\n";
$logContent .= "Data: " . date('Y-m-d H:i:s') . "\n";
$logContent .= "Arquivos processados: {$processedFiles}\n";
$logContent .= "Total de substituições: {$totalReplacements}\n\n";
$logContent .= "Traduções aplicadas:\n";
foreach ($translations as $search => $replace) {
    $logContent .= "'{$search}' => '{$replace}'\n";
}

file_put_contents($rootDir . '/logs/traducao_' . date('Y-m-d_H-i-s') . '.log', $logContent);

echo "Log salvo em: logs/traducao_" . date('Y-m-d_H-i-s') . ".log\n";
?>
