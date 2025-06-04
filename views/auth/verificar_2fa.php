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
    <title>Verificação 2FA – Rechlytics</title>
    <style>
        /* Estilo minimal para manter a consistência */
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 400px; margin: auto; background: #fff; padding: 20px; border-radius: 6px;
                     box-shadow: 0 2px 4px rgba(0,0,0,.1); }
        h2 { margin-bottom: 16px; }
        .erro { color: #c00; margin-bottom: 12px; }
        input, button { width: 100%; padding: 8px; margin: 6px 0 12px; border-radius: 4px; }
        input { border: 1px solid #ccc; }
        button { background: #28a745; color: #fff; border: none; cursor: pointer; }
        button:hover { background: #1e7e34; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Digite seu Código 2FA</h2>

        <?php if ($mensagem_erro): ?>
            <div class="erro"><?php echo htmlspecialchars($mensagem_erro); ?></div>
        <?php endif; ?>

        <!-- Aqui está a correção: subimos dois níveis para chegar em controllers/validar_2fa.php -->
        <form action="../../controllers/validar_2fa.php" method="POST">
            <label for="codigo_2fa">Código 2FA:</label>
            <input type="text" id="codigo_2fa" name="codigo_2fa" required pattern="\d{6}"
                   title="Insira o código de 6 dígitos enviado por e-mail">

            <button type="submit" name="verificar_2fa">Verificar</button>
        </form>
    </div>
</body>
</html>
