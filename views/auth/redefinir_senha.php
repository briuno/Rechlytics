<?php
session_start();
include __DIR__ . '/../../config/db.php';

// Caminho base dinâmico com domínio correto
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 2), '/');

// Verifica se um token foi passado na URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("<p style='color: red;'>❌ Token inválido.</p>");
}

$token = trim($_GET['token']); // Remove espaços extras

// Depuração: Exibir token recebido
echo "<p>🔍 Token recebido (GET): |" . bin2hex($token) . "|</p>";

// Buscar o token no banco de dados
$stmt = $conn->prepare("SELECT id, reset_token, reset_token_expira FROM usuarios WHERE reset_token = ? AND reset_token_expira > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($usuario_id, $reset_token, $reset_token_expira);
$stmt->fetch();

// Depuração: Exibir informações do banco
echo "<p>📌 Token no banco: |" . ($reset_token ? bin2hex($reset_token) : "NULL") . "|</p>";
echo "<p>⏳ Expira em: " . ($reset_token_expira ? htmlspecialchars($reset_token_expira) : "NULL") . "</p>";

// Verificação avançada dos tokens
if ($stmt->num_rows === 0) {
    echo "<p style='color: red;'>❌ Nenhuma linha encontrada com esse token!</p>";
    die();
} elseif ($token !== $reset_token) {
    echo "<p style='color: red;'>⚠ Os tokens NÃO coincidem!</p>";
    die();
} elseif (strtotime($reset_token_expira) < time()) {
    echo "<p style='color: red;'>⏳ Token expirado!</p>";
    die();
}

$stmt->close();

// Processo de redefinição de senha
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nova_senha = trim($_POST['senha']);
    $confirma_senha = trim($_POST['confirma_senha']);

    if ($nova_senha !== $confirma_senha) {
        $_SESSION['msg'] = "⚠ As senhas não coincidem!";
    } elseif (strlen($nova_senha) < 8) {
        $_SESSION['msg'] = "⚠ A senha deve ter pelo menos 8 caracteres!";
    } else {
        // Criar hash seguro da senha
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Atualizar a senha e remover o token
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ?, reset_token = NULL, reset_token_expira = NULL WHERE id = ?");
        $stmt->bind_param("si", $senha_hash, $usuario_id);
        $stmt->execute();

        $_SESSION['msg'] = "✅ Senha redefinida com sucesso! Faça login.";
        header("Location: $base_url/views/login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha - Rechlytics</title>
</head>
<body>
    <h2>🔑 Redefinir Senha</h2>
    
    <?php
    if (isset($_SESSION['msg'])) {
        echo "<p style='color: red;'>" . $_SESSION['msg'] . "</p>";
        unset($_SESSION['msg']);
    }
    ?>
    
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?token=' . urlencode($token); ?>" method="POST">
        <label>Nova Senha:</label>
        <input type="password" name="senha" required minlength="8">
        
        <label>Confirme a Senha:</label>
        <input type="password" name="confirma_senha" required minlength="8">

        <button type="submit">Redefinir Senha</button>
    </form>

    <p><a href="<?php echo $base_url; ?>/views/login.php">🔙 Voltar ao Login</a></p>
</body>
</html>
