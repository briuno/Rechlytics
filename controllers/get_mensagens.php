<?php
session_start();
include __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["erro" => "Acesso não autorizado."]);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Se for admin e estiver acessando mensagens de um cliente específico
if ($_SESSION['usuario_tipo'] === 'admin' && isset($_GET['cliente_id'])) {
    $usuario_id = intval($_GET['cliente_id']);
}

// Consulta as mensagens do chat
$stmt = $conn->prepare("SELECT mensagem, remetente, data_envio FROM chat_mensagens WHERE usuario_id = ? ORDER BY data_envio ASC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$mensagens = [];
while ($row = $result->fetch_assoc()) {
    $mensagens[] = [
        "mensagem" => htmlspecialchars($row['mensagem']),
        "remetente" => ($row['remetente'] === 'cliente') ? "Você" : "Suporte",
        "data_envio" => date("d/m/Y H:i", strtotime($row['data_envio']))
    ];
}

// Verifica se há mensagens
if (empty($mensagens)) {
    echo json_encode(["mensagem" => "Nenhuma mensagem encontrada."]);
} else {
    echo json_encode($mensagens);
}
?>
