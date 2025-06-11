# Documentação de Banco de Dados - SISENGLISH

## Visão Geral

Este documento descreve a estrutura completa do banco de dados do sistema SISENGLISH, incluindo tabelas, relacionamentos, índices e views.

## Informações Gerais

- **SGBD:** MySQL 5.7+ / MariaDB 10.3+
- **Charset:** utf8mb4
- **Collation:** utf8mb4_unicode_ci
- **Engine:** InnoDB (todas as tabelas)

## Estrutura de Tabelas

### 1. usuarios
**Descrição:** Controle de acesso ao sistema

```sql
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cognome` varchar(50) NOT NULL COMMENT 'Login único do usuário',
  `senha` varchar(100) NOT NULL COMMENT 'Senha (hash)',
  `nome` varchar(100) NOT NULL COMMENT 'Nome completo',
  `tipo` varchar(20) NOT NULL COMMENT 'ADMINISTRADOR ou USUARIO',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cognome` (`cognome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Dados Iniciais:**
```sql
INSERT INTO `usuarios` VALUES (1, 'ADMIN', '1234', 'ADMINISTRADOR', 'ADMINISTRADOR');
```

### 2. estudantes (anteriormente alunos)
**Descrição:** Estudantes da escola de inglês

```sql
CREATE TABLE `estudantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matricula` varchar(50) NOT NULL COMMENT 'Matrícula única do estudante',
  `nome` varchar(100) NOT NULL COMMENT 'Nome completo',
  `nivel_ingles` varchar(100) NOT NULL COMMENT 'Nível de inglês atual',
  `telefone` varchar(50) NOT NULL COMMENT 'Telefone de contato',
  `email` varchar(100) NULL COMMENT 'Email do estudante',
  `data_nascimento` date NULL COMMENT 'Data de nascimento',
  `endereco` text NULL COMMENT 'Endereço completo',
  `status` enum('ATIVO','INATIVO','FORMADO') DEFAULT 'ATIVO',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricula` (`matricula`),
  KEY `idx_nome` (`nome`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Estudantes da escola de inglês';
```

### 3. formacoes (Cursos)
**Descrição:** Cursos de inglês oferecidos pela escola

```sql
CREATE TABLE `formacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curso` varchar(100) NOT NULL COMMENT 'Nome do curso',
  `ano` varchar(4) NOT NULL COMMENT 'Ano de realização',
  `turma` varchar(20) NOT NULL COMMENT 'Identificação da turma',
  `inicio` date NOT NULL COMMENT 'Data de início',
  `termino` date NOT NULL COMMENT 'Data de término',
  `local` varchar(100) NOT NULL COMMENT 'Local das aulas',
  `situacao` varchar(20) NOT NULL COMMENT 'EM ANDAMENTO, CONCLUÍDO, CANCELADO',
  `nivel_curso` varchar(100) NOT NULL COMMENT 'Nível do curso de inglês',
  `modalidade` varchar(100) NOT NULL COMMENT 'PRESENCIAL, ONLINE, HÍBRIDO',
  `unidade` varchar(100) NOT NULL COMMENT 'Unidade responsável',
  `carga_horaria` int(11) NOT NULL COMMENT 'Total de horas',
  `instituicao` varchar(255) NOT NULL COMMENT 'Instituição responsável',
  `municipio` varchar(255) NOT NULL COMMENT 'Cidade',
  `autorizacao` varchar(255) DEFAULT NULL COMMENT 'Número da autorização',
  `parecer` text DEFAULT NULL COMMENT 'Observações gerais',
  `professor_responsavel` varchar(100) NULL COMMENT 'Professor responsável',
  `material_didatico` varchar(255) NULL COMMENT 'Material utilizado',
  `preco` decimal(10,2) NULL COMMENT 'Preço do curso',
  `max_alunos` int(11) DEFAULT 20 COMMENT 'Máximo de alunos',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_curso_ano` (`curso`, `ano`),
  KEY `idx_situacao` (`situacao`),
  KEY `idx_nivel` (`nivel_curso`),
  KEY `idx_modalidade` (`modalidade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Cursos de inglês oferecidos';
```

### 4. docentes (Professores)
**Descrição:** Professores e instrutores da escola

```sql
CREATE TABLE `docentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matricula` varchar(50) NOT NULL COMMENT 'Matrícula única',
  `nome` varchar(100) NOT NULL COMMENT 'Nome completo',
  `rg` varchar(20) DEFAULT NULL COMMENT 'RG',
  `orgao_expedidor` varchar(20) DEFAULT NULL COMMENT 'Órgão expedidor do RG',
  `cpf` varchar(14) DEFAULT NULL COMMENT 'CPF',
  `lattes` varchar(255) DEFAULT NULL COMMENT 'Link do Currículo Lattes',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email',
  `fone_residencial` varchar(20) DEFAULT NULL,
  `fone_profissional` varchar(20) DEFAULT NULL,
  `fone_celular` varchar(20) DEFAULT NULL,
  `instituicao` varchar(255) DEFAULT NULL COMMENT 'Instituição de origem',
  `data_ingresso` date DEFAULT NULL COMMENT 'Data de ingresso',
  `cargo` varchar(100) DEFAULT NULL COMMENT 'Cargo/Especialização',
  `lotacao` varchar(100) DEFAULT NULL COMMENT 'Lotação',
  `cidade_lotacao` varchar(100) DEFAULT NULL,
  `disciplinas_professor` text DEFAULT NULL,
  `disciplinas_conteudista` text DEFAULT NULL,
  `formacao_tecnologica_1` varchar(255) DEFAULT NULL,
  `formacao_tecnologica_2` varchar(255) DEFAULT NULL,
  `graduacao_1` varchar(255) DEFAULT NULL,
  `graduacao_2` varchar(255) DEFAULT NULL,
  `graduacao_3` varchar(255) DEFAULT NULL,
  `especializacao_gestao` varchar(255) DEFAULT NULL,
  `especializacao_outros` varchar(255) DEFAULT NULL,
  `mestrado` varchar(255) DEFAULT NULL,
  `doutorado` varchar(255) DEFAULT NULL,
  `pos_doutorado` varchar(255) DEFAULT NULL,
  `situacao` varchar(50) DEFAULT 'ATIVO',
  `classificacao` varchar(100) DEFAULT NULL COMMENT 'Tipo de professor',
  `carga_horaria` int(11) DEFAULT NULL COMMENT 'Carga horária semanal',
  `certificacoes_ingles` text NULL COMMENT 'Certificações em inglês',
  `experiencia_ensino` int(11) NULL COMMENT 'Anos de experiência',
  `especializacao_ingles` varchar(255) NULL COMMENT 'Área de especialização',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricula` (`matricula`),
  KEY `idx_nome` (`nome`),
  KEY `idx_situacao` (`situacao`),
  KEY `idx_classificacao` (`classificacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Professores da escola de inglês';
```

### 5. disciplinas
**Descrição:** Disciplinas/matérias oferecidas

```sql
CREATE TABLE `disciplinas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL COMMENT 'Nome da disciplina',
  `descricao` text DEFAULT NULL COMMENT 'Descrição detalhada',
  `carga_horaria` int(11) DEFAULT NULL COMMENT 'Carga horária da disciplina',
  `nivel_requerido` varchar(50) NULL COMMENT 'Nível mínimo necessário',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Disciplinas oferecidas';
```

### 6. matriculas
**Descrição:** Matrículas de estudantes em cursos

```sql
CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formacao_id` int(11) NOT NULL COMMENT 'ID do curso',
  `estudante_id` int(11) NOT NULL COMMENT 'ID do estudante',
  `situacao` varchar(20) NOT NULL DEFAULT 'ATIVO' COMMENT 'Status da matrícula',
  `nota_final` decimal(5,2) NULL COMMENT 'Nota final (0-10)',
  `frequencia` decimal(5,2) NULL COMMENT 'Percentual de frequência (0-100)',
  `status_curso` enum('CURSANDO','APROVADO','REPROVADO','DESISTENTE') DEFAULT 'CURSANDO',
  `data_conclusao` date NULL COMMENT 'Data de conclusão',
  `certificado_emitido` boolean DEFAULT FALSE COMMENT 'Se certificado foi emitido',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `formacao_estudante` (`formacao_id`,`estudante_id`),
  KEY `estudante_id` (`estudante_id`),
  KEY `idx_situacao` (`situacao`),
  KEY `idx_status_curso` (`status_curso`),
  CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`formacao_id`) REFERENCES `formacoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`estudante_id`) REFERENCES `estudantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Matrículas dos estudantes';
