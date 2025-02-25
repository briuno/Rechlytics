<?php
session_start();
include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controllers/log.php';

// Caminho base para evitar problemas de redirecionamento
$base_url = dirname($_SERVER['SCRIPT_NAME'], 3);

if (!isset($_SESSION['usuario_2fa'])) {
    header("Location: $base_url/views/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_2fa'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_digitado = trim($_POST['codigo']);

    // Verificar o código no banco
    $stmt = $conn->prepare("SELECT two_factor_code, two_factor_expira, two_factor_valid_until, tipo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($codigo_armazenado, $expira, $validade_2fa, $tipo_usuario);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && $codigo_digitado === $codigo_armazenado && strtotime($expira) > time()) {
        // Autenticação concluída
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['usuario_tipo'] = $tipo_usuario;
        unset($_SESSION['usuario_2fa']);

        // Definir duração da autenticação 2FA por 24 horas
        $validade_2fa = date("Y-m-d H:i:s", strtotime("+24 hours"));
        $_SESSION['2fa_valid_until'][$usuario_id] = strtotime($validade_2fa);

        // Atualizar validade do 2FA no banco de dados
        $stmt = $conn->prepare("UPDATE usuarios SET two_factor_code = NULL, two_factor_expira = NULL, two_factor_valid_until = ? WHERE id = ?");
        $stmt->bind_param("si", $validade_2fa, $usuario_id);
        $stmt->execute();

        registrarLog($conn, $usuario_id, "Autenticação 2FA bem-sucedida");

        // Redirecionar conforme o tipo de usuário
        $destino = ($tipo_usuario === 'admin') ? "$base_url/views/admin/admin_dashboard.php" : "$base_url/views/dashboard.php";
        header("Location: $destino");
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

    <form action="" method="POST">
        <label>Digite o código recebido por e-mail:</label>
        <input type="text" name="codigo" required>
        <button type="submit">Confirmar</button>
    </form>

    <p><a href="<?php echo $base_url; ?>/views/login.php">Voltar</a></p>
</body>
</html>
