# Changelog - SISENGLISH

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Não Lançado]

### Planejado
- Sistema de notas e avaliações por disciplina
- Controle de presença individual por aula
- Geração automática de certificados
- Dashboard com gráficos e estatísticas
- Notificações por email
- API REST completa
- Interface mobile responsiva

## [1.0.0] - 2025-06-09

### Adicionado

#### Funcionalidades Principais
- **Sistema de Gestão de Estudantes**
  - Cadastro, edição e exclusão de estudantes
  - Busca com autocomplete por nome e matrícula
  - Paginação com 10 itens por página
  - Validação de dados obrigatórios

- **Sistema de Gestão de Cursos**
  - Criação e gerenciamento de cursos de inglês
  - Diferentes níveis: Básico, Intermediário, Avançado, Business English
  - Modalidades: Presencial, Online, Híbrido
  - Controle de datas, carga horária e capacidade

- **Sistema de Matrículas**
  - Matrícula de estudantes em cursos
  - Controle de situação (Ativo, Concluído, Desistente)
  - Matrícula automática durante cadastro de estudante
  - Validação de matrícula duplicada

- **Sistema de Professores**
  - Criação automática a partir de estudantes
  - Vinculação a cursos e disciplinas
  - Diferentes classificações (Titular, Conteudista, Tutor, etc.)
  - Controle de carga horária

- **Sistema de Disciplinas**
  - Cadastro e gerenciamento de disciplinas
  - Associação com cursos e professores
  - Criação automática durante vinculação

#### Funcionalidades Avançadas
- **Autocomplete Inteligente**
  - Busca em tempo real por estudantes
  - Preenchimento automático de formulários
  - Suporte a busca por matrícula e nome

- **Sistema de Relatórios**
  - Relatório geral de cursos
  - Lista de presença por curso
  - Certidões de discentes e docentes
  - Exportação para impressão

- **Sistema de Tradução**
  - Script automático de migração de terminologia
  - Conversão de contexto militar para escola de inglês
  - Mapeamento de termos e valores

#### Segurança
- **Proteção CSRF** em todos os formulários
- **Sanitização** de entrada de dados
- **Prepared Statements** para queries SQL
- **Controle de Sessão** baseado em roles
- **Sistema de Logs** com Monolog

#### Qualidade de Código
- **Testes Automatizados** com PHPUnit
- **PHP-CS-Fixer** para padrão de código
- **Composer** para gerenciamento de dependências
- **Autoload PSR-4** para classes

### Modificado

#### Migração do SISCAP
- **Estrutura de Banco**
  - Adaptação de tabelas para contexto de escola de inglês
  - Adição de campos específicos (preço, máximo de alunos)
  - Criação de views para relatórios

- **Terminologia**
  - "Capacitações" → "Cursos de Inglês"
  - "Participantes" → "Estudantes"
  - "Formações" → "Cursos"
  - Níveis adaptados para ensino de inglês

#### Interface
- **Layout Moderno** com Bootstrap 5
- **Navegação Intuitiva** com breadcrumbs
- **Responsividade** parcial para dispositivos móveis
- **Feedback Visual** para ações do usuário

### Corrigido
- **Encoding UTF-8** para caracteres especiais
- **Validação de Datas** em criação de cursos
- **Duplicação de Matrículas** no mesmo curso
- **Sanitização** de dados de entrada
- **Logs de Auditoria** para todas as operações

## [0.9.0] - 2025-06-01 (SISCAP)

### Base Original (Sistema de Capacitação)
- Sistema básico de gestão de participantes
- Controle de formações militares
- Relatórios simples
- Interface básica com Bootstrap 4

---

## Tipos de Mudanças

- `Added` para novas funcionalidades
- `Changed` para mudanças em funcionalidades existentes
- `Deprecated` para funcionalidades que serão removidas
- `Removed` para funcionalidades removidas
- `Fixed` para correções de bugs
- `Security` para correções de vulnerabilidades

## Roadmap

### v1.1.0 (Planejado para Q3 2025)
- Sistema de notas e avaliações
- Controle de presença por aula
- Dashboard com estatísticas
- Melhorias na interface mobile

### v1.2.0 (Planejado para Q4 2025)
- API REST completa
- Sistema de notificações
- Geração automática de certificados
- Integração com sistemas externos

### v2.0.0 (Planejado para 2026)
- Refatoração para framework moderno (Laravel/Symfony)
- Interface completamente responsiva
- Sistema de permissões granular
- Multi-tenancy para múltiplas escolas

---

**Mantido por:** Equipe de Desenvolvimento SISENGLISH  
**Última atualização:** 9 de Junho de 2025
