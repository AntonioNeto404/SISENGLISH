# SISENGLISH - Sistema de Gestão para Escola de Inglês

## Visão Geral do Projeto

O **SISENGLISH** é um sistema de gestão acadêmica desenvolvido em PHP especificamente para escolas de inglês. O sistema evoluiu de um projeto anterior (SISCAP - Sistema de Capacitação) para atender as necessidades específicas de uma escola de idiomas.

### Informações Técnicas
- **Linguagem:** PHP 7.4+
- **Framework:** PHP Puro (sem framework)
- **Banco de Dados:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, Bootstrap 5.x, JavaScript
- **Arquitetura:** MVC (Model-View-Controller)
- **Composer:** Gerenciamento de dependências
- **Testes:** PHPUnit 9.5+

## Estrutura do Projeto

```
siscap/
├── assets/                 # Recursos estáticos
│   ├── css/               # Estilos CSS
│   ├── img/               # Imagens e logos
│   └── js/                # Scripts JavaScript
├── config/                # Configurações do sistema
│   ├── bootstrap.php      # Bootstrap da aplicação
│   ├── csrf.php          # Proteção CSRF
│   └── database.php      # Configuração do banco
├── controllers/           # Controladores MVC
│   ├── alunos/           # Gestão de estudantes
│   ├── cursos/           # Gestão de cursos
│   ├── disciplinas/      # Gestão de disciplinas
│   ├── docentes/         # Gestão de professores
│   └── matriculas/       # Gestão de matrículas
├── database/             # Scripts de banco de dados
├── docs/                 # Documentação do projeto
├── logs/                 # Logs da aplicação
├── models/               # Modelos de dados
├── tests/                # Testes automatizados
├── vendor/               # Dependências do Composer
└── views/                # Views (interface)
    ├── alunos/           # Interface de estudantes
    ├── cursos/           # Interface de cursos
    ├── layouts/          # Layouts base
    └── relatorios/       # Relatórios
```

## Módulos Principais

### 1. Gestão de Estudantes (Alunos)
- **Localização:** `models/aluno.php`, `views/alunos/`, `controllers/alunos/`
- **Funcionalidades:**
  - Cadastro de novos estudantes
  - Edição de dados pessoais
  - Listagem com paginação e busca
  - Exclusão de registros
  - Autocomplete para seleção rápida

**Campos principais:**
- `matricula`: Identificação única do estudante
- `nome`: Nome completo
- `posto`: Nível de inglês (adaptado do campo original)
- `forca`: Telefone/Instituição de origem

### 2. Gestão de Cursos (Formações)
- **Localização:** `models/curso.php`, `views/cursos/`, `controllers/cursos/`
- **Funcionalidades:**
  - Criação de novos cursos de inglês
  - Gestão de informações do curso
  - Vinculação de professores e disciplinas
  - Matrícula de estudantes
  - Controle de situação (Em Andamento, Concluído, etc.)

**Campos principais:**
- `curso`: Nome do curso
- `ano`: Ano de realização
- `turma`: Identificação da turma
- `inicio/termino`: Datas do curso
- `tipo_capacitacao`: Nível do curso (Básico, Intermediário, Avançado, etc.)
- `modalidade`: Presencial, Online, Híbrido
- `carga_horaria`: Horas de duração
- `preco`: Valor do curso (campo migrado)

### 3. Gestão de Professores (Docentes)
- **Localização:** `models/docente.php`, `views/docentes/`, `controllers/docentes/`
- **Funcionalidades:**
  - Cadastro automático a partir de estudantes
  - Vinculação a cursos e disciplinas
  - Gestão de dados profissionais
  - Controle de classificações

**Campos principais:**
- `matricula`: Identificação única
- `nome`: Nome completo
- `cargo`: Especialização/Nível
- `classificacao`: Tipo de professor (Titular, Conteudista, etc.)
- `carga_horaria`: Carga horária de trabalho

### 4. Gestão de Matrículas
- **Localização:** `models/matricula.php`, `controllers/matriculas/`
- **Funcionalidades:**
  - Matrícula de estudantes em cursos
  - Controle de situação (Ativo, Concluído, Desistente)
  - Relatórios de participação
  - Gestão de notas e frequência (campos migrados)

### 5. Gestão de Disciplinas
- **Localização:** `models/disciplina.php`, `views/disciplinas/`
- **Funcionalidades:**
  - Cadastro de disciplinas
  - Associação com cursos
  - Definição de carga horária

