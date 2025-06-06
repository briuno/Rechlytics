<?php
session_start();
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../controllers/log.php';
include __DIR__ . '/../controllers/email.php';

$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 2), '/');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $empresa = trim($_POST['empresa']);

    $nome = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $cpf = htmlspecialchars($cpf, ENT_QUOTES, 'UTF-8');
    $telefone = htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8');
    $endereco = htmlspecialchars($endereco, ENT_QUOTES, 'UTF-8');
    $empresa = htmlspecialchars($empresa, ENT_QUOTES, 'UTF-8');

    if ($senha !== $confirma_senha) {
        $_SESSION['msg'] = 'As senhas não coincidem!';
        header("Location: $base_url/views/cadastro.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? OR cpf = ?");
    $stmt->bind_param("ss", $email, $cpf);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['msg'] = 'Este e-mail ou CPF já está cadastrado.';
        header("Location: $base_url/views/cadastro.php");
        exit();
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, cpf, telefone, endereco, empresa, tipo, email_verificado, reset_token, reset_token_expira) VALUES (?, ?, ?, ?, ?, ?, ?, 'cliente', 0, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))");
    $token = bin2hex(random_bytes(16));
    $stmt->bind_param("ssssssss", $nome, $email, $senha_hash, $cpf, $telefone, $endereco, $empresa, $token);

    if ($stmt->execute()) {
        $usuario_id = $stmt->insert_id;
        registrarLog($conn, $usuario_id, 'Novo usuário cadastrado');

        $ativacao_link = "$base_url/views/auth/ativar_conta.php?email=$email&token=$token";
        enviarEmail($email, 'Confirme seu cadastro', 'Clique no link para ativar sua conta: ' . $ativacao_link);

        $_SESSION['msg'] = 'Cadastro realizado com sucesso! Verifique seu e-mail para ativar sua conta.';
        header("Location: $base_url/views/login.php");
        exit();
    } else {
        $_SESSION['msg'] = 'Erro ao cadastrar usuário.';
        header("Location: $base_url/views/cadastro.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
    <link rel="stylesheet" href="../public/css/estilos.css">
    <script src="../public/js/script.js" defer></script>
    <title>Cadastro - Rechlytics</title>
</head>
<body>
    <h2>Cadastro</h2>

    <?php
    if (isset($_SESSION['msg'])) {
        echo '<p>' . $_SESSION['msg'] . '</p>';
        unset($_SESSION['msg']);
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="form-auth">
        <label>Nome:</label>
        <input type="text" name="nome" required>

        <label>Email:</label>
        <input type="text" name="email" required>

        <label>CPF:</label>
        <input type="text" name="cpf" required>

        <label>Telefone:</label>
        <input type="text" name="telefone" required>

        <label>Endereço:</label>
        <input type="text" name="endereco" required>

        <label>Empresa:</label>
        <input type="text" name="empresa" required>

        <label>Senha:</label>
        <input type="password" name="senha" required minlength="8">

        <label>Confirma Senha:</label>
        <input type="password" name="confirma_senha" required minlength="8">

        <button type="submit">Cadastrar</button>
    </form>

    <p><a href="<?php echo $base_url; ?>/views/login.php">Já tem uma conta? Faça login</a></p>
</body>
</html>