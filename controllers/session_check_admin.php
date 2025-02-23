<?php
// Inicia a sessão, se necessário
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado e se é administrador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    // Se não estiver logado ou não for admin, redireciona para a página de login com uma mensagem de acesso negado
    header("Location: login.php?acesso=negado");
    exit();
}

// Atualiza o timestamp da última atividade (caso já esteja utilizando session_check para expiração)
$_SESSION['last_activity'] = time();
?>
