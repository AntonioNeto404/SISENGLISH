-- Migração para adaptar o banco SISCAP para contexto de escola de inglês (SISENGLISH)
-- Execute este script APÓS fazer backup do banco de dados

-- 1. Renomear campos da tabela alunos para contexto de escola
ALTER TABLE `alunos` 
CHANGE `posto` `nivel_ingles` VARCHAR(100) NOT NULL COMMENT 'Nível de inglês do aluno (Básico, Intermediário, Avançado)', 
CHANGE `forca` `telefone` VARCHAR(50) NOT NULL COMMENT 'Telefone de contato do aluno';

-- 2. Adicionar novos campos para escola de inglês
ALTER TABLE `alunos` 
ADD COLUMN `email` VARCHAR(100) NULL COMMENT 'Email do aluno' AFTER `telefone`,
ADD COLUMN `data_nascimento` DATE NULL COMMENT 'Data de nascimento' AFTER `email`,
ADD COLUMN `responsavel` VARCHAR(100) NULL COMMENT 'Nome do responsável (para menores)' AFTER `data_nascimento`,
ADD COLUMN `telefone_responsavel` VARCHAR(50) NULL COMMENT 'Telefone do responsável' AFTER `responsavel`,
ADD COLUMN `data_matricula` DATE NOT NULL DEFAULT CURRENT_DATE COMMENT 'Data da matrícula' AFTER `telefone_responsavel`,
ADD COLUMN `status` ENUM('ATIVO', 'INATIVO', 'SUSPENSO') NOT NULL DEFAULT 'ATIVO' COMMENT 'Status do aluno' AFTER `data_matricula`;

-- 3. Atualizar dados existentes com valores padrão apropriados
UPDATE `alunos` SET 
`nivel_ingles` = CASE 
    WHEN `nivel_ingles` LIKE '%SOLDADO%' OR `nivel_ingles` LIKE '%SD%' THEN 'BÁSICO'
    WHEN `nivel_ingles` LIKE '%CABO%' OR `nivel_ingles` LIKE '%CB%' THEN 'INTERMEDIÁRIO'
    WHEN `nivel_ingles` LIKE '%SARGENTO%' OR `nivel_ingles` LIKE '%SGT%' THEN 'AVANÇADO'
    WHEN `nivel_ingles` LIKE '%TENENTE%' OR `nivel_ingles` LIKE '%TEN%' THEN 'BUSINESS ENGLISH'
    WHEN `nivel_ingles` LIKE '%CAPITÃO%' OR `nivel_ingles` LIKE '%CAP%' THEN 'BUSINESS ENGLISH'
    ELSE 'BÁSICO'
END,
`telefone` = CASE 
    WHEN `telefone` LIKE '%EXERCITO%' OR `telefone` LIKE '%POLICIA%' THEN '(00) 00000-0000'
    ELSE `telefone`
END;

-- 4. Adicionar campos específicos para cursos de inglês na tabela formacoes
ALTER TABLE `formacoes` 
CHANGE `tipo_capacitacao` `nivel_curso` VARCHAR(100) NOT NULL COMMENT 'Nível do curso de inglês',
ADD COLUMN `professor_responsavel` VARCHAR(100) NULL COMMENT 'Professor responsável pelo curso' AFTER `parecer`,
ADD COLUMN `material_didatico` VARCHAR(255) NULL COMMENT 'Material didático utilizado' AFTER `professor_responsavel`,
ADD COLUMN `preco` DECIMAL(10,2) NULL COMMENT 'Preço do curso' AFTER `material_didatico`,
ADD COLUMN `max_alunos` INT(11) DEFAULT 20 COMMENT 'Número máximo de alunos por turma' AFTER `preco`;

-- 5. Atualizar níveis de curso existentes
UPDATE `formacoes` SET 
`nivel_curso` = CASE 
    WHEN `nivel_curso` LIKE '%FORMAÇÃO%' THEN 'BÁSICO'
    WHEN `nivel_curso` LIKE '%CAPACITAÇÃO%' THEN 'INTERMEDIÁRIO'
    WHEN `nivel_curso` LIKE '%TREINAMENTO%' THEN 'AVANÇADO'
    WHEN `nivel_curso` LIKE '%SEMINÁRIO%' THEN 'CONVERSAÇÃO'
    WHEN `nivel_curso` LIKE '%OFICINA%' THEN 'PREPARATÓRIO'
    ELSE 'BÁSICO'
END;

-- 6. Atualizar situações dos cursos
UPDATE `formacoes` SET 
`situacao` = CASE 
    WHEN `situacao` LIKE '%FINALIZADA%' THEN 'CONCLUÍDO'
    WHEN `situacao` LIKE '%CONCLUIDO%' THEN 'CONCLUÍDO'
    ELSE `situacao`
END;

-- 7. Adicionar campos para controle de presença e notas
ALTER TABLE `matriculas` 
ADD COLUMN `nota_final` DECIMAL(5,2) NULL COMMENT 'Nota final do aluno (0-10)' AFTER `formacao_id`,
ADD COLUMN `frequencia` DECIMAL(5,2) NULL COMMENT 'Percentual de frequência (0-100)' AFTER `nota_final`,
ADD COLUMN `status_curso` ENUM('CURSANDO', 'APROVADO', 'REPROVADO', 'DESISTENTE') NOT NULL DEFAULT 'CURSANDO' AFTER `frequencia`,
ADD COLUMN `data_conclusao` DATE NULL COMMENT 'Data de conclusão do curso' AFTER `status_curso`,
ADD COLUMN `certificado_emitido` BOOLEAN DEFAULT FALSE COMMENT 'Se o certificado foi emitido' AFTER `data_conclusao`;

