<?php
function registrarLog($conn, $usuario_id, $acao) {
    if (!$conn) {
        die("Erro na conexão com o banco de dados ao tentar registrar log.");
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido';

    $stmt = $conn->prepare("INSERT INTO logs (usuario_id, acao, ip) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $acao, $ip);
    $stmt->execute();
}
?>

