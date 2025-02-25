<?php
if (!function_exists('registrarLog')) {
    function registrarLog($conn, $usuario_id, $acao) {
        if (!$conn) {
            error_log("Erro: Conexão com o banco de dados não encontrada ao registrar log.");
            return;
        }

        date_default_timezone_set('America/Sao_Paulo'); // Garante o fuso horário correto
        $data_atual = date("Y-m-d H:i:s"); // Captura a data correta

        $usuario_id = !empty($usuario_id) ? $usuario_id : NULL;

        $stmt = $conn->prepare("INSERT INTO logs (usuario_id, acao, data) VALUES (?, ?, ?)");
        if (!$stmt) {
            error_log("Erro ao preparar statement de log: " . $conn->error);
            return;
        }

        $stmt->bind_param("iss", $usuario_id, $acao, $data_atual);
        
        if (!$stmt->execute()) {
            error_log("Erro ao registrar log: " . $stmt->error);
        }

        $stmt->close();
    }
}
?>

