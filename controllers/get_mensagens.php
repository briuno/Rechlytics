<?php
session_start();
include __DIR__ . '/config/db.php';

header('Content-Type: application/json'); // Define o cabeçalho da resposta como JSON

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["erro" => "Acesso não autorizado."]);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Se for admin e estiver acessando mensagens de um cliente específico
if ($_SESSION['usuario_tipo'] === 'admin' && isset($_GET['cliente_id'])) {
    $usuario_id = intval($_GET['cliente_id']); // Garante que seja um número
}

// Consulta as mensagens do chat
$stmt = $conn->prepare("SELECT mensagem, remetente, data_envio FROM chat_mensagens WHERE usuario_id = ? ORDER BY data_envio ASC");
$stmt->bind_param("i", $usuario_id);

if (!$stmt->execute()) {
    echo json_encode(["erro" => "Erro ao buscar mensagens do chat."]);
    exit();
}

$result = $stmt->get_result();
$mensagens = [];

while ($row = $result->fetch_assoc()) {
    $mensagens[] = [
        "mensagem" => htmlspecialchars($row['mensagem']),
        "remetente" => ($row['remetente'] === 'cliente') ? "Você" : "Suporte",
        "data_envio" => date("d/m/Y H:i", strtotime($row['data_envio']))
    ];
}

echo json_encode($mensagens);
?>
