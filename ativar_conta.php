<?php
include 'includes/db.php';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];

    // Atualiza o status da conta para verificado
    $stmt = $conn->prepare("UPDATE usuarios SET email_verificado = 1 WHERE email = ?");
    $stmt->bind_param("s", $email);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Conta ativada com sucesso! Agora você pode fazer login.</p>";
    } else {
        echo "<p style='color: red;'>Erro ao ativar a conta.</p>";
    }
} else {
    echo "<p style='color: red;'>Link inválido.</p>";
}
?>
