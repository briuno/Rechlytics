<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define o tempo limite de inatividade (em segundos)
$timeout_duration = 1800;

// Inclui a conexão com o banco de dados e o sistema de logs
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../controllers/log.php';

// Caminho base para evitar problemas no redirecionamento
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 2), '/');
$login_url = $base_url . "/views/login.php";


// Verifica se o usuário está logado e se é administrador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: $login_url?acesso=negado");
    exit();
}

// Verifica a inatividade e faz logout automático se necessário
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    $usuario_id = $_SESSION['usuario_id'] ?? null;

    // Limpa e destrói a sessão
    session_unset();
    session_destroy();

    // Registra o logout por inatividade no log
    if ($usuario_id) {
        registrarLog($conn, $usuario_id, "Logout automático por inatividade - Admin");
    }

    header("Location: $login_url?session_expired=1");
    exit();
}

// Atualiza o timestamp da última atividade para controle de sessão
$_SESSION['last_activity'] = time();
?>


