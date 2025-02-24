<?php
include __DIR__ . '/../../config/db.php';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = htmlspecialchars($_GET['email'], ENT_QUOTES, 'UTF-8');
    $token = htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8');

    // Verificar se o e-mail e o token existem no banco
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND reset_token = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Atualizar o status da conta para verificado e remover o token
        $stmt = $conn->prepare("UPDATE usuarios SET email_verificado = 1, reset_token = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Conta ativada com sucesso! Agora você pode fazer <a href='/rechlytics/views/login.php'>login</a>.</p>";
        } else {
            echo "<p style='color: red;'>Erro ao ativar a conta. Tente novamente mais tarde.</p>";
        }
    } else {
        echo "<p style='color: red;'>Link de ativação inválido ou expirado.</p>";
    }
} else {
    echo "<p style='color: red;'>Link inválido.</p>";
}
?>

