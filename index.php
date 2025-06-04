<?php
$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Rechlytics - Página Inicial</title>
</head>
<body>
    <h1>Rechlytics</h1>
    <p><a href="<?php echo $base_url; ?>/views/login.php">Login</a> |
       <a href="<?php echo $base_url; ?>/views/cadastro.php">Cadastro</a></p>
</body>
</html>