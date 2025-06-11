# Documentação da API - SISENGLISH

## Visão Geral

O SISENGLISH possui alguns endpoints de API para integração e funcionalidades dinâmicas. Esta documentação detalha todos os endpoints disponíveis.

**Base URL:** `http://seu-dominio.com/controllers/`

## Autenticação

Todos os endpoints requerem sessão ativa. O sistema usa autenticação baseada em sessões PHP.

**Headers Obrigatórios:**
```
Cookie: PHPSESSID={session_id}
```

**Verificação de Autenticação:**
```php
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit();
}
```

## Endpoints de Estudantes

### GET /alunos/search_autocomplete.php
Busca estudantes para autocomplete.

**Parâmetros:**
- `query` (string, obrigatório): Termo de busca
- `type` (string, opcional): Tipo de busca (`matricula`, `nome`, `all`)
- `limit` (int, opcional): Limite de resultados (padrão: 10)

**Exemplo de Requisição:**
```
GET /alunos/search_autocomplete.php?query=joão&type=nome&limit=5
```

**Resposta de Sucesso (200):**
```json
[
    {
        "id": 1,
        "matricula": "2025001",
        "nome": "JOÃO SILVA",
        "posto": "BÁSICO",
        "forca": "(11) 99999-9999"
    },
    {
        "id": 2,
        "matricula": "2025002", 
        "nome": "JOÃO SANTOS",
        "posto": "INTERMEDIÁRIO",
        "forca": "(11) 88888-8888"
    }
]
```

**Resposta de Erro (401):**
```json
{
    "error": "Unauthorized"
}
```

**Resposta de Erro (403):**
```json
{
    "error": "System expired"
}
```

## Endpoints de Professores

### GET /docentes/search.php
Busca professores para preenchimento automático.

**Parâmetros:**
- `query` (string, obrigatório): Termo de busca
- `mode` (string, opcional): Modo de busca (`matricula`, `nome`)

**Exemplo de Requisição:**
```
GET /docentes/search.php?query=123456&mode=matricula
```

**Resposta de Sucesso (200):**
```json
{
    "id": 5,
    "matricula": "123456",
    "nome": "MARIA PROFESSORA",
    "cargo": "PROFESSORA TITULAR",
    "instituicao": "SISENGLISH",
    "classificacao": "INSTRUTOR TITULAR",
    "carga_horaria": "40"
}
```

**Resposta Vazia (200):**
```json
[]
```

## Endpoints de Cursos

### GET /cursos/get_curso_info.php
Obtém informações detalhadas de um curso.

**Parâmetros:**
- `id` (int, obrigatório): ID do curso

**Exemplo de Requisição:**
```
GET /cursos/get_curso_info.php?id=1
```

**Resposta de Sucesso (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "curso": "INGLÊS BÁSICO",
        "ano": "2025",
        "turma": "A",
        "disciplina_padrao": "Inglês Básico - Conversação",
        "carga_horaria_curso": 80,
        "nivel": "BÁSICO",
        "modalidade": "PRESENCIAL"
    }
}
```

**Resposta de Erro (404):**
```json
{
    "success": false,
    "error": "Curso não encontrado"
}
```

### GET /cursos/search.php
Busca nomes de cursos para autocomplete.

**Parâmetros:**
- `term` (string, obrigatório): Termo de busca

**Exemplo de Requisição:**
```
GET /cursos/search.php?term=inglês
```

**Resposta de Sucesso (200):**
```json
[
    "INGLÊS BÁSICO",
    "INGLÊS INTERMEDIÁRIO", 
    "INGLÊS AVANÇADO",
    "INGLÊS BUSINESS"
]
```

## Códigos de Status HTTP

| Código | Significado | Descrição |
|--------|-------------|-----------|
| 200 | OK | Requisição bem-sucedida |
| 401 | Unauthorized | Usuário não autenticado |
| 403 | Forbidden | Sistema expirado ou sem permissão |
| 404 | Not Found | Recurso não encontrado |
| 500 | Internal Server Error | Erro interno do servidor |

## Tratamento de Erros

### Estrutura de Erro Padrão
```json
{
    "error": "Mensagem de erro",
    "code": "ERROR_CODE",
    "details": "Detalhes adicionais (opcional)"
}
```

### Erros Comuns

**Sessão Expirada:**
```json
{
    "error": "Unauthorized",
    "code": "SESSION_EXPIRED"
}
```

**Sistema Expirado:**
```json
{
    "error": "System expired",
    "code": "SYSTEM_EXPIRED"
}
```

**Parâmetros Inválidos:**
```json
{
    "error": "Invalid parameters",
    "code": "INVALID_PARAMS",
    "details": "Query parameter is required"
}
```

## Exemplos de Uso

### JavaScript - Autocomplete de Estudantes
```javascript
async function searchStudents(query) {
    try {
        const response = await fetch(
            `/controllers/alunos/search_autocomplete.php?query=${encodeURIComponent(query)}&limit=10`
        );
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const students = await response.json();
        return students;
    } catch (error) {
        console.error('Erro ao buscar estudantes:', error);
        return [];
    }
}