## Funcionalidades Especiais

### 1. Sistema de Tradução Automática
- **Arquivo:** `translate_to_english_school.php`
- **Função:** Migra terminologia militar para contexto de escola de inglês
- **Mapeamentos principais:**
  - "Capacitação" → "Curso de Inglês"
  - "Participantes" → "Estudantes"
  - "Formação" → "Curso"

### 2. Autocomplete Inteligente
- **Localização:** `controllers/alunos/search_autocomplete.php`
- **Funcionalidades:**
  - Busca em tempo real por matrícula ou nome
  - Preenchimento automático de formulários
  - Integração com sistema de professores

### 3. Sistema de Relatórios
- **Localização:** `views/relatorios/`
- **Tipos:**
  - Relatório geral de cursos
  - Lista de presença
  - Certidões de discentes e docentes
  - Relatório de estudantes por curso

### 4. Controle de Expiração
- **Localização:** `config/database.php`
- **Função:** Controla acesso ao sistema baseado em data de expiração

## Banco de Dados

### Tabelas Principais

#### 1. `usuarios`
Sistema de autenticação e autorização.
```sql
- id (PK)
- cognome (login único)
- senha
- nome
- tipo (ADMINISTRADOR, USUARIO)
```

#### 2. `alunos` (Estudantes)
```sql
- id (PK)
- matricula (UNIQUE)
- nome
- posto (nível de inglês)
- forca (telefone/instituição)
```

#### 3. `formacoes` (Cursos)
```sql
- id (PK)
- curso
- ano
- turma
- inicio/termino
- situacao
- tipo_capacitacao (nível do curso)
- modalidade
- carga_horaria
- preco (migrado)
- max_alunos (migrado)
```

#### 4. `docentes` (Professores)
```sql
- id (PK)
- matricula
- nome
- cargo
- classificacao
- carga_horaria
- (+ vários campos de qualificação)
```

#### 5. `matriculas`
```sql
- id (PK)
- formacao_id (FK)
- estudante_id (FK)
- situacao
- nota_final (migrado)
- frequencia (migrado)
- status_curso (migrado)
```

#### 6. `disciplinas`
```sql
- id (PK)
- nome
- descricao
- carga_horaria
```

#### 7. `curso_professores` (Pivot)
```sql
- id (PK)
- formacao_id (FK)
- professor_id (FK)
- disciplina_id (FK)
- created_by
- created_at
```

### Views Criadas na Migração

#### `view_estudantes_ativos`
Relatório de estudantes ativos com estatísticas de cursos.

#### `view_cursos_resumo`
Resumo de cursos com contagem de alunos e vagas disponíveis.

## Arquitetura de Segurança

### 1. Proteção CSRF
- **Arquivo:** `config/csrf.php`
- **Implementação:** Tokens únicos por sessão
- **Validação:** Obrigatória em todas as operações POST

### 2. Sanitização de Dados
- **Input:** `filter_input()` com filtros apropriados
- **Output:** `htmlspecialchars()` para prevenção XSS
- **SQL:** Prepared statements obrigatórios

### 3. Controle de Acesso
- **Sessões:** Controle baseado em `$_SESSION['tipo']`
- **Níveis:** ADMINISTRADOR, USUARIO
- **Páginas:** Verificação em cada controlador

### 4. Logging
- **Biblioteca:** Monolog
- **Localização:** `logs/app.log`
- **Eventos:** Criação, edição, exclusão de registros

## Dependências do Composer

### Produção
```json
{
  "vlucas/phpdotenv": "^5.5",     // Variáveis de ambiente
  "monolog/monolog": "^3.2"       // Sistema de logs
}
```

### Desenvolvimento
```json
{
  "phpunit/phpunit": "^9.5",              // Testes unitários
  "friendsofphp/php-cs-fixer": "^3.0"     // Padrão de código
}
```

## Testes Automatizados

### Estrutura
- **Localização:** `tests/`
- **Framework:** PHPUnit 9.5
- **Configuração:** `phpunit.xml`

### Testes Implementados
1. **AlunoTest**: Testa modelo de estudantes
2. **CursoTest**: Testa modelo de cursos
3. **DocenteTest**: Testa modelo de professores
4. **CursoInstrutorTest**: Testa relacionamentos

### Execução
```bash
composer test
# ou
vendor/bin/phpunit --colors=always
```

## API Endpoints

