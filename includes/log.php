<?php
function registrarLog($conn, $usuario_id, $acao) {
    $stmt = $conn->prepare("INSERT INTO logs (usuario_id, acao, data) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $usuario_id, $acao);
    $stmt->execute();
}
?>


