-- Create database
CREATE DATABASE IF NOT EXISTS siscap03_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE siscap03_db;

-- Table structure for table `usuarios`
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cognome` varchar(50) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cognome` (`cognome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user
INSERT INTO `usuarios` (`cognome`, `senha`, `nome`, `tipo`) VALUES
('ADMIN', '1234', 'ADMINISTRADOR', 'ADMINISTRADOR');

-- Table structure for table `alunos`
CREATE TABLE IF NOT EXISTS `alunos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matricula` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `posto` varchar(100) NOT NULL,
  `forca` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricula` (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `formacoes`
CREATE TABLE IF NOT EXISTS `formacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curso` varchar(100) NOT NULL,
  `ano` varchar(4) NOT NULL,
  `turma` varchar(20) NOT NULL,
  `inicio` date NOT NULL,
  `termino` date NOT NULL,
  `local` varchar(100) NOT NULL,
  `situacao` varchar(20) NOT NULL,
  `tipo_capacitacao` varchar(100) NOT NULL,
  `modalidade` varchar(100) NOT NULL,
  `campus` varchar(100) NOT NULL,
  `carga_horaria` int(11) NOT NULL,
  `instituicao` varchar(255) NOT NULL,
  `municipio` varchar(255) NOT NULL,
  `portaria` varchar(255) DEFAULT NULL,
  `parecer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `matriculas`
CREATE TABLE IF NOT EXISTS `matriculas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formacao_id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `situacao` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `formacao_aluno` (`formacao_id`,`aluno_id`),
  KEY `aluno_id` (`aluno_id`),
  CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`formacao_id`) REFERENCES `formacoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `gestores`
CREATE TABLE IF NOT EXISTS `gestores` (
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
(1, DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY));

-- Table structure for table `cursos`
CREATE TABLE IF NOT EXISTS `cursos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(255) NOT NULL,
    `ano` YEAR NOT NULL,
    `turma` VARCHAR(50) NOT NULL,
    `inicio` DATE NOT NULL,
    `termino` DATE NOT NULL,
    `local` VARCHAR(255) NOT NULL,
    `situacao` ENUM('EM ANDAMENTO', 'CONCLUÍDO', 'CANCELADO') NOT NULL,
    `tipo_capacitacao` ENUM('Presencial', 'Online') NOT NULL,
    `modalidade` VARCHAR(100) NOT NULL,
    `campus` VARCHAR(100) NOT NULL,
    `carga_horaria` INT NOT NULL,
    `instituicao` VARCHAR(255) NOT NULL,
    `municipio` VARCHAR(255) NOT NULL,
    `portaria` VARCHAR(255),
    `parecer` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