// Uso
searchStudents('joão').then(students => {
    console.log('Estudantes encontrados:', students);
});
```

### JavaScript - Busca de Professor
```javascript
async function findTeacher(query, mode = 'nome') {
    try {
        const response = await fetch(
            `/controllers/docentes/search.php?query=${encodeURIComponent(query)}&mode=${mode}`
        );
        
        const teacher = await response.json();
        return teacher;
    } catch (error) {
        console.error('Erro ao buscar professor:', error);
        return null;
    }
}

// Uso por matrícula
findTeacher('123456', 'matricula').then(teacher => {
    if (teacher && teacher.id) {
        console.log('Professor encontrado:', teacher);
        // Preencher formulário
        document.getElementById('nome').value = teacher.nome;
        document.getElementById('cargo').value = teacher.cargo;
    }
});
```

### JavaScript - Informações do Curso
```javascript
async function loadCourseInfo(courseId) {
    try {
        const response = await fetch(
            `/controllers/cursos/get_curso_info.php?id=${courseId}`
        );
        
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.error);
        }
    } catch (error) {
        console.error('Erro ao carregar curso:', error);
        return null;
    }
}

// Uso
loadCourseInfo(1).then(course => {
    if (course) {
        document.getElementById('disciplina').value = course.disciplina_padrao;
        document.getElementById('carga_horaria').value = course.carga_horaria_curso;
    }
});
```

## Limitações da API

1. **Formato:** Apenas JSON
2. **Autenticação:** Baseada em sessões (não stateless)
3. **CORS:** Não configurado para requests externos
4. **Rate Limiting:** Não implementado
5. **Versionamento:** Não implementado
6. **Documentação OpenAPI:** Não disponível

## Segurança

### Proteções Implementadas
- **Autenticação obrigatória**
- **Verificação de expiração do sistema**
- **Sanitização de parâmetros de entrada**
- **Prepared statements no banco**

### Recomendações
- **HTTPS:** Use sempre HTTPS em produção
- **Headers de Segurança:** Configure headers apropriados
- **Rate Limiting:** Implemente controle de taxa de requisições
- **Validação:** Valide todos os parâmetros de entrada

## Logs e Monitoramento

### Logs da API
As chamadas da API são registradas no arquivo `logs/app.log`:

```json
{
    "timestamp": "2025-06-09 14:30:00",
    "level": "INFO", 
    "endpoint": "/alunos/search_autocomplete.php",
    "user_id": 1,
    "query": "joão",
    "results": 3,
    "response_time": "45ms"
}
```

### Monitoramento
Para monitorar a saúde da API:

```bash
# Verificar logs de erro
tail -f logs/app.log | grep ERROR

# Monitorar uso
grep "search_autocomplete" logs/app.log | wc -l
```

## Roadmap Futuro

### Funcionalidades Planejadas
1. **API REST completa** com todos os recursos CRUD
2. **Autenticação JWT** para stateless
3. **Rate limiting** para controle de uso
4. **Documentação OpenAPI/Swagger**
5. **Webhooks** para integração externa
6. **API de relatórios** em PDF/Excel

### Versionamento
Futuras versões da API seguirão o padrão:
- `v1/` - Versão atual (sem prefixo)
- `v2/` - Próxima versão com breaking changes

---

**SISENGLISH API Documentation**  
**Versão:** 1.0  
**Última atualização:** Junho 2025
