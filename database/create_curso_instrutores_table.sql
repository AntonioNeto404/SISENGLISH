-- Create table for course-instructor assignments
CREATE TABLE IF NOT EXISTS `curso_instrutores` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `formacao_id` INT(11) NOT NULL,
  `docente_id` INT(11) NOT NULL,
  `disciplina_id` INT(11) NOT NULL,
  `created_by` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `formacao_idx` (`formacao_id`),
  KEY `docente_idx` (`docente_id`),
  KEY `disciplina_idx` (`disciplina_id`),
  CONSTRAINT `fk_curso_instrutores_formacoes` FOREIGN KEY (`formacao_id`) REFERENCES `formacoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_curso_instrutores_docentes` FOREIGN KEY (`docente_id`) REFERENCES `docentes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_curso_instrutores_disciplinas` FOREIGN KEY (`disciplina_id`) REFERENCES `disciplinas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;