# Guia de Desenvolvimento - SISENGLISH

## Visão Geral do Desenvolvimento

Este guia é destinado a desenvolvedores que irão trabalhar na manutenção e evolução do sistema SISENGLISH.

## Configuração do Ambiente de Desenvolvimento

### Pré-requisitos
- **PHP 7.4+** (recomendado PHP 8.1)
- **Composer** para gerenciamento de dependências
- **MySQL 5.7+** ou **MariaDB 10.3+**
- **Git** para controle de versão
- **VSCode** ou **PHPStorm** (IDEs recomendadas)

### Configuração Inicial
```powershell
# Clone do repositório
git clone https://github.com/seu-usuario/sisenglish.git
cd sisenglish

# Instalação das dependências
composer install

# Configuração do ambiente
cp .env.example .env

# Configurar banco de desenvolvimento
# Editar .env com dados do banco local
```

### Estrutura de Branches
```
main/master     # Produção
develop         # Desenvolvimento principal
feature/*       # Novas funcionalidades
bugfix/*        # Correções de bugs
hotfix/*        # Correções urgentes
```

## Arquitetura do Sistema

### Padrão MVC Implementado

#### Models (Modelos)
**Localização:** `models/`

Responsáveis pela lógica de dados e comunicação com o banco:

```php
<?php
// Exemplo: models/exemplo.php
class Exemplo {
    private $conn;
    private $table_name = "tabela_exemplo";
    
    public $id;
    public $campo1;
    public $campo2;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Métodos CRUD
    public function create() { /* ... */ }
    public function readAll() { /* ... */ }
    public function readOne() { /* ... */ }
    public function update() { /* ... */ }
    public function delete() { /* ... */ }
}
```

#### Views (Visualizações)
**Localização:** `views/`

Responsáveis pela apresentação dos dados:

```php
<?php
// Exemplo: views/exemplo/index.php
session_start();
include_once '../../config/database.php';
include_once '../../models/exemplo.php';

$page_title = "Título da Página";
$base_url = "../..";
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <!-- Conteúdo da página -->
</div>

<?php include_once '../layouts/footer.php'; ?>
```

#### Controllers (Controladores)
**Localização:** `controllers/`

Responsáveis pela lógica de negócio e fluxo da aplicação:

```php
<?php
// Exemplo: controllers/exemplo/create.php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/csrf.php';

// Validação CSRF
csrf_validate();

// Verificação de autenticação
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Lógica do controlador
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Processar dados
    // Validar
    // Salvar
    // Redirecionar
}
```

## Padrões de Código

### Convenções de Nomenclatura

#### PHP
```php
// Classes: PascalCase
class MinhaClasse {}

// Métodos e variáveis: camelCase
public function meuMetodo() {}
$minhaVariavel = 'valor';

// Constantes: UPPER_CASE
const MINHA_CONSTANTE = 'valor';

// Arquivos: snake_case.php
meu_arquivo.php
```

#### Banco de Dados
```sql
-- Tabelas: plural, snake_case
CREATE TABLE estudantes;
CREATE TABLE curso_professores;

-- Campos: snake_case
nome_completo
data_nascimento
created_at
```

#### JavaScript
```javascript
// Variáveis e funções: camelCase
const minhaVariavel = 'valor';
function minhaFuncao() {}

// Constantes: UPPER_CASE
const MINHA_CONSTANTE = 'valor';
```

### Formatação de Código

#### PHP-CS-Fixer
```powershell
# Executar correção automática
composer fix

# Ou diretamente
vendor\bin\php-cs-fixer fix
```

**Configuração (.php-cs-fixer.php):**
```php
<?php
return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'trailing_comma_in_multiline' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude(['vendor', 'node_modules'])
    );
```

## Desenvolvimento de Funcionalidades

### Criando um Novo Módulo

#### 1. Model
```php
<?php
// models/novo_modulo.php
class NovoModulo {
    private $conn;
    private $table_name = "novo_modulo";
    
    public $id;
    public $nome;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nome) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->nome);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
    
    // ... outros métodos CRUD
}
```

#### 2. Migration (SQL)
```sql
-- database/create_novo_modulo_table.sql
CREATE TABLE IF NOT EXISTS `novo_modulo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### 3. Views
```php
<?php
// views/novo-modulo/index.php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

include_once '../../config/database.php';
include_once '../../models/novo_modulo.php';

$database = new Database();
$db = $database->getConnection();
$modelo = new NovoModulo($db);

$page_title = "Novo Módulo";
$base_url = "../..";
include_once '../layouts/header.php';
?>

<div class="container mt-4">
    <h2>Gerenciar Novo Módulo</h2>
    <!-- Conteúdo específico -->
</div>

<?php include_once '../layouts/footer.php'; ?>
```

