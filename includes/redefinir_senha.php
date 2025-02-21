<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $token = bin2hex(random_bytes(50));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $stmt = $conn->prepare("UPDATE usuarios SET token_recuperacao=?, token_expira=? WHERE email=?");
        $stmt->bind_param("sss", $token, $expira, $email);
        $stmt->execute();

        include 'includes/log.php';
        registrarLog($conn, $usuario_id, "Redefiniu a senha");

        $link = "https://rechlytics.com/nova_senha.php?token=$token";
        mail($email, "Recuperação de Senha", "Clique no link para redefinir sua senha: $link");
    }

    header("Location: ../esq_senha.php?sucesso=1");
    exit();
}
?>