### Busca de Estudantes
- **URL:** `controllers/alunos/search_autocomplete.php`
- **Método:** GET
- **Parâmetros:** `query`, `type`, `limit`
- **Resposta:** JSON com dados do estudante

### Busca de Professores
- **URL:** `controllers/docentes/search.php`
- **Método:** GET
- **Parâmetros:** `query`, `mode`
- **Resposta:** JSON com dados do professor

### Informações do Curso
- **URL:** `controllers/cursos/get_curso_info.php`
- **Método:** GET
- **Parâmetros:** `id`
- **Resposta:** JSON com detalhes do curso

## Fluxos de Trabalho

### 1. Cadastro de Novo Curso
1. Acesso: `views/cursos/create.php`
2. Preenchimento de dados básicos
3. Submissão para `controllers/cursos/create.php`
4. Validação e criação no banco
5. Redirecionamento para listagem

### 2. Vinculação de Professor
1. Acesso: `views/cursos/add_instructor.php?id={curso_id}`
2. Escolha entre modo de busca (matrícula/nome/participante)
3. Preenchimento automático via autocomplete
4. Submissão para `controllers/cursos/add_instructor.php`
5. Criação automática de professor se não existir
6. Vinculação na tabela pivot

### 3. Matrícula de Estudante
1. Acesso: `views/cursos/enroll.php?id={curso_id}`
2. Busca de estudante existente
3. Seleção e confirmação
4. Criação de registro em `matriculas`
5. Atualização de contadores

## Configuração e Instalação

### 1. Requisitos do Sistema
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx
- Composer
- Extensões PHP: PDO, PDO_MySQL, mbstring

### 2. Processo de Instalação
1. Clone do repositório
2. Execução de `composer install`
3. Configuração do arquivo `.env`
4. Acesso a `install.php`
5. Configuração do banco de dados
6. Login com usuário padrão (ADMIN/1234)

### 3. Configuração de Ambiente
```env
DB_HOST=localhost
DB_NAME=sisenglish_db
DB_USER=root
DB_PASS=senha
```

## Migração e Evolução

### Script de Migração
- **Arquivo:** `database/migrate_to_english_school.sql`
- **Função:** Adapta estrutura militar para escola de inglês
- **Alterações:**
  - Renomeia campos e valores
  - Adiciona campos específicos (preço, máximo de alunos)
  - Cria views para relatórios
  - Atualiza terminologia

### Correção de Acentuação
- **Arquivo:** `fix_accents.php`
- **Função:** Corrige encoding de caracteres especiais
- **Alcance:** Todas as tabelas principais

## Manutenção e Monitoramento

### 1. Logs da Aplicação
- **Localização:** `logs/app.log`
- **Formato:** JSON estruturado
- **Rotação:** Manual

### 2. Backup de Dados
- Backup regular do banco MySQL
- Backup de arquivos de upload (se implementado)
- Versionamento de código via Git

### 3. Atualizações
- Testes em ambiente de desenvolvimento
- Migração de banco quando necessário
- Atualização de dependências via Composer

## Considerações de Performance

### 1. Paginação
- Implementada em todas as listagens
- 10 itens por página (configurável)
- Queries otimizadas com LIMIT/OFFSET

### 2. Cache
- Cache de sessão para dados do usuário
- Prepared statements reutilizáveis
- Otimização de queries com índices

### 3. Frontend
- Uso de Bootstrap para responsividade
- JavaScript para interações dinâmicas
- Autocomplete otimizado com debouncing

## Limitações Conhecidas

1. **Sem suporte a uploads de arquivo**
2. **Sistema de backup manual**
3. **Relatórios limitados a HTML/impressão**
4. **Não possui API REST completa**
5. **Interface não totalmente responsiva**

## Roadmap Futuro

### Funcionalidades Planejadas
1. Sistema de notas e avaliações
2. Controle de presença por aula
3. Geração de certificados automática
4. Dashboard com gráficos
5. Sistema de notificações
6. API REST completa
7. Interface mobile dedicada

### Melhorias Técnicas
1. Implementação de framework (Laravel/Symfony)
2. Migração para PHP 8+
3. Implementação de cache Redis
4. Testes de integração
5. CI/CD automatizado
6. Containerização com Docker

---

**Desenvolvido para gestão acadêmica de escolas de inglês**  
**Baseado em SISCAP (Sistema de Capacitação)**  
**Documentação gerada em:** Junho 2025