```

### 7. curso_professores (Tabela Pivot)
**Descrição:** Relacionamento entre cursos e professores

```sql
CREATE TABLE `curso_professores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formacao_id` int(11) NOT NULL COMMENT 'ID do curso',
  `professor_id` int(11) NOT NULL COMMENT 'ID do professor',
  `disciplina_id` int(11) NOT NULL COMMENT 'ID da disciplina',
  `created_by` int(11) NOT NULL COMMENT 'Usuário que fez a vinculação',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_assignment` (`formacao_id`,`professor_id`,`disciplina_id`),
  KEY `professor_id` (`professor_id`),
  KEY `disciplina_id` (`disciplina_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `curso_professores_ibfk_1` FOREIGN KEY (`formacao_id`) REFERENCES `formacoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `curso_professores_ibfk_2` FOREIGN KEY (`professor_id`) REFERENCES `docentes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `curso_professores_ibfk_3` FOREIGN KEY (`disciplina_id`) REFERENCES `disciplinas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `curso_professores_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Vinculação professores-cursos';
```

### 8. aulas (Extensão futura)
**Descrição:** Controle individual de aulas

```sql
CREATE TABLE `aulas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formacao_id` int(11) NOT NULL,
  `data_aula` date NOT NULL,
  `horario_inicio` time NOT NULL,
  `horario_fim` time NOT NULL,
  `conteudo` text NOT NULL COMMENT 'Conteúdo da aula',
  `professor_id` int(11) NULL,
  `observacoes` text NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `formacao_id` (`formacao_id`),
  KEY `professor_id` (`professor_id`),
  KEY `idx_data` (`data_aula`),
  CONSTRAINT `aulas_ibfk_1` FOREIGN KEY (`formacao_id`) REFERENCES `formacoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `aulas_ibfk_2` FOREIGN KEY (`professor_id`) REFERENCES `docentes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Controle de aulas';
