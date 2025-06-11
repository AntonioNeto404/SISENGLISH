-- Create disciplinas table
CREATE TABLE IF NOT EXISTS `disciplinas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text,
  `carga_horaria` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some sample disciplines (optional)
INSERT INTO `disciplinas` (`nome`, `descricao`, `carga_horaria`) VALUES
('Técnicas Operacionais', 'Técnicas e procedimentos operacionais básicos', 40),
('Legislação Aplicada', 'Estudo da legislação aplicada à área', 30),
('Gerenciamento de Crises', 'Técnicas de gerenciamento de situações críticas', 20);