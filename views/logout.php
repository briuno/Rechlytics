<?php
session_start();
include __DIR__ . '/../config/db.php'; 
include __DIR__ . '/../controllers/log.php'; 

// Registrar log de logout
if (isset($_SESSION['usuario_id'])) {
    registrarLog($conn, $_SESSION['usuario_id'], "Logout realizado");
}

// Encerrar a sessão
session_unset();
session_destroy();

// Redirecionar para a página inicial
header("Location: /rechlytics/index.php");
exit();
?>