```

### 9. presencas (Extensão futura)
**Descrição:** Controle de presença por aula

```sql
CREATE TABLE `presencas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aula_id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `presente` boolean NOT NULL DEFAULT FALSE,
  `observacoes` text NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_presenca` (`aula_id`, `aluno_id`),
  KEY `aluno_id` (`aluno_id`),
  CONSTRAINT `presencas_ibfk_1` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presencas_ibfk_2` FOREIGN KEY (`aluno_id`) REFERENCES `estudantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Controle de presença';
```

### 10. coordenadores
**Descrição:** Coordenadores e gestores

```sql
CREATE TABLE `coordenadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `assinatura` varchar(255) DEFAULT NULL COMMENT 'Caminho para arquivo de assinatura',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Coordenadores da escola';
```

### 11. expiracao
**Descrição:** Controle de expiração do sistema

```sql
CREATE TABLE `expiracao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_exp` date NOT NULL COMMENT 'Data de expiração do sistema',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `expiracao` VALUES (1, DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY));
```

## Views do Sistema

### view_estudantes_ativos
**Descrição:** Relatório de estudantes ativos com estatísticas

```sql
CREATE VIEW `view_estudantes_ativos` AS
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
FROM estudantes a
LEFT JOIN matriculas m ON a.id = m.estudante_id
LEFT JOIN formacoes f ON m.formacao_id = f.id
WHERE a.status = 'ATIVO'
GROUP BY a.id
ORDER BY a.nome;
```

### view_cursos_resumo
**Descrição:** Resumo de cursos com contagem de alunos

```sql
CREATE VIEW `view_cursos_resumo` AS
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
```

## Relacionamentos

### Diagrama de Relacionamentos

```
usuarios (1) ──── (N) curso_professores
    │
    └─ created_by

estudantes (1) ──── (N) matriculas ──── (N) formacoes
    │                                        │
    │                                        │
    └─ presencas ──── aulas ────────────────┘

docentes (1) ──── (N) curso_professores ──── (N) disciplinas
    │                      │
    │                      └─ (N) formacoes
    │
    └─ (N) aulas

formacoes (1) ──── (N) aulas
```

### Chaves Estrangeiras

