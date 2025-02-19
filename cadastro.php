<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'cliente')");
    $stmt->bind_param("sss", $nome, $email, $senha);

    if ($stmt->execute()) {
        header("Location: login.php");
    } else {
        echo "Erro ao cadastrar.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Rechlytics</title>
</head>
<body>
    <h2>Cadastro</h2>
    <form action="cadastro.php" method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Senha:</label>
        <input type="password" name="senha" required>
        <button type="submit">Cadastrar</button>
    </form>
    <p><a href="login.php">Já tem uma conta? Faça login</a></p>
</body>
</html>
