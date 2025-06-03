# Rechlytics 🚀📊

*Rechlytics* é um sistema web desenvolvido para disponibilizar **dashboards interativos** para clientes, a partir de dados tratados e gerados no **Power BI**.  

Os clientes podem acessar seus dashboards exclusivos, interagir com gráficos, baixar relatórios e se comunicar com administradores via chat.  
Os administradores podem gerenciar clientes, dashboards, responder mensagens e monitorar logs de auditoria.

---

## 📌 Funcionalidades Principais

### ✅ **Autenticação e Segurança**
- Login e Logout seguro com **hash de senha (`password_hash`)**.
- **Autenticação em dois fatores (2FA)** via e-mail.
- Limitação de tentativas de login para evitar ataques de força bruta.
- Sistema de recuperação de senha por e-mail.

### ✅ **Gestão de Dashboards**
- **Dashboards interativos via Power BI (iframe)**.
- Cada cliente acessa **apenas seus próprios dashboards**.
- Administração de dashboards pelo painel de administrador.

### ✅ **Chat com Suporte**
- Os clientes podem enviar mensagens para os administradores.
- O suporte responde e as mensagens ficam salvas no histórico.
- Notificações por e-mail sempre que um cliente recebe uma resposta.

### ✅ **Painel Administrativo**
- **Gerenciamento de Clientes** (adicionar, editar e excluir).
- **Gestão de Dashboards** (vincular dashboards aos clientes).
- **Logs de Auditoria** para rastrear atividades no sistema.

---

## 📌 Estrutura do Banco de Dados (MySQL)

O sistema usa **MySQL** como banco de dados, com as seguintes tabelas:

### **🔹 Tabela `usuarios` (Armazena os usuários do sistema)**
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
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### **🔹 Tabela `dashboards` (Gerencia os painéis do Power BI por cliente)**
```sql
CREATE TABLE dashboards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    url TEXT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
```

### **🔹 Tabela `mensagens` (Armazena o chat entre cliente e administrador)**
```sql
CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lida TINYINT(1) DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
```

### **🔹 Tabela `logs` (Registra ações importantes no sistema)**
```sql
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    acao TEXT NOT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);
```

### 🔹 Criando as tabelas automaticamente
Para criar todas as tabelas necessárias em uma nova instalação, execute o script:

```bash
php scripts/create_tables.php
```

O script verifica e cria cada tabela somente se ela ainda não existir.
---

## 📌 Estrutura de Diretórios

```
Rechlytics/
├── index.php          # Página inicial (login)
├── README.md          # Documentação do projeto
│
├── config/            # Configurações gerais do sistema
│   ├── config.php     # Configurações do sistema
│   └── db.php         # Conexão com o banco de dados
│
├── controllers/       # Lógica do backend (autenticação, emails, logs, sessões)
│   ├── auth.php       # Autenticação de usuários
│   ├── email.php      # Envio de emails com PHPMailer
│   ├── get_mensagens.php  # Busca mensagens do chat
│   ├── log.php        # Gerenciamento de logs do sistema
│   ├── session_check.php  # Validação de sessão para usuários
│   └── session_check_admin.php  # Validação de sessão para admins
│
├── scripts/          # Scripts auxiliares
│   └── create_tables.php  # Criação das tabelas

├── vendor/            # Dependências do Composer (PHPMailer, etc.)
│
└── views/             # Páginas da interface do usuário
    ├── auth/          # Páginas de autenticação
    │   ├── ativar_conta.php
    │   ├── esq_senha.php
    │   ├── redefinir_senha.php
    │   ├── verificar_2fa.php
    │
    ├── admin/         # Páginas específicas do administrador
    │   ├── admin_chat.php
    │   ├── admin_dashboard.php
    │   ├── admin_dashboards.php
    │   ├── admin_editar_usuario.php
    │   ├── admin_logs.php
    │
    ├── cadastro.php
    ├── chat.php
    ├── dashboard.php
    ├── login.php
    ├── logout.php
    ├── perfil.php
    ├── ver_dashboard.php
```

---

O **Rechlytics** está pronto para ser expandido e adaptado conforme necessário. Caso precise de mais melhorias ou funcionalidades, basta abrir uma **issue** no repositório! 🚀  

Se precisar de suporte, me avise! 😊

