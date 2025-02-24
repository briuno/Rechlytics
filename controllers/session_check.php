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

// Verifica se a variável de última atividade existe
if (isset($_SESSION['last_activity'])) {
    // Se a diferença entre o tempo atual e a última atividade for maior que o tempo limite
    if ((time() - $_SESSION['last_activity']) > $timeout_duration) {
        // Armazena o ID do usuário antes de destruir a sessão
        $usuario_id = $_SESSION['usuario_id'] ?? null;

        // Limpa e destrói a sessão
        session_unset();
        session_destroy();

        // Registra o logout no log se o usuário ainda estava autenticado
        if ($usuario_id) {
            registrarLog($conn, $usuario_id, "Logout realizado por inatividade");
        }

        // Redireciona para a página de login indicando que a sessão expirou
        header("Location: views/login.php?session_expired=1");
        exit();
    }
}

// Atualiza o timestamp da última atividade para o tempo atual
$_SESSION['last_activity'] = time();
?>
