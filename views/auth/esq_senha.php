<?php
session_start();
include __DIR__ . '/../../config/db.php';

// Caminho base dinâmico com domínio correto
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 2), '/');

if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("<p style='color: red;'>Token inválido.</p>");
}

$token = trim($_GET['token']); // Remove espaços extras

// Depuração: Imprimir token recebido
echo "<p>Token recebido: " . htmlspecialchars($token) . "</p>";

// Verificar se o token existe e ainda é válido
$stmt = $conn->prepare("SELECT id, reset_token, reset_token_expira FROM usuarios WHERE reset_token = ? AND reset_token_expira > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($usuario_id, $reset_token, $reset_token_expira);
$stmt->fetch();

// Depuração: Verificar se o token armazenado é exatamente igual ao recebido
echo "<p>Token no banco: " . htmlspecialchars($reset_token) . "</p>";
echo "<p>Expira em: " . htmlspecialchars($reset_token_expira) . "</p>";

if ($stmt->num_rows === 0 || strcmp($reset_token, $token) !== 0) {
    echo "<p style='color: red;'>Token inválido ou expirado.</p>";
    die();
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nova_senha = trim($_POST['senha']);
    $confirma_senha = trim($_POST['confirma_senha']);

    // Verificar se as senhas coincidem
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
