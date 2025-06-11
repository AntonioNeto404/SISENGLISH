<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: ../../index.php"); exit(); }
include_once '../../config/database.php';
include_once '../../models/Professor.php';
include_once '../../models/Curso.php';

$database = new Database();
$db = $database->getConnection();
if($database->checkExpiration()) { header("Location: ../expiration.php"); exit(); }

$page_title = "Certidão de Professores";
$base_url = "../..";
include_once '../layouts/header.php';
?>
<link rel="stylesheet" href="../../assets/css/certidao-print.css">
<?php

// Define timezone and current date in Portuguese
date_default_timezone_set('America/Recife');
setlocale(LC_TIME, 'pt_BR.UTF-8');

// Function to format date in Portuguese
function formatDatePTBR($date = null) {
    if ($date === null) {
        $date = time();
    } elseif (is_string($date)) {
        $date = strtotime($date);
    }
    
    $days = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];
    $months = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
    
    $dayOfWeek = $days[date('w', $date)];
    $day = date('d', $date);
    $month = $months[date('n', $date) - 1];
    $year = date('Y', $date);
    
    return ucfirst($dayOfWeek) . ', ' . $day . ' de ' . $month . ' de ' . $year;
}

$today = formatDatePTBR();

// Fetch professores for selection
$professorObj = new Professor($db);
$stmtD = $professorObj->readAll();
$selected = $_GET['professor'] ?? '';
?>
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .certificate, .certificate * {
        visibility: visible;
    }
    .certificate {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 15mm;
        page-break-after: always;
        box-shadow: none;
        border: none;
    }
    .d-print-none {
        display: none !important;
    }
    .navbar, .breadcrumb, form, .btn, footer {
        display: none !important;
    }
}
</style>
<div class="container mt-4">
    <h2><i class="fas fa-file-alt"></i> Certidão de Professores</h2>
    <hr>
    <form method="get" class="form-inline mb-3 d-print-none">
        <label class="mr-2">Professor:</label>
        <select name="professor" class="form-control">
            <option value="">Selecione</option>
            <?php while($d = $stmtD->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?php echo $d['id']; ?>" <?php if($selected==$d['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($d['nome']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-print"></i> Gerar</button>
    </form>

<?php if($selected): ?>
    <?php
    // Fetch selected professor and their courses
    $professorObj->id = $selected;
    if(!$professorObj->readOne()) {
        echo '<div class="alert alert-warning">Professor não encontrado ou não existem registros de docência.</div>';
    } else {
    $cursoObj = new Curso($db);
    $stmtC = $cursoObj->readAll();
    ?>    <div id="certidaoDocencia" class="certificate" style="width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #000; page-break-after: always; background-color: white; font-family: Arial, sans-serif;">
        <!-- Cabeçalho com logo -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <div style="width: 100px;">
                <div style="display: flex; align-items: center; flex-direction: column;">
                    <img src="../../assets/img/logo acides.png" alt="Logo" style="width: 80px; margin-bottom: 5px;">
                    <span style="font-size: 10px; text-align: center;">ACIDES</span>
                </div>
            </div>
            <div style="text-align: center; flex-grow: 1;">
                <div style="font-size: 11px; font-weight: bold; text-align: center;">
                    Secretaria de Defesa Social<br>
                    Academia Integrada de Defesa Social - ACIDES<br>
                    Formando Pacificadores Sociais<br>
                    Gerência de Integração e Curso de Inglês - GICAP<br>
                    Rua São Geraldo, 111, Santo Amaro, Recife, PE<br>
                    CEP 50040-020, fone 3183-5040/ 5098/ 5099/ 5086
                </div>
            </div>            <div style="text-align: right; width: 150px;">
                <div style="display: flex; flex-direction: column; align-items: flex-end; font-size: 10px;">
                    <img src="../../assets/img/assinatura jailton.png" alt="Assinatura" style="width: 120px; margin-bottom: 2px;">                    <span>JOSÉ JAILTON SIQUEIRA DE MELO – TEN CEL BM</span>
                    <span>Gerente de Integração e Curso de Inglês</span>
                    <span><?php echo htmlspecialchars(formatDatePTBR()); ?></span>
                </div>
            </div>
        </div>
        
        <h1 style="text-align: center; font-size: 18px; font-weight: normal; margin: 30px 0 20px 0;">C E R T I D Ã O &nbsp; D E &nbsp; D O C Ê N C I A</h1>
          <p style="text-align: justify; font-size: 12px; margin-bottom: 30px; line-height: 1.5;">
            Certifico para os devidos fins que após busca em nossos registros, no banco de dados da 
            Superintendência de Curso e Curso de Inglês Institucional e da Academia Integrada de 
            Defesa Social - ACIDES, o servidor abaixo descrito participou na qualidade de Professor 
            da(s) curso de inglês(ões), conforme elencada(s), no(s) período(s) especificado(s).
        </p>
          <!-- Dados do professor -->
        <div style="margin-bottom: 30px; font-size: 12px;">
            <table width="100%" style="border-collapse: collapse;">
                <tr>
                    <td width="20%" style="font-weight: bold; padding: 5px 0;">Nome do servidor:</td>
                    <td width="80%" style="padding: 5px 0;"><?php echo htmlspecialchars(strtoupper($professorObj->nome)); ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 5px 0;">Instituição:</td>
                    <td style="padding: 5px 0;"><?php echo htmlspecialchars(strtoupper($professorObj->instituicao)); ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 5px 0;">Matr.:</td>
                    <td style="padding: 5px 0;"><?php echo htmlspecialchars($professorObj->matricula); ?></td>
                </tr>
            </table>
        </div>
          <!-- Cursos -->
        <div style="margin-bottom: 30px;">
            <?php 
            $prevAno = null;
            $primeiro = true;
            while($c = $stmtC->fetch(PDO::FETCH_ASSOC)):
                // Agrupando por ano
                if($c['ano'] != $prevAno) {
                    if(!$primeiro) echo "</table>";
                    $primeiro = false;
                    echo '<h3 style="background-color: #f0f0f0; padding: 5px; font-size: 14px; margin-top: 20px;">' . htmlspecialchars($c['ano']) . '</h3>';
                    echo '<table width="100%" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px; font-size: 11px; border: 1px solid #ddd;">';
                    echo '<tr style="background-color: #f8f8f8;">
                        <th style="text-align: left; padding: 5px; border: 1px solid #ddd;">Disciplina</th>
                        <th style="text-align: center; width: 60px; border: 1px solid #ddd;">Turma</th>
                        <th style="text-align: center; width: 80px; border: 1px solid #ddd;">Início</th>
                        <th style="text-align: center; width: 80px; border: 1px solid #ddd;">Término</th>
                        <th style="text-align: center; width: 60px; border: 1px solid #ddd;">C/H</th>
                        <th style="text-align: center; width: 100px; border: 1px solid #ddd;">Class.</th>
                    </tr>';
                    $prevAno = $c['ano'];
                }
            ?>
                <tr>                    <td style="padding: 5px; border: 1px solid #ddd;"><?php echo htmlspecialchars($c['disciplina'] ?? '-'); ?></td>
                    <td style="text-align: center; border: 1px solid #ddd;"><?php echo htmlspecialchars($c['turma'] ?? '-'); ?></td>
                    <td style="text-align: center; border: 1px solid #ddd;"><?php echo isset($c['inicio']) ? date('d/m/Y', strtotime($c['inicio'])) : '-'; ?></td>
                    <td style="text-align: center; border: 1px solid #ddd;"><?php echo isset($c['termino']) ? date('d/m/Y', strtotime($c['termino'])) : '-'; ?></td>
                    <td style="text-align: center; border: 1px solid #ddd;"><?php echo htmlspecialchars($c['carga_horaria'] ?? '-'); ?></td>
                    <td style="text-align: center; border: 1px solid #ddd;"><?php echo htmlspecialchars(isset($c['classificacao']) ? $c['classificacao'] : 'TITULAR'); ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if(!$primeiro): ?></table><?php endif; ?>
        </div>        <!-- Assinatura -->
        <div style="margin-top: 40px; display: flex; justify-content: center;">
            <div style="text-align: center;">
                <img src="../../assets/img/assinatura alisson barros.png" alt="Assinatura" style="width: 160px; margin-bottom: -25px;">
                <div style="border-top: 1px solid #000; padding-top: 0px; font-size: 12px; margin-top: -5px;">
                    ALYSSON BARROS DA SILVA - MAJ BM<br>
                    Coordenador do CTONLINE - GICAP
                </div>
            </div>
        </div>
          <div style="margin-top: 10px; text-align: left; font-size: 10px;">
            <p>Recife, PE<br>
               <?php echo htmlspecialchars(formatDatePTBR()); ?><br>
               <?php echo date('H:i'); ?>
            </p>
        </div>
        
        <div style="text-align: right; font-size: 10px; margin-top: -40px;">
            <p>2º TEN BM MAT. 930183-6 / BARRETO<br>
               Unidade de Banco de Dados-GICAP
            </p>
        </div></div>

    <!-- Botões para imprimir e baixar PDF -->
    <div class="mb-4 mt-4 d-print-none">
        <div class="row">
            <div class="col-md-6">
                <button onclick="window.print()" class="btn btn-primary btn-block">
                    <i class="fas fa-print"></i> Imprimir Certidão
                </button>
            </div>
            <div class="col-md-6">
                <button onclick="gerarPDF()" class="btn btn-danger btn-block">
                    <i class="fas fa-file-pdf"></i> Baixar como PDF
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
    function gerarPDF() {
        // Configurações do PDF
        var element = document.getElementById('certidaoDocencia');
        var opt = {
            margin: 10,
            filename: 'certidao_docencia_<?php echo $professorObj->matricula; ?>.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };        // Gerar PDF
        html2pdf().from(element).set(opt).save();
    }
    </script>
<?php } // Fechamento do else (professor encontrado) ?>
<?php endif; ?>
</div>
<?php include_once '../layouts/footer.php'; ?>