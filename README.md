# Rechlytics

Rechlytics é um sistema web para disponibilizar dashboards interativos do Power BI aos clientes. Ele permite que usuários finais visualizem relatórios em um ambiente seguro enquanto administradores gerenciam o conteúdo e prestam suporte.

## Funcionalidades

- **Autenticação com hash de senha** usando `password_hash`.
- **Autenticação em dois fatores (2FA)** via e‑mail.
- Limite de tentativas de login para prevenir força bruta.
- Recuperação de senha por e‑mail.
- Gestão de dashboards vinculados a cada cliente.
- Chat entre clientes e equipe de suporte.
- Painel administrativo com gestão de usuários, dashboards e logs de auditoria.

## Configuração Rápida

1. Edite `config/db.php` com as credenciais do seu banco MySQL.
2. Importe as tabelas descritas abaixo no seu banco de dados.
3. Hospede os arquivos PHP em um servidor com suporte a PHP e acesso ao banco.
4. Acesse `index.php` para realizar o login ou cadastro.

## Estrutura do Banco de Dados

### Tabela `usuarios`
```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NULL,
    cpf VARCHAR(14) UNIQUE NULL,
    empresa VARCHAR(150) NULL,
    endereco TEXT NULL,
    tipo ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente',
    email_verificado TINYINT(1) DEFAULT 0,
    two_factor_code VARCHAR(6) NULL,
    two_factor_expira DATETIME NULL,
    reset_token VARCHAR(64) NULL,
    reset_token_expira DATETIME NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
Se já possuir essa tabela sem as colunas de recuperação de senha, execute o `ALTER TABLE` abaixo:
```sql
ALTER TABLE usuarios
    ADD reset_token VARCHAR(64) NULL,
    ADD reset_token_expira DATETIME NULL;
```

### Tabela `dashboards`
```sql
CREATE TABLE dashboards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    url TEXT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
```

### Tabela `chat_mensagens`
```sql
CREATE TABLE chat_mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    mensagem TEXT NOT NULL,
    remetente ENUM('cliente', 'admin') NOT NULL,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
```

### Tabela `logs`
```sql
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    acao TEXT NOT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);
```

## Estrutura de Diretórios

```
Rechlytics/
├── index.php
├── config/
│   ├── config.php
│   └── db.php
├── controllers/
│   ├── auth.php
│   ├── email.php
│   ├── get_mensagens.php
│   ├── log.php
│   ├── session_check.php
│   └── session_check_admin.php
├── vendor/
└── views/
    ├── auth/
    │   ├── ativar_conta.php
    │   ├── esq_senha.php
    │   ├── redefinir_senha.php
    │   └── verificar_2fa.php
    ├── admin/
    │   ├── admin_chat.php
    │   ├── admin_dashboard.php
    │   ├── admin_dashboards.php
    │   ├── admin_editar_usuario.php
    │   └── admin_logs.php
    ├── cadastro.php
    ├── chat.php
    ├── dashboard.php
    ├── login.php
    ├── logout.php
    ├── perfil.php
    └── ver_dashboard.php
```

## Contribuições

Sugestões e melhorias são bem‑vindas! Abra uma issue ou envie um pull request.

