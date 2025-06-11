<?php
session_start();

// Clear any existing session data
session_unset();
session_destroy();

// Define page title
$page_title = "Licença Expirada - SISENGLISH";

// Hide navbar
$hide_navbar = true;

// Include header
include_once 'layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Licença Expirada</h4>
                </div>
                <div class="card-body text-center">
                    <p class="lead">O prazo fornecido para utilização deste sistema se esgotou.</p>
                    <p>Por favor, entre em contato com o administrador do sistema para renovar sua licença.</p>
                    <hr>
                    <p><i class="fas fa-envelope"></i> Email: admin@siscap.com.br</p>
                    <p><i class="fas fa-phone"></i> Telefone: (XX) XXXX-XXXX</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'layouts/footer.php';
?>