#### 4. Controllers
```php
<?php
// controllers/novo-modulo/create.php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/csrf.php';

csrf_validate();

if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

include_once '../../config/database.php';
include_once '../../models/novo_modulo.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $modelo = new NovoModulo($db);
    
    $modelo->nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    
    if($modelo->create()) {
        $_SESSION['message'] = "Registro criado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao criar registro.";
    }
    
    header("Location: ../../views/novo-modulo/index.php");
    exit();
}
```

#### 5. Testes
```php
<?php
// tests/Models/NovoModuloTest.php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use NovoModulo;
use PDO;

class NovoModuloTest extends TestCase {
    private static $db;
    
    public static function setUpBeforeClass(): void {
        // Configurar conexão de teste
        $host = getenv('DB_HOST') ?: 'localhost';
        $name = getenv('DB_NAME') ?: 'siscap03_db';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        
        self::$db = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
        self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function testCreate(): void {
        $modelo = new NovoModulo(self::$db);
        $modelo->nome = 'Teste';
        
        $this->assertTrue($modelo->create());
        $this->assertNotEmpty($modelo->id);
    }
}
```

### Implementando Autocomplete

#### JavaScript
```javascript
// assets/js/autocomplete.js
class Autocomplete {
    constructor(inputId, endpoint, onSelect) {
        this.input = document.getElementById(inputId);
        this.endpoint = endpoint;
        this.onSelect = onSelect;
        this.timeout = null;
        this.init();
    }
    
    init() {
        this.input.addEventListener('input', (e) => {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.search(e.target.value);
            }, 300);
        });
    }
    
    async search(term) {
        if(term.length < 2) return;
        
        try {
            const response = await fetch(
                `${this.endpoint}?query=${encodeURIComponent(term)}`
            );
            const results = await response.json();
            this.showResults(results);
        } catch(error) {
            console.error('Erro no autocomplete:', error);
        }
    }
    
    showResults(results) {
        // Implementar exibição dos resultados
    }
}

// Uso
new Autocomplete('estudante-input', '/controllers/alunos/search_autocomplete.php', (item) => {
    // Callback quando item é selecionado
});
```

### Implementando Validações

#### PHP - Server Side
```php
<?php
// config/validators.php
class Validators {
    public static function validateMatricula($matricula) {
        return !empty($matricula) && ctype_digit($matricula);
    }
    
    public static function validateNome($nome) {
        return !empty($nome) && mb_strlen($nome) >= 3;
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
```

#### JavaScript - Client Side
```javascript
// assets/js/validators.js
const Validators = {
    matricula: (value) => {
        return /^\d+$/.test(value) && value.length > 0;
    },
    
    nome: (value) => {
        return value.trim().length >= 3;
    },
    
    email: (value) => {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(value);
    },
    
    required: (value) => {
        return value.trim().length > 0;
    }
};
```

## Testes

### Configuração de Testes
**Arquivo:** `phpunit.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         colors="true">
    <testsuites>
        <testsuite name="Models">
            <directory>tests/Models</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### Executando Testes
```powershell
# Todos os testes
composer test

# Testes específicos
vendor\bin\phpunit tests\Models\AlunoTest.php

# Com cobertura (se xdebug instalado)
vendor\bin\phpunit --coverage-html coverage/
```

### Escrevendo Testes
```php
<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;

class ExemploTest extends TestCase {
    private static $db;
    
    public static function setUpBeforeClass(): void {
        // Configuração única para todos os testes
    }
    
    protected function setUp(): void {
        // Configuração para cada teste
    }
    
    public function testExemplo(): void {
        // Arrange (preparar)
        $modelo = new MinhaClasse();
        
        // Act (agir)
        $resultado = $modelo->meuMetodo();
        
        // Assert (verificar)
        $this->assertTrue($resultado);
    }
    
    protected function tearDown(): void {
        // Limpeza após cada teste
    }
}
```

## Debugging

### Logs do Sistema
```php
<?php
// Usando Monolog
global $log;
$log->info("Mensagem informativa", ['context' => $data]);
$log->warning("Aviso", ['user_id' => $_SESSION['user_id']]);
$log->error("Erro crítico", ['exception' => $e->getMessage()]);
```

### Debug no PHP
```php
<?php
// Desenvolvimento
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Debug de variáveis
var_dump($variavel);
print_r($array);

