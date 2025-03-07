<?php
session_start();
include __DIR__ . '/../../config/db.php';

// Caminho base dinâmico
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');

// Verifica se um token foi passado na URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("<p style='color: red;'>❌ Token inválido.</p>");
}

$token = trim($_GET['token']); // Remove espaços extras

// DEBUG: Exibir token recebido pelo PHP para depuração
echo "<p>Token recebido no PHP: " . htmlspecialchars($token) . "</p>";

// Buscar o token no banco de dados
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE BINARY reset_token = ? AND reset_token_expira > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

// Depuração: verificar se encontrou o usuário
if ($result->num_rows === 0) {
    die("<p style='color: red;'>❌ Token não encontrado no banco.</p>");
}

// Obtém o ID do usuário
$usuario = $result->fetch_assoc();
$usuario_id = $usuario['id'];

$stmt->close();

// Processo de redefinição de senha
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nova_senha = trim($_POST['senha']);
    $confirma_senha = trim($_POST['confirma_senha']);

    // Validações
    if (empty($nova_senha) || empty($confirma_senha)) {
        $_SESSION['msg'] = "⚠ Por favor, preencha todos os campos!";
    } elseif ($nova_senha !== $confirma_senha) {
        $_SESSION['msg'] = "⚠ As senhas não coincidem!";
    } elseif (strlen($nova_senha) < 8) {
        $_SESSION['msg'] = "⚠ A senha deve ter pelo menos 8 caracteres!";
    } else {
        // Criar hash seguro da senha
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Atualizar a senha e remover o token
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ?, reset_token = NULL, reset_token_expira = NULL WHERE id = ?");
        $stmt->bind_param("si", $senha_hash, $usuario_id);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "✅ Senha redefinida com sucesso! Faça login.";
            header("Location: $base_url/login.php");
            exit();
        } else {
            $_SESSION['msg'] = "❌ Erro ao redefinir a senha. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - Rechlytics</title>
</head>
<body>
    <h2>🔑 Redefinir Senha</h2>

    <?php
    if (isset($_SESSION['msg'])) {
        echo "<p style='color: red;'>" . htmlspecialchars($_SESSION['msg']) . "</p>";
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

    <p><a href="<?php echo $base_url; ?>/login.php">🔙 Voltar ao Login</a></p>
</body>
</html>
