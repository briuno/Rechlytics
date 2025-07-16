<?php
// Rechlytics/views/auth/verificar_2fa.php
session_start();
if (!isset($_SESSION['usuario_2fa'])) {
    header("Location: ../login.php");
    exit();
}
$mensagem_erro = $_SESSION['erro_2fa'] ?? '';
unset($_SESSION['erro_2fa']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
    <link rel="stylesheet" href="../../public/css/doisFatores.css">
    <script src="../../public/js/script.js" defer></script>
    <title>Verificação 2FA – Rechlytics</title>
</head>
<body>
    <div class="container">
        <h2 class="page-title">Digite seu Código 2FA</h2>

        <?php if ($mensagem_erro): ?>
            <div class="erro"><?php echo htmlspecialchars($mensagem_erro); ?></div>
        <?php endif; ?>

        <!-- Aqui está a correção: subimos dois níveis para chegar em controllers/validar_2fa.php -->
        <form action="../../controllers/validar_2fa.php" method="POST" class="form-auth">
            <label for="codigo_2fa">Código 2FA:</label>
            <input type="text" id="codigo_2fa" name="codigo_2fa" placeholder="******" required pattern="\d{6}"
                   title="Insira o código de 6 dígitos enviado por e-mail">

            <button type="submit" name="verificar_2fa">Verificar</button>
        </form>
    </div>
</body>
</html>
