<?php
session_start();
include __DIR__ . '/config/db.php';
include __DIR__ . '/config/log.php';

if (!isset($_SESSION['usuario_2fa'])) {
    header("Location: /auth/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_2fa'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_digitado = trim($_POST['codigo']);

    // Verificar o código no banco
    $stmt = $conn->prepare("SELECT two_factor_code, two_factor_expira FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($codigo_armazenado, $expira);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && $codigo_digitado === $codigo_armazenado && strtotime($expira) > time()) {
        // Autenticação concluída
        $_SESSION['usuario_id'] = $usuario_id;
        unset($_SESSION['usuario_2fa']);

        // Limpar código do banco
        $stmt = $conn->prepare("UPDATE usuarios SET two_factor_code = NULL, two_factor_expira = NULL WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();

        registrarLog($conn, $usuario_id, "Autenticação 2FA bem-sucedida");

        // Redirecionar ao painel
        header("Location: /client/dashboard.php");
        exit();
    } else {
        $_SESSION['erro_2fa'] = "Código inválido ou expirado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Verificação 2FA - Rechlytics</title>
</head>
<body>
    <h2>Verificação 2FA</h2>

    <?php if (isset($_SESSION['erro_2fa'])): ?>
        <p style="color: red;"><?php echo $_SESSION['erro_2fa']; unset($_SESSION['erro_2fa']); ?></p>
    <?php endif; ?>

    <form action="/auth/verificar_2fa.php" method="POST">
        <label>Digite o código recebido por e-mail:</label>
        <input type="text" name="codigo" required>
        <button type="submit">Confirmar</button>
    </form>

    <p><a href="/auth/login.php">Voltar</a></p>
</body>
</html>
