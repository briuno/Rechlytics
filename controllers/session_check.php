<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define o tempo limite de inatividade (em segundos)
// Exemplo: 1800 segundos = 30 minutos
$timeout_duration = 1800;

// Verifica se a variável de última atividade existe
if (isset($_SESSION['last_activity'])) {
    // Se a diferença entre o tempo atual e a última atividade for maior que o tempo limite
    if ((time() - $_SESSION['last_activity']) > $timeout_duration) {
        // Limpa a sessão e a destrói
        session_unset();
        session_destroy();
        include 'includes/log.php';
        registrarLog($conn, $_SESSION['usuario_id'], "Logout realizado");
        // Redireciona para a página de login, opcionalmente indicando que a sessão expirou
        header("Location: login.php?session_expired=1");
        exit();
    }
}

// Atualiza o timestamp da última atividade para o tempo atual
$_SESSION['last_activity'] = time();
?>
