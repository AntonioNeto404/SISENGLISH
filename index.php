<?php
session_start();

// Incluir arquivos de banco de dados e objetos
include_once 'config/database.php';

// Verificar se o sistema expirou
$database = new Database();
$db = $database->getConnection();

if($database->checkExpiration()) {
    include_once 'views/expiration.php';
    exit();
}

// Verificar se o usuário está logado
if(isset($_SESSION['user_id'])) {
    header("Location: views/dashboard.php");
    exit();
}

// Gerar token CSRF se não existir
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Definir título da página
$page_title = "Login - SISENGLISH";

// Incluir cabeçalho
include_once 'views/layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">SISENGLISH - Sistema de Gestão Escolar</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Verificar mensagem de erro de login
                    if(isset($_SESSION['error_message'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                        unset($_SESSION['error_message']);
                    }
                    ?>
                    <form method="post" action="controllers/login.php">
                        <?php require_once __DIR__ . '/config/csrf.php'; echo csrf_input(); ?>
                        <div class="form-group mb-3">
                            <label for="cognome">Nome de Usuário:</label>                            <input type="text" class="form-control" id="cognome" name="cognome" placeholder="Digite seu nome de usuário" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="senha">Senha:</label>
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block w-100">
                            <i class="fas fa-sign-in-alt"></i> Entrar no Sistema
                        </button>
                    </form>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            SISENGLISH - Sistema de Gestão para Escola de Inglês
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir rodapé
include_once 'views/layouts/footer.php';
?>