| Tabela | Campo | Referencia | Ação |
|--------|-------|------------|------|
| matriculas | formacao_id | formacoes.id | CASCADE |
| matriculas | estudante_id | estudantes.id | CASCADE |
| curso_professores | formacao_id | formacoes.id | CASCADE |
| curso_professores | professor_id | docentes.id | CASCADE |
| curso_professores | disciplina_id | disciplinas.id | CASCADE |
| curso_professores | created_by | usuarios.id | RESTRICT |
| aulas | formacao_id | formacoes.id | CASCADE |
| aulas | professor_id | docentes.id | SET NULL |
| presencas | aula_id | aulas.id | CASCADE |
| presencas | aluno_id | estudantes.id | CASCADE |

## Índices

### Índices de Performance

```sql
-- Estudantes
CREATE INDEX idx_estudantes_nome ON estudantes(nome);
CREATE INDEX idx_estudantes_status ON estudantes(status);

-- Formações
CREATE INDEX idx_formacoes_curso_ano ON formacoes(curso, ano);
CREATE INDEX idx_formacoes_situacao ON formacoes(situacao);
CREATE INDEX idx_formacoes_nivel ON formacoes(nivel_curso);

-- Docentes
CREATE INDEX idx_docentes_nome ON docentes(nome);
CREATE INDEX idx_docentes_situacao ON docentes(situacao);

-- Matrículas
CREATE INDEX idx_matriculas_situacao ON matriculas(situacao);
CREATE INDEX idx_matriculas_status ON matriculas(status_curso);

-- Aulas
CREATE INDEX idx_aulas_data ON aulas(data_aula);
```

## Scripts de Migração

### Migração do SISCAP
**Arquivo:** `database/migrate_to_english_school.sql`

Principais alterações:
1. Renomeia campos para contexto de escola de inglês
2. Adiciona campos específicos (preço, máximo de alunos)
3. Atualiza valores enum e terminologia
4. Cria views para relatórios
5. Adiciona tabelas de controle (aulas, presenças)

### Correção de Encoding
**Arquivo:** `fix_accents.php`

Corrige problemas de encoding em:
- Nomes de estudantes e professores
- Títulos de cursos
- Descrições de disciplinas

## Backup e Restore

### Backup Completo
```powershell
mysqldump -u usuario -p --single-transaction --routines --triggers sisenglish_db > backup_completo.sql
```

### Backup por Tabela
```powershell
mysqldump -u usuario -p sisenglish_db estudantes > backup_estudantes.sql
mysqldump -u usuario -p sisenglish_db formacoes > backup_cursos.sql
```

### Restore
```powershell
mysql -u usuario -p sisenglish_db < backup_completo.sql
```

## Manutenção

### Limpeza de Dados Antigos
```sql
-- Remover logs antigos (se implementado)
DELETE FROM logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Arquivar cursos muito antigos
UPDATE formacoes SET situacao = 'ARQUIVADO' 
WHERE termino < DATE_SUB(NOW(), INTERVAL 2 YEAR) 
AND situacao = 'CONCLUÍDO';
```

### Otimização
```sql
-- Analisar tabelas
ANALYZE TABLE estudantes, formacoes, docentes, matriculas;

-- Otimizar tabelas
OPTIMIZE TABLE estudantes, formacoes, docentes, matriculas;

-- Verificar fragmentação
SHOW TABLE STATUS WHERE Engine = 'InnoDB';
```

### Monitoramento
```sql
-- Estatísticas gerais
SELECT 
    'Estudantes' as tabela, COUNT(*) as total FROM estudantes
UNION ALL
SELECT 'Cursos', COUNT(*) FROM formacoes
UNION ALL  
SELECT 'Professores', COUNT(*) FROM docentes
UNION ALL
SELECT 'Matrículas', COUNT(*) FROM matriculas;

-- Cursos por situação
SELECT situacao, COUNT(*) as total 
FROM formacoes 
GROUP BY situacao;

-- Estudantes por status
SELECT status, COUNT(*) as total 
FROM estudantes 
GROUP BY status;
```

---

**SISENGLISH - Documentação de Banco de Dados**  
**Versão:** 1.0  
**Última atualização:** Junho 2025  
**SGBD:** MySQL 5.7+ / MariaDB 10.3+
