<?php
if (!function_exists('registrarLog')) {
    function registrarLog($conn, $usuario_id, $acao) {
        if (!$conn) {
            error_log("Erro: Conexão com o banco de dados não encontrada ao registrar log.");
            return;
        }

        // Se o ID do usuário for nulo, registrar como "Sistema"
        $usuario_id = !empty($usuario_id) ? $usuario_id : NULL;

        $stmt = $conn->prepare("INSERT INTO logs (usuario_id, acao, data) VALUES (?, ?, CURRENT_TIMESTAMP)");
        if (!$stmt) {
            error_log("Erro ao preparar statement de log: " . $conn->error);
            return;
        }

        $stmt->bind_param("is", $usuario_id, $acao);
        
        if (!$stmt->execute()) {
            error_log("Erro ao registrar log: " . $stmt->error);
        }

        $stmt->close();
    }
}
?>