// Debug com die
dd($variavel); // se implementado

// Xdebug (se instalado)
xdebug_break();
```

### Debug JavaScript
```javascript
// Console
console.log('Debug info:', data);
console.error('Erro:', error);
console.table(array);

// Breakpoints
debugger;

// Performance
console.time('operacao');
// ... código
console.timeEnd('operacao');
```

## Banco de Dados

### Migrations
Criar sempre scripts SQL para mudanças no banco:

```sql
-- database/migrate_add_campo_exemplo.sql
ALTER TABLE `estudantes` 
ADD COLUMN `campo_exemplo` VARCHAR(100) NULL 
COMMENT 'Descrição do campo' 
AFTER `campo_existente`;
```

### Seeders (Dados de Teste)
```sql
-- database/seed_dados_teste.sql
INSERT INTO `estudantes` (`matricula`, `nome`, `posto`, `forca`) VALUES
('TEST001', 'ESTUDANTE TESTE 1', 'BÁSICO', '(11) 99999-9999'),
('TEST002', 'ESTUDANTE TESTE 2', 'INTERMEDIÁRIO', '(11) 88888-8888');
```

### Backup e Restore
```powershell
# Backup
mysqldump -u usuario -p sisenglish_db > backup.sql

# Restore
mysql -u usuario -p sisenglish_db < backup.sql
```

## Performance

### Otimizações de Query
```php
<?php
// Usar índices
// Evitar SELECT *
// Usar LIMIT para paginação
// Prepared statements sempre

// Exemplo otimizado
$query = "SELECT id, nome, matricula 
          FROM estudantes 
          WHERE nome LIKE ? 
          ORDER BY nome 
          LIMIT ? OFFSET ?";
$stmt = $db->prepare($query);
$stmt->bindValue(1, "%{$search}%");
$stmt->bindValue(2, $limit, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
```

### Cache (se implementado)
```php
<?php
// Exemplo de cache simples
function getCachedData($key, $callback, $ttl = 3600) {
    $cacheFile = "cache/{$key}.json";
    
    if(file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
        return json_decode(file_get_contents($cacheFile), true);
    }
    
    $data = $callback();
    file_put_contents($cacheFile, json_encode($data));
    return $data;
}
```

## Deployment

### Checklist de Deploy
- [ ] Testes passando
- [ ] Migrations executadas
- [ ] .env configurado para produção
- [ ] Logs limpos
- [ ] Backup do banco atual
- [ ] Permissões de arquivo corretas

### Script de Deploy
```powershell
# deploy.ps1
Write-Host "Iniciando deploy..."

# Backup
mysqldump -u $env:DB_USER -p$env:DB_PASS $env:DB_NAME > "backup-$(Get-Date -Format 'yyyyMMdd-HHmmss').sql"

# Atualizar código
git pull origin main

# Dependências
composer install --no-dev --optimize-autoloader

# Migrations (se houver)
# php migrate.php

# Limpar logs antigos
Get-ChildItem logs/ -Name "*.log" | Where-Object {$_.LastWriteTime -lt (Get-Date).AddDays(-7)} | Remove-Item

Write-Host "Deploy concluído!"
```

## Contribuindo

### Fluxo de Trabalho
1. **Fork** do repositório
2. **Clone** do seu fork
3. **Branch** para nova feature: `git checkout -b feature/nova-funcionalidade`
4. **Desenvolver** e **testar**
5. **Commit** com mensagens descritivas
6. **Push** para seu fork
7. **Pull Request** para o repositório principal

### Mensagens de Commit
```
feat: adiciona autocomplete para seleção de estudantes
fix: corrige validação de matrícula duplicada
docs: atualiza documentação da API
style: formata código segundo PSR-12
refactor: reorganiza estrutura de controllers
test: adiciona testes para modelo de cursos
```

### Code Review
- **Código limpo** e comentado
- **Testes** para novas funcionalidades
- **Documentação** atualizada
- **Performance** considerada
- **Segurança** validada

---

**SISENGLISH - Guia de Desenvolvimento**  
**Versão:** 1.0  
**Para desenvolvedores:** Júnior, Pleno, Sênior  
**Última atualização:** Junho 2025
