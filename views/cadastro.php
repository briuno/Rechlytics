<?php
session_start();
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../controllers/log.php'; // Para registrar o cadastro
include __DIR__ . '/../controllers/email.php'; // Para enviar e-mail de ativação

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $empresa = trim($_POST['empresa']);

    // Prevenir XSS
    $nome = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $cpf = htmlspecialchars($cpf, ENT_QUOTES, 'UTF-8');
    $telefone = htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8');
    $endereco = htmlspecialchars($endereco, ENT_QUOTES, 'UTF-8');
    $empresa = htmlspecialchars($empresa, ENT_QUOTES, 'UTF-8');

    // Verificar se as senhas coincidem
    if ($senha !== $confirma_senha) {
        echo "<p>As senhas não coincidem.</p>";
        exit();
    }

    // Verificar se o e-mail ou CPF já está cadastrado
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? OR cpf = ?");
    $stmt->bind_param("ss", $email, $cpf);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "<p>Este e-mail ou CPF já está cadastrado.</p>";
        exit();
    }

    // Criar hash seguro da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Cadastrar usuário
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, cpf, telefone, endereco, empresa, tipo, email_verificado) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, 'cliente', 0)");
    $stmt->bind_param("sssssss", $nome, $email, $senha_hash, $cpf, $telefone, $endereco, $empresa);
    
    if ($stmt->execute()) {
        $usuario_id = $stmt->insert_id;
        registrarLog($conn, $usuario_id, "Novo usuário cadastrado");

    // Criar token de ativação
    $token = bin2hex(random_bytes(16));

    // Atualizar token na coluna reset_token para ativação da conta
    $stmt = $conn->prepare("UPDATE usuarios SET reset_token = ?, reset_token_expira = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE id = ?");
    $stmt->bind_param("si", $token, $usuario_id);
    $stmt->execute();

    // Enviar e-mail de ativação
    $ativacao_link = "https://rechlytics.com/views/auth/ativar_conta.php?email=$email&token=$token";
    enviarEmail($email, "Confirme seu cadastro", "Clique no link para ativar sua conta: $ativacao_link");


        echo "<p>Cadastro realizado com sucesso! Verifique seu e-mail para ativar sua conta.</p>";
    } else {
        echo "<p>Erro ao cadastrar usuário.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Rechlytics</title>
</head>
<body>
    <h2>Cadastro de Usuário</h2>
    <form action="views/cadastro.php" method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>CPF:</label>
        <input type="text" name="cpf" required>

        <label>Telefone:</label>
        <input type="text" name="telefone" required>

        <label>Endereço:</label>
        <input type="text" name="endereco">

        <label>Empresa (opcional):</label>
        <input type="text" name="empresa">

        <label>Senha:</label>
        <input type="password" name="senha" required>
        
        <label>Confirme a Senha:</label>
        <input type="password" name="confirma_senha" required>

        <button type="submit">Cadastrar</button>
    </form>
    <p><a href="views/login.php">Já tem uma conta? Faça login</a></p>
</body>
</html>