-- 8. Criar tabela para controle de aulas
CREATE TABLE IF NOT EXISTS `aulas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formacao_id` int(11) NOT NULL,
  `data_aula` date NOT NULL,
  `horario_inicio` time NOT NULL,
  `horario_fim` time NOT NULL,
  `conteudo` text NOT NULL COMMENT 'Conteúdo da aula',
  `professor_id` int(11) NULL,
  `observacoes` text NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`formacao_id`) REFERENCES `formacoes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`professor_id`) REFERENCES `docentes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Controle de aulas dos cursos';

-- 9. Criar tabela para presença dos alunos
CREATE TABLE IF NOT EXISTS `presencas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aula_id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `presente` boolean NOT NULL DEFAULT FALSE,
  `observacoes` text NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_presenca` (`aula_id`, `aluno_id`),
  FOREIGN KEY (`aula_id`) REFERENCES `aulas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`aluno_id`) REFERENCES `alunos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Controle de presença dos alunos';

-- 10. Adicionar campos específicos para professores de inglês
ALTER TABLE `docentes` 
ADD COLUMN `especialidade_ingles` VARCHAR(100) NULL COMMENT 'Especialidade em ensino de inglês' AFTER `qualificacao`,
ADD COLUMN `certificacoes` TEXT NULL COMMENT 'Certificações em inglês (TOEFL, IELTS, etc.)' AFTER `especialidade_ingles`,
ADD COLUMN `experiencia_anos` INT(11) NULL COMMENT 'Anos de experiência no ensino' AFTER `certificacoes`,
ADD COLUMN `nivel_ingles` ENUM('NATIVO', 'FLUENTE', 'AVANÇADO', 'INTERMEDIÁRIO') DEFAULT 'AVANÇADO' AFTER `experiencia_anos`;

-- 11. Inserir dados de exemplo para escola de inglês
INSERT IGNORE INTO `formacoes` (`curso`, `ano`, `turma`, `inicio`, `termino`, `local`, `situacao`, `nivel_curso`, `modalidade`, `campus`, `carga_horaria`, `instituicao`, `municipio`, `preco`, `max_alunos`) VALUES
('Inglês Básico I', '2025', 'A1', '2025-02-01', '2025-05-30', 'Sala 101', 'EM ANDAMENTO', 'BÁSICO', 'PRESENCIAL', 'Unidade Centro', 60, 'SISENGLISH School', 'São Paulo', 299.90, 15),
('Inglês Intermediário', '2025', 'B1', '2025-02-01', '2025-05-30', 'Sala 102', 'EM ANDAMENTO', 'INTERMEDIÁRIO', 'PRESENCIAL', 'Unidade Centro', 80, 'SISENGLISH School', 'São Paulo', 399.90, 12),
('Business English', '2025', 'C1', '2025-02-15', '2025-06-15', 'Sala 201', 'EM ANDAMENTO', 'BUSINESS ENGLISH', 'HÍBRIDO', 'Unidade Centro', 100, 'SISENGLISH School', 'São Paulo', 599.90, 10),
('Preparatório TOEFL', '2025', 'T1', '2025-03-01', '2025-05-31', 'Sala 301', 'EM ANDAMENTO', 'PREPARATÓRIO TOEFL', 'ONLINE', 'Unidade Virtual', 120, 'SISENGLISH School', 'São Paulo', 799.90, 8);

-- 12. Atualizar comentários das tabelas
ALTER TABLE `alunos` COMMENT = 'Estudantes matriculados na escola de inglês';
ALTER TABLE `formacoes` COMMENT = 'Cursos de inglês oferecidos pela escola';
ALTER TABLE `docentes` COMMENT = 'Professores de inglês da escola';
ALTER TABLE `matriculas` COMMENT = 'Matrículas dos estudantes nos cursos';

-- 13. Criar view para relatório de estudantes ativos
CREATE OR REPLACE VIEW `view_estudantes_ativos` AS
SELECT 
    a.id,
    a.matricula,
    a.nome,
    a.nivel_ingles,
    a.telefone,
    a.email,
    a.status,
    COUNT(m.id) as total_cursos,
    MAX(f.ano) as ultimo_ano_curso
FROM alunos a
LEFT JOIN matriculas m ON a.id = m.aluno_id
LEFT JOIN formacoes f ON m.formacao_id = f.id
WHERE a.status = 'ATIVO'
GROUP BY a.id
ORDER BY a.nome;

-- 14. Criar view para relatório de cursos
CREATE OR REPLACE VIEW `view_cursos_resumo` AS
SELECT 
    f.id,
    f.curso,
    f.nivel_curso,
    f.ano,
    f.turma,
    f.situacao,
    f.modalidade,
    f.carga_horaria,
    f.preco,
    f.max_alunos,
    COUNT(m.id) as alunos_matriculados,
    (f.max_alunos - COUNT(m.id)) as vagas_disponiveis
FROM formacoes f
LEFT JOIN matriculas m ON f.id = m.formacao_id
GROUP BY f.id
ORDER BY f.ano DESC, f.curso;

-- Fim da migração
-- IMPORTANTE: Teste todas as funcionalidades após executar este script!
