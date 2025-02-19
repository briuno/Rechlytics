<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Buscar mensagens agrupadas por clientes
$clientes = $conn->query("SELECT DISTINCT usuarios.id, usuarios.nome FROM chat_mensagens 
                          JOIN usuarios ON chat_mensagens.usuario_id = usuarios.id");

$mensagens = [];

if (isset($_GET['cliente_id'])) {
    $cliente_id = $_GET['cliente_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mensagem = $_POST['mensagem'];
        $remetente = 'admin';

        $stmt = $conn->prepare("INSERT INTO chat_mensagens (usuario_id, mensagem, remetente) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $cliente_id, $mensagem, $remetente);
        $stmt->execute();
    }

    // Buscar histórico do chat com o cliente selecionado
    $stmt = $conn->prepare("SELECT mensagem, remetente, data_envio FROM chat_mensagens WHERE usuario_id = ? ORDER BY data_envio ASC");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $mensagens = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Chat - Rechlytics</title>
</head>
<body>
    <h2>Gestão de Chat</h2>

    <h3>Selecionar Cliente:</h3>
    <ul>
        <?php while ($cliente = $clientes->fetch_assoc()): ?>
            <li><a href="admin_chat.php?cliente_id=<?php echo $cliente['id']; ?>"><?php echo htmlspecialchars($cliente['nome']); ?></a></li>
        <?php endwhile; ?>
    </ul>

    <?php if (isset($_GET['cliente_id'])): ?>
        <h3>Histórico de Mensagens</h3>

        <div style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll;">
            <?php while ($row = $mensagens->fetch_assoc()): ?>
                <p>
                    <strong><?php echo ($row['remetente'] === 'cliente') ? "Cliente" : "Suporte"; ?>:</strong>
                    <?php echo htmlspecialchars($row['mensagem']); ?>
                    <small>(<?php echo $row['data_envio']; ?>)</small>
                </p>
            <?php endwhile; ?>
        </div>

        <form action="admin_chat.php?cliente_id=<?php echo $cliente_id; ?>" method="POST">
            <label>Mensagem:</label>
            <textarea name="mensagem" required></textarea>
            <button type="submit">Responder</button>
        </form>
    <?php endif; ?>

    <p><a href="admin_dashboard.php">Voltar</a></p>
</body>
</html>
