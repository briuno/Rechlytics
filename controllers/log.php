<?php
function registrarLog($conn, $usuario_id, $acao) {
    // Se o ID do usuário for nulo, registrar como "Sistema"
    $usuario_id = !empty($usuario_id) ? $usuario_id : NULL;

    $stmt = $conn->prepare("INSERT INTO logs (usuario_id, acao, data) VALUES (?, ?, CURRENT_TIMESTAMP)");
    $stmt->bind_param("is", $usuario_id, $acao);
    
    if (!$stmt->execute()) {
        error_log("Erro ao registrar log: " . $stmt->error);
    }
}
?>

