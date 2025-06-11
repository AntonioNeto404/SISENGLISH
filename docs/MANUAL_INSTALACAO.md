# Manual de Instalação - SISENGLISH

## Pré-requisitos

### Servidor Web
- **Apache 2.4+** ou **Nginx 1.18+**
- **PHP 7.4** ou superior (recomendado PHP 8.1+)
- **MySQL 5.7+** ou **MariaDB 10.3+**

### Extensões PHP Obrigatórias
```bash
php-pdo
php-pdo-mysql
php-mbstring
php-json
php-openssl
php-zip
php-curl
```

### Ferramentas de Desenvolvimento
- **Composer** (gerenciador de dependências)
- **Git** (controle de versão)

## Instalação Passo a Passo

### 1. Download e Configuração Inicial

```bash
# Clone o repositório
git clone https://github.com/seu-usuario/sisenglish.git
cd sisenglish

# Instalar dependências
composer install

# Copiar arquivo de ambiente
cp .env.example .env
```

### 2. Configuração do Banco de Dados

#### 2.1 Criar Base de Dados
```sql
CREATE DATABASE sisenglish_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
```

#### 2.2 Configurar Arquivo .env
```env
# Configurações do Banco de Dados
DB_HOST=localhost
DB_NAME=sisenglish_db
DB_USER=seu_usuario
DB_PASS=sua_senha

# Configurações da Aplicação
APP_NAME="SISENGLISH"
APP_ENV=production
APP_DEBUG=false
```

### 3. Instalação via Interface Web

1. **Acesse:** `http://seu-dominio.com/install.php`
2. **Preencha os dados de conexão:**
   - Host do banco de dados
   - Nome da base de dados
   - Usuário e senha
3. **Execute a instalação**
4. **Login inicial:**
   - Usuário: `ADMIN`
   - Senha: `1234`

### 4. Configuração do Servidor Web

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Proteção de arquivos sensíveis
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name seu-dominio.com;
    root /var/www/sisenglish;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Bloquear arquivos sensíveis
    location ~ /\.(env|git) {
        deny all;
    }
}
```

### 5. Permissões de Arquivos

```bash
# Definir proprietário correto
chown -R www-data:www-data /var/www/sisenglish

# Permissões de diretórios
find /var/www/sisenglish -type d -exec chmod 755 {} \;

# Permissões de arquivos
find /var/www/sisenglish -type f -exec chmod 644 {} \;

# Permissões especiais para logs
chmod 777 logs/
chmod 666 logs/app.log
```

## Verificação da Instalação

### 1. Teste de Conectividade
- Acesse a página inicial
- Verifique se não há erros PHP
- Teste o login com credenciais padrão

### 2. Teste de Funcionalidades
- Criar um novo estudante
- Criar um novo curso
- Fazer uma matrícula
- Gerar um relatório

### 3. Verificação de Logs
```bash
tail -f logs/app.log
```

## Migração de Dados (Se Aplicável)

### Do SISCAP para SISENGLISH

```bash
# 1. Backup da base atual
mysqldump -u usuario -p siscap03_db > backup_siscap.sql

# 2. Executar script de migração
mysql -u usuario -p sisenglish_db < database/migrate_to_english_school.sql

# 3. Executar correção de acentuação
php fix_accents.php
```

## Solução de Problemas Comuns

### Erro de Conexão com Banco
```
Solução:
1. Verificar credenciais no .env
2. Testar conexão manual: mysql -u usuario -p
3. Verificar se MySQL está rodando: systemctl status mysql
```

### Erro de Permissões
```
Solução:
1. Verificar proprietário dos arquivos
2. Ajustar permissões conforme seção 5
3. Verificar SELinux se aplicável
```

### Erro 500 - Internal Server Error
```
Solução:
1. Verificar logs do Apache/Nginx
2. Verificar logs PHP: tail -f /var/log/php_errors.log
3. Verificar sintaxe: php -l index.php
```

### Problemas com Composer
```bash
# Limpar cache
composer clear-cache

# Reinstalar dependências
rm -rf vendor/
composer install
```

## Configurações de Produção

### 1. Segurança
```bash
# Remover arquivos de desenvolvimento
rm install.php
rm translate_to_english_school.php
rm fix_accents.php

# Configurar .env para produção
APP_DEBUG=false
```

### 2. Performance
```php
// Configurar PHP.ini
memory_limit = 256M
max_execution_time = 30
upload_max_filesize = 10M
post_max_size = 10M
```

### 3. Backup Automatizado
```bash
#!/bin/bash
# Script de backup - salvar como backup.sh
BACKUP_DIR="/var/backups/sisenglish"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup do banco
mysqldump -u usuario -p sisenglish_db > $BACKUP_DIR/db_$DATE.sql

# Backup dos arquivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/sisenglish

# Manter apenas últimos 7 dias
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

### 4. Monitoramento
```bash
# Crontab para verificação de saúde
*/5 * * * * curl -f http://seu-dominio.com/health.php || echo "Site down" | mail admin@empresa.com
```

## Atualizações

### Processo de Atualização
```bash
# 1. Backup completo
./backup.sh

# 2. Baixar nova versão
git pull origin main

# 3. Atualizar dependências
composer install --no-dev

# 4. Executar migrações se houver
php migrate.php

# 5. Limpar cache se implementado
php artisan cache:clear
```

## Suporte e Manutenção

### Contatos
- **Desenvolvedor:** [Seu Nome]
- **Email:** seu-email@empresa.com
- **Telefone:** (XX) XXXX-XXXX

### Documentação Adicional
- [Manual do Usuário](MANUAL_USUARIO.md)
- [Documentação da API](API_DOCUMENTATION.md)
- [Guia de Desenvolvimento](DESENVOLVIMENTO.md)

---

**Data da última atualização:** Junho 2025  
**Versão do documento:** 1.0
