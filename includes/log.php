<?php
function registrarLog($conn, $usuario_id, $acao) {
    // Obter o IP do usuário (pode ser melhorado para casos de proxies)
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido';
    
    // Preparar a query para inserir o log
    $stmt = $conn->prepare("INSERT INTO logs (usuario_id, acao, ip) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $acao, $ip);
    $stmt->execute();
}
?>
