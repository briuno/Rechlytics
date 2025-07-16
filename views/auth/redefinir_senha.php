<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

include __DIR__ . '/../../config/db.php';

// Caminho base dinâmico
$base_url = rtrim(
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
    . "://" . $_SERVER['HTTP_HOST']
    . dirname(dirname($_SERVER['SCRIPT_NAME'])),
    '/'
);

// 1) Verifica se houve token na URL
if (!isset($_GET['token']) || empty(trim($_GET['token']))) {
    die("<p>❌ Token inválido ou ausente.</p>");
}
$token = trim($_GET['token']);

// 2) Busca o registro pelo token (sem checar expiração no SQL)
$stmt = $conn->prepare("
    SELECT id, reset_token_expira 
      FROM usuarios 
     WHERE reset_token = ?
    LIMIT 1
");
if (!$stmt) {
    error_log("Prepare SELECT falhou: " . $conn->error);
    die("<p>❌ Erro interno. Tente novamente.</p>");
}
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    die("<p>❌ Token não encontrado no banco.</p>");
}

$usuario = $result->fetch_assoc();
$usuario_id     = $usuario['id'];
$expira_str     = $usuario['reset_token_expira']; // string no formato "YYYY-MM-DD HH:MM:SS"
$stmt->close();

// 3) Verifica em PHP se o token já expirou
$agora_ts = strtotime(date("Y-m-d H:i:s"));
$expira_ts = strtotime($expira_str);

if ($expira_ts === false) {
    error_log("Formato de reset_token_expira inválido para usuário_id = $usuario_id");
    die("<p>❌ Erro interno. Tente novamente mais tarde.</p>");
}

if ($agora_ts > $expira_ts) {
    // O token já expirou
    die("<p>❌ Token expirado. Gere um novo link de redefinição.</p>");
}

// 4) Se chegou aqui, token é válido e não expirou. Processa o POST de nova senha:
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nova_senha     = trim($_POST['senha'] ?? '');
    $confirma_senha = trim($_POST['confirma_senha'] ?? '');

    // Validações
    if ($nova_senha === '' || $confirma_senha === '') {
        $_SESSION['msg'] = "⚠ Por favor, preencha todos os campos!";
    } elseif ($nova_senha !== $confirma_senha) {
        $_SESSION['msg'] = "⚠ As senhas não coincidem!";
    } elseif (mb_strlen($nova_senha) < 8) {
        $_SESSION['msg'] = "⚠ A senha deve ter pelo menos 8 caracteres!";
    } else {
        // Cria hash seguro
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Atualiza a senha e remove o token
        $stmt2 = $conn->prepare("
            UPDATE usuarios 
               SET senha = ?, 
                   reset_token = NULL, 
                   reset_token_expira = NULL 
             WHERE id = ?
        ");
        if (!$stmt2) {
            error_log("Prepare UPDATE senha falhou: " . $conn->error);
            $_SESSION['msg'] = "❌ Erro ao redefinir a senha. Tente novamente.";
        } else {
            $stmt2->bind_param("si", $senha_hash, $usuario_id);
            if (!$stmt2->execute()) {
                error_log("Execute UPDATE senha falhou: " . $stmt2->error);
                $_SESSION['msg'] = "❌ Erro ao redefinir a senha. Tente novamente.";
            } elseif ($stmt2->affected_rows === 0) {
                error_log("Nenhum registro afetado na redefinição de senha para usuário_id = $usuario_id");
                $_SESSION['msg'] = "❌ Erro ao redefinir a senha. Contate o suporte.";
            } else {
                $stmt2->close();
                $_SESSION['msg'] = "✅ Senha redefinida com sucesso! Faça login.";
                header("Location: $base_url/login.php");
                exit();
            }
            $stmt2->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
    <link rel="stylesheet" href="../../public/css/redefinirSenha.css">
    <script src="../../public/js/script.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - Rechlytics</title>
</head>
<body>
    <h2 class="page-title">Redefinir Senha</h2>

    <?php
    if (isset($_SESSION['msg'])) {
        echo "<p>" . htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8') . "</p>";
        unset($_SESSION['msg']);
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?token=' . urlencode($token); ?>" method="POST" class="form-auth">
        <label>Nova Senha:</label>
        <input type="password" name="senha" placeholder="Digite sua nova senha" required minlength="8">

        <label>Confirme a Senha:</label>
        <input type="password" name="confirma_senha" placeholder="Confirme sua nova senha" required minlength="8">

        <button type="submit">Redefinir Senha</button>
    </form>

    <p><a href="<?php echo $base_url; ?>/login.php">Voltar ao Login</a></p>
</body>
</html>
