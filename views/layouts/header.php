<?php
// Only start session if not already active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title><?php echo htmlspecialchars(isset($page_title) ? $page_title : "SISENGLISH - Sistema de Gestão Escolar"); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars(isset($base_url) ? $base_url : ''); ?>/assets/css/style.css">
</head>
<body>
    <?php if(isset($_SESSION['user_id']) && !isset($hide_navbar)): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo htmlspecialchars(isset($base_url) ? $base_url : ''); ?>/views/dashboard.php">
                <i class="fas fa-graduation-cap"></i> SISENGLISH
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlspecialchars(isset($base_url) ? $base_url : ''); ?>/views/dashboard.php">
                            <i class="fas fa-home"></i> Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlspecialchars(isset($base_url) ? $base_url : ''); ?>/views/cursos/index.php">
                            <i class="fas fa-book"></i> Cursos de Inglês
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlspecialchars(isset($base_url) ? $base_url : ''); ?>/views/estudantes/index.php">
                            <i class="fas fa-user-graduate"></i> Estudantes
                        </a>
                    </li>                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlspecialchars(isset($base_url) ? $base_url : ''); ?>/views/professores/index.php">
                            <i class="fas fa-chalkboard-teacher"></i> Professores
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlspecialchars(isset($base_url) ? $base_url : ''); ?>/views/relatorios/index.php">
                            <i class="fas fa-chart-bar"></i> Relatórios
                        </a>
                    </li>
                    <?php if($_SESSION['tipo'] == 'ADMINISTRADOR'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cogs"></i> Administração
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?php echo htmlspecialchars(isset($base_url) ? $base_url : ''); ?>/views/usuarios/index.php">
                                    <i class="fas fa-user-cog"></i> Usuários do Sistema
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo htmlspecialchars(isset($base_url) ? $base_url : ''); ?>/views/coordenadores/index.php">
                                    <i class="fas fa-user-tie"></i> Coordenadores
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text me-3">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['nome']); ?>
                    </span>
                    <a href="<?php echo htmlspecialchars(isset($base_url) ? $base_url : ''); ?>/controllers/logout.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    <?php if(isset($_SESSION['message'])): ?>
    <div class="alert alert-success" role="alert" aria-live="polite"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if(isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger" role="alert" aria-live="assertive"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
