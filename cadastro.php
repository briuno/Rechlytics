<?php
session_start();
include 'includes/db.php';
include 'includes/log.php'; // Para registrar o cadastro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $empresa = trim($_POST['empresa']);

    // Verificar se as senhas coincidem
    if ($senha !== $confirma_senha) {
        echo "<p style='color: red;'>As senhas não coincidem.</p>";
        exit();
    }

    // Verificar se o e-mail já está cadastrado
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<p style='color: red;'>Este e-mail já está cadastrado.</p>";
        exit();
    }

    // Verificar se o CPF já está cadastrado
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE cpf = ?");
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<p style='color: red;'>Este CPF já está cadastrado.</p>";
        exit();
    }

    // Hash seguro da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Cadastrar usuário
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, cpf, telefone, endereco, empresa, tipo, email_verificado) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, 'cliente', 0)");
    $stmt->bind_param("sssssss", $nome, $email, $senha_hash, $cpf, $telefone, $endereco, $empresa);
    
    if ($stmt->execute()) {
        $usuario_id = $stmt->insert_id;
        registrarLog($conn, $usuario_id, "Novo usuário cadastrado");

        // Enviar e-mail de ativação (opcional)
        include 'includes/email.php';
        $token = bin2hex(random_bytes(16)); // Token seguro
        $ativacao_link = "https://rechlytics.com/ativar_conta.php?email=$email&token=$token";
        enviarEmail($email, "Confirme seu cadastro", "Clique no link para ativar sua conta: $ativacao_link");

        echo "<p style='color: green;'>Cadastro realizado com sucesso! Verifique seu e-mail para ativar sua conta.</p>";
    } else {
        echo "<p style='color: red;'>Erro ao cadastrar usuário.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Rechlytics</title>
    <script>
        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]/g, '');
            if (cpf.length !== 11 || /^(.)\1+$/.test(cpf)) return false;
            let soma = 0, resto;
            for (let i = 1; i <= 9; i++) soma += parseInt(cpf[i - 1]) * (11 - i);
            resto = (soma * 10) % 11;
            if (resto == 10 || resto == 11) resto = 0;
            if (resto != parseInt(cpf[9])) return false;
            soma = 0;
            for (let i = 1; i <= 10; i++) soma += parseInt(cpf[i - 1]) * (12 - i);
            resto = (soma * 10) % 11;
            if (resto == 10 || resto == 11) resto = 0;
            return resto == parseInt(cpf[10]);
        }

        function validarFormulario() {
            let cpf = document.getElementById("cpf").value;
            if (!validarCPF(cpf)) {
                alert("CPF inválido!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <h2>Cadastro de Usuário</h2>
    <form action="cadastro.php" method="POST" onsubmit="return validarFormulario()">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>CPF:</label>
        <input type="text" name="cpf" id="cpf" required>

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
    <p><a href="login.php">Já tem uma conta? Faça login</a></p>
</body>
</html>
