<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechlytics - Página Inicial</title>
</head>
<body>
    <h2>Bem-vindo ao Rechlytics</h2>
    <p>Sua plataforma para análise e visualização de dados.</p>

    <?php
    $base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
    ?>

    <a href="<?php echo $base_url; ?>/views/login.php"><button>Entrar</button></a>
    <a href="<?php echo $base_url; ?>/views/cadastro.php"><button>Cadastrar</button></a>
</body>
</html>

