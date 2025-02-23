<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Verifica se é admin ou cliente
if ($_SESSION['usuario_tipo'] === 'admin' && isset($_GET['cliente_id'])) {
    $usuario_id = $_GET['cliente_id'];
}

$stmt = $conn->prepare("SELECT mensagem, remetente, data_envio FROM chat_mensagens WHERE usuario_id = ? ORDER BY data_envio ASC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$mensagens = [];

while ($row = $result->fetch_assoc()) {
    $mensagens[] = [
        "mensagem" => htmlspecialchars($row['mensagem']),
        "remetente" => ($row['remetente'] === 'cliente') ? "Você" : "Suporte",
        "data_envio" => $row['data_envio']
    ];
}

echo json_encode($mensagens);
?>
