<?php
// Rechlytics/controllers/validar_2fa.php

session_start();
include __DIR__ . '/../config/db.php';

// Caminho base dinâmico apontando para a raiz do projeto
$base_url = rtrim(
    (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] .
    dirname($_SERVER['SCRIPT_NAME'], 2),
    '/'
);

if (!isset($_SESSION['usuario_2fa']) || !isset($_POST['codigo_2fa'])) {
    header("Location: $base_url/views/login.php");
    exit();
}

$idUsuario = $_SESSION['usuario_2fa'];
$codigoPostado = trim($_POST['codigo_2fa']);

// Buscar no banco o código e expiração
$stmt = $conn->prepare("
    SELECT two_factor_code, two_factor_expira 
    FROM usuarios 
    WHERE id = ?
");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$stmt->bind_result($codigoArmazenado, $expira2fa);
$stmt->fetch();

// Verifica se expirou
if (new DateTime() > new DateTime($expira2fa)) {
    $_SESSION['erro_2fa'] = "Código expirado. Solicite novo login.";
    unset($_SESSION['usuario_2fa']);
    header("Location: $base_url/views/login.php");
    exit();
}

// Verifica se bate com o do banco
if ($codigoPostado === $codigoArmazenado) {
    // Autenticação 2FA bem-sucedida
    $_SESSION['usuario_id']   = $idUsuario;
    $_SESSION['usuario_tipo'] = 'user'; // ou busque do BD, se precisar
    unset($_SESSION['usuario_2fa']);
    header("Location: $base_url/views/dashboard.php");
    exit();
} else {
    $_SESSION['erro_2fa'] = "Código inválido. Tente novamente.";
    header("Location: $base_url/views/auth/verificar_2fa.php");
    exit();
}
?>
