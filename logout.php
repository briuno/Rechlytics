<?php
session_start();
session_destroy();
include 'includes/log.php';
registrarLog($conn, $_SESSION['usuario_id'], "Logout realizado");
header("Location: login.php");
exit();
?>
