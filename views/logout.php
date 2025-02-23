<?php
session_start();
include __DIR__ . '/config/db.php'; // Garante a conexão com o banco
include __DIR__ . '/config/log.php'; // Garante o sistema de logs

// Registrar log de logout
if (isset($_SESSION['usuario_id'])) {
    registrarLog($conn, $_SESSION['usuario_id'], "Logout realizado");
}

// Encerrar a sessão
session_unset();
session_destroy();

// Redirecionar para a página inicial
header("Location: /index.php");
exit();
?>
