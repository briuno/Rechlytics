# Rechlytics 🚀📊

**Rechlytics** é um sistema web desenvolvido para disponibilizar **dashboards interativos** para clientes, a partir de dados tratados e gerados no **Power BI**.  

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
---

## 📌 Estrutura de Pastas

📂 **Rechlytics/** _(Diretório principal)_  
│── 📂 **assets/** _(Futuramente para armazenar CSS, JS, imagens)_  
│  
│── 📂 **config/** _(Configurações e Banco de Dados)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `config.php` _(Configurações gerais do sistema)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `db.php` _(Configuração do MySQL)_  
│  
│── 📂 **controllers/** _(Regras de Negócio, Login, Segurança, Chat)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `atualizar_senha.php` _(Processo de atualização de senha)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `auth.php` _(Processo de login e autenticação)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `email.php` _(Envio de e-mails com PHPMailer)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `get_mensagens.php` _(Recuperação de mensagens do chat)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `log.php` _(Registro de logs do sistema)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `session_check.php` _(Verifica se o usuário está logado)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `session_check_admin.php` _(Verifica se o usuário é admin)_  
│  
│── 📂 **vendor/** _(Dependências instaladas pelo Composer)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── 📂 `composer/`  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── 📂 `phpmailer/` _(Biblioteca de e-mails)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `autoload.php` _(Carregamento automático de classes)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `composer.json` _(Gerenciamento de dependências)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `composer.lock` _(Controle de versões)_  
│  
│── 📂 **views/** _(Telas visíveis para os usuários)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `index.php` _(Página inicial)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `login.php` _(Tela de login)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `cadastro.php` _(Tela de cadastro)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `dashboard.php` _(Painel do Cliente)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `perfil.php` _(Edição de perfil)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `chat.php` _(Chat com suporte)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `ver_dashboard.php` _(Exibição do dashboard)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `logout.php` _(Encerrar sessão)_  
│  
│── 📂 **views/admin/** _(Área administrativa)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `admin_dashboard.php` _(Painel Admin)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `admin_dashboards.php` _(Gerenciar dashboards)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `admin_logs.php` _(Ver auditoria de logs)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `admin_chat.php` _(Gerenciar mensagens dos clientes)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `admin_editar_usuario.php` _(Editar usuários)_  
│  
│── 📂 **views/auth/** _(Autenticação e segurança)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `ativar_conta.php` _(Ativação de conta)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `esq_senha.php` _(Esqueci minha senha)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `nova_senha.php` _(Redefinir senha)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `redefinir_senha.php` _(Confirmação de senha)_  
│&nbsp;&nbsp;&nbsp;&nbsp; ├── `verificar_2fa.php` _(Autenticação em dois fatores)_  
│  
│── `README.md` _(Este arquivo)_  

---

## 📌 Instalação e Configuração

### **1️⃣ Clone o Repositório**
```bash
git clone https://github.com/seu-usuario/Rechlytics.git
cd Rechlytics
```

### **2️⃣ Instale as Dependências (PHPMailer)**
```bash
composer install
```

### **3️⃣ Configure o Banco de Dados**  
- Importe o arquivo `banco.sql` no **MySQL**.  
- Atualize as credenciais no `config/db.php`.  

### **4️⃣ Inicie o Servidor Local**
```bash
php -S localhost:8000 -t public
```

### **5️⃣ Acesse no Navegador**
```plaintext
http://localhost:8000/views/login.php
```

---

## 📌 Considerações Finais  
O **Rechlytics** está pronto para ser expandido e adaptado conforme necessário. Caso precise de mais melhorias ou funcionalidades, basta abrir uma **issue** no repositório! 🚀  

Se precisar de suporte, me avise! 😊  
