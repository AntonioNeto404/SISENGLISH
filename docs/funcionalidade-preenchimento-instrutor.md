# Funcionalidade de Pré-preenchimento de Campos de Instrutor

## Descrição
Foi implementada uma funcionalidade que pré-preenche automaticamente os campos "Disciplina" e "Carga Horária" na tela de cadastro de instrutores, baseado nas informações da capacitação selecionada.

## Como Funciona

### 1. Quando o usuário clica no botão "Instrutor" 
- O sistema redireciona para a página `add_instructor.php` passando o ID da capacitação
- A página automaticamente carrega as informações da capacitação via AJAX

### 2. Pré-preenchimento automático
- **Campo Disciplina**: É preenchido com o nome da capacitação
- **Campo Carga Horária**: É preenchido com a carga horária da capacitação
- Os campos ficam com destaque visual (fundo ligeiramente diferente e borda azul)
- Uma notificação informa que os campos foram pré-preenchidos

### 3. Campos editáveis
- Mesmo pré-preenchidos, os campos podem ser alterados pelo usuário
- Quando o usuário começar a digitar, o destaque visual é removido
- As alterações são salvas normalmente

## Arquivos Modificados

### 1. `controllers/cursos/get_curso_info.php` (NOVO)
- Endpoint AJAX que retorna informações da capacitação em formato JSON
- Inclui nome do curso, ano, turma, carga horária e disciplina padrão

### 2. `views/cursos/add_instructor.php` (MODIFICADO)
- Adicionado JavaScript para carregar automaticamente as informações do curso
- Adicionado CSS para destacar campos pré-preenchidos
- Melhorada a experiência do usuário com feedback visual
- Adicionadas explicações nos campos sobre o pré-preenchimento

## Benefícios

1. **Agilidade**: Reduz o tempo de cadastro de instrutores
2. **Consistência**: Evita erros de digitação no nome da disciplina
3. **Flexibilidade**: Permite alteração dos campos quando necessário
4. **Usabilidade**: Interface clara e intuitiva com feedback visual

## Compatibilidade
- Funciona com navegadores modernos que suportam JavaScript ES6+
- Compatível com Bootstrap 5
- Usa fetch API para requisições AJAX
