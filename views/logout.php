<?php
session_start();
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../controllers/log.php';

// Caminho base para evitar problemas no redirecionamento
$base_url = dirname($_SERVER['SCRIPT_NAME'], 2);

// Registrar log de logout
if (isset($_SESSION['usuario_id'])) {
    registrarLog($conn, $_SESSION['usuario_id'], "Logout realizado");
}

// Encerrar a sessão
session_unset();
session_destroy();

// Redirecionar para a página inicial
header("Location: $base_url/index.php");
exit();
?>
