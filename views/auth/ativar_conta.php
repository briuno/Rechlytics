<?php
include __DIR__ . '/../../config/db.php';

// Caminho base dinâmico com domínio correto
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 3), '/');

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
            echo "<p>Conta ativada com sucesso! Agora você pode fazer <a href='$base_url/views/login.php'>login</a>.</p>";
        } else {
            echo "<p>Erro ao ativar a conta. Tente novamente mais tarde.</p>";
        }
    } else {
        echo "<p>Link de ativação inválido ou expirado.</p>";
    }
} else {
    echo "<p>Link de ativação inválido.</p>";
}
?>


