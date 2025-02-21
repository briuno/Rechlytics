<?php
session_start();
include 'includes/db.php'; // Garante que a conexão com o banco está disponível
include 'includes/log.php'; // Garante que o sistema de logs está disponível

// Registrar log de logout
if (isset($_SESSION['usuario_id'])) {
    registrarLog($conn, $_SESSION['usuario_id'], "Logout realizado");
}

// Encerrar a sessão
session_unset();
session_destroy();

header("Location: index.php");
exit();
?>
