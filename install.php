<?php
// Set page title
$page_title = "Instalação - SISENGLISH";

// Hide navbar
$hide_navbar = true;

// Check if form was submitted
$installation_success = false;
$error_message = "";

// Verify database directory and SQL file exist
if (!file_exists('database')) {
    mkdir('database', 0777, true);
}

// Check if SQL file exists, if not create it
if (!file_exists('database/siscap03_db.sql')) {
    $sql_content = '-- Banco de dados SISENGLISH - Sistema de Gestão para Escola de Inglês
CREATE DATABASE IF NOT EXISTS sisenglish_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sisenglish_db;

-- Tabela de usuários do sistema
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cognome` varchar(50) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cognome` (`cognome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir usuário administrador padrão
INSERT INTO `usuarios` (`cognome`, `senha`, `nome`, `tipo`) VALUES
(\'ADMIN\', \'1234\', \'ADMINISTRADOR\', \'ADMINISTRADOR\');

-- Tabela de estudantes (estudantes da escola de inglês)
CREATE TABLE IF NOT EXISTS `estudantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matricula` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `nivel_ingles` varchar(100) NOT NULL COMMENT \'Nível de inglês do estudante\',
  `telefone` varchar(50) NOT NULL COMMENT \'Telefone de contato\',
  `email` varchar(100) NULL COMMENT \'Email do estudante\',
  `data_nascimento` date NULL COMMENT \'Data de nascimento\',
  `responsavel` varchar(100) NULL COMMENT \'Responsável (para menores)\',
  `telefone_responsavel` varchar(50) NULL COMMENT \'Telefone do responsável\',
  `data_matricula` date NOT NULL DEFAULT CURRENT_DATE,
  `status` enum(\'ATIVO\',\'INATIVO\',\'SUSPENSO\') NOT NULL DEFAULT \'ATIVO\',
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricula` (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de cursos de inglês (cursos)
CREATE TABLE IF NOT EXISTS `formacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curso` varchar(100) NOT NULL,
  `ano` varchar(4) NOT NULL,
  `turma` varchar(20) NOT NULL,
  `inicio` date NOT NULL,
  `termino` date NOT NULL,
  `local` varchar(100) NOT NULL,
  `situacao` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `matriculas`
CREATE TABLE IF NOT EXISTS `matriculas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formacao_id` int(11) NOT NULL,
  `estudante_id` int(11) NOT NULL,
  `situacao` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `formacao_estudante` (`formacao_id`,`estudante_id`),
  KEY `estudante_id` (`estudante_id`),
  CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`formacao_id`) REFERENCES `formacoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`estudante_id`) REFERENCES `estudantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `coordenadores`
CREATE TABLE IF NOT EXISTS `coordenadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `assinatura` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `expiracao`
CREATE TABLE IF NOT EXISTS `expiracao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_exp` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default expiration date (current date + 30 days)
INSERT INTO `expiracao` (`id`, `data_exp`) VALUES
(1, DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY));';

    file_put_contents('database/siscap03_db.sql', $sql_content);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config/csrf.php';
    csrf_validate();
    // Get database connection details
    $host = $_POST['host'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $database = $_POST['database'];
    
    try {
        // Create database connection
        $conn = new PDO("mysql:host=$host", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if it doesn't exist
        $conn->exec("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn->exec("USE $database");
        
        // Execute all SQL migrations in database directory
        $sqlDir = __DIR__ . '/database';
        $files = scandir($sqlDir);
        // Sort files for deterministic order
        sort($files);
        foreach ($files as $file) {
            if (substr($file, -4) === '.sql') {
                $path = $sqlDir . '/' . $file;
                $sql = file_get_contents($path);
                // Split into individual statements
                $stmts = array_filter(array_map('trim', explode(';', $sql)));
                foreach ($stmts as $stmt) {
                    $conn->exec($stmt);
                }
            }
        }
        
        // Create config file
        $config_content = '<?php
class Database {
    private $host = "' . $host . '";
    private $db_name = "' . $database . '";
    private $username = "' . $username . '";
    private $password = "' . $password . '";
    public $conn;
    
    // Get database connection
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    // Check if system has expired
    public function checkExpiration() {
        try {
            $query = "SELECT * FROM expiracao WHERE id = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $expiry_date = strtotime($row["data_exp"]);
                $current_date = time();
                
                if($current_date > $expiry_date) {
                    return true; // System has expired
                }
            }
            return false; // Not expired
        } catch(PDOException $exception) {
            return false;
        }
    }
}
?>';
        
        // Create config directory if it doesn't exist
        if (!file_exists('config')) {
            mkdir('config', 0777, true);
        }
        
        // Write config file
        file_put_contents('config/database.php', $config_content);
        
        $installation_success = true;
    } catch(PDOException $e) {
        $error_message = "Erro na instalação: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Instalação do SISENGLISH - Sistema de Gestão para Escola de Inglês</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($installation_success): ?>
                            <div class="alert alert-success">
                                <h4 class="alert-heading"><i class="fas fa-check-circle"></i> Instalação concluída com sucesso!</h4>
                                <p>O sistema SISENGLISH foi instalado com sucesso. Agora você pode acessar o sistema.</p>
                                <hr>
                                <p class="mb-0">Usuário padrão: <strong>ADMIN</strong><br>Senha padrão: <strong>1234</strong></p>
                                <p class="mt-3">Por questões de segurança, altere a senha após o primeiro login.</p>
                                <div class="text-center mt-4">
                                    <a href="index.php" class="btn btn-primary">Acessar o Sistema</a>
                                </div>
                            </div>
                        <?php elseif (!empty($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                            <div class="text-center mt-4">
                                <a href="install.php" class="btn btn-primary">Tentar Novamente</a>
                            </div>
                        <?php else: ?>
                            <form method="post" action="install.php">
                                <?php
                                require_once __DIR__ . '/../config/csrf.php';
                                echo csrf_input();
                                ?>
                                <h5 class="mb-4">Configuração do Banco de Dados</h5>
                                <div class="mb-3">
                                    <label for="host" class="form-label">Host:</label>
                                    <input type="text" class="form-control" id="host" name="host" value="localhost" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Usuário:</label>
                                    <input type="text" class="form-control" id="username" name="username" value="root" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Senha:</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                <div class="mb-3">
                                    <label for="database" class="form-label">Nome do Banco de Dados:</label>
                                    <input type="text" class="form-control" id="database" name="database" value="siscap03_db" required>
                                </div>
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">Instalar</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
