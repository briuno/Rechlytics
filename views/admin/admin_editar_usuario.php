<?php
session_start();
include __DIR__ . '/../../controllers/session_check_admin.php';
include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controllers/log.php';

// Caminho base dinâmico com domínio correto
$base_url = rtrim(
    (isset($_SERVER['HTTPS']) ? "https" : "http") .
    "://" . $_SERVER['HTTP_HOST'] .
    dirname($_SERVER['SCRIPT_NAME'], 3),
    '/'
);

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: $base_url/views/login.php");
    exit();
}

// Verifica se um ID de usuário foi passado na URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de usuário inválido.");
}

$usuario_id = intval($_GET['id']);

// Buscar os dados do usuário
$stmt = $conn->prepare("
    SELECT nome, email, tipo, cpf, empresa, endereco, telefone
    FROM usuarios
    WHERE id = ?
");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Usuário não encontrado.");
}

$usuario = $result->fetch_assoc();
$stmt->close();

// Buscar últimos dashboards desse cliente
$dashboards = [];
$stmtDash = $conn->prepare("
    SELECT id, nome, url
    FROM dashboards
    WHERE usuario_id = ?
    ORDER BY nome
");
$stmtDash->bind_param("i", $usuario_id);
$stmtDash->execute();
$dash_result = $stmtDash->get_result();
while ($row = $dash_result->fetch_assoc()) {
    $dashboards[] = $row;
}
$stmtDash->close();

// Buscar a última vez que o cliente fez login
$ultimo_login = null;
$stmtLogin = $conn->prepare("
    SELECT MAX(data) 
    FROM logs 
    WHERE usuario_id = ? 
      AND acao LIKE '%Login%'
");
$stmtLogin->bind_param("i", $usuario_id);
$stmtLogin->execute();
$stmtLogin->bind_result($ultimo_login);
$stmtLogin->fetch();
$stmtLogin->close();

// Atualizar usuário no banco
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome     = trim($_POST['nome']);
    $email    = trim($_POST['email']);
    $tipo     = trim($_POST['tipo']);
    $empresa  = trim($_POST['empresa']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);

    // Garantir que o tipo de usuário seja válido
    if (!in_array($tipo, ['cliente', 'admin'])) {
        die("Tipo de usuário inválido.");
    }

    // Atualizar os dados do usuário
    $stmtUpd = $conn->prepare("
        UPDATE usuarios 
        SET nome = ?, email = ?, tipo = ?, empresa = ?, endereco = ?, telefone = ?
        WHERE id = ?
    ");
    $stmtUpd->bind_param("ssssssi", $nome, $email, $tipo, $empresa, $endereco, $telefone, $usuario_id);

    if ($stmtUpd->execute()) {
        registrarLog($conn, $_SESSION['usuario_id'], "Editou o usuário ID $usuario_id");
        $_SESSION['msg'] = "Usuário atualizado com sucesso!";
        header("Location: $base_url/views/admin/admin_dashboard.php");
        exit();
    } else {
        $_SESSION['msg'] = "Erro ao atualizar usuário.";
    }
    $stmtUpd->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário – Rechlytics</title>
    <style>
        /* Estilização mínima para apresentação */
        form { margin-bottom: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="email"], textarea {
            width: 400px;
            padding: 6px;
            border: 1px solid #CCC;
            border-radius: 4px;
            font-size: 14px;
        }
        textarea { height: 80px; resize: vertical; }
        button {
            margin-top: 12px;
            padding: 8px 16px;
            background-color: #2C3E50;
            color: #FFF;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover { background-color: #1A252F; }
        .info { margin-top: 20px; padding: 10px; border: 1px solid #DDD; background-color: #F9F9F9; }
        .dash-list { margin-top: 10px; }
        .dash-item { margin-bottom: 8px; }
        .label-small { font-size: 13px; color: #555; }
    </style>
</head>
<body>
    <h2>Editar Usuário</h2>

    <?php if (isset($_SESSION['msg'])): ?>
        <p><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></p>
    <?php endif; ?>

    <form action="<?php echo $base_url; ?>/views/admin/admin_editar_usuario.php?id=<?php echo $usuario_id; ?>" method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Tipo de Usuário:</label>
        <select name="tipo" required>
            <option value="cliente" <?php echo ($usuario['tipo'] === 'cliente') ? 'selected' : ''; ?>>Cliente</option>
            <option value="admin" <?php echo ($usuario['tipo'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>

        <label>CPF:</label>
        <p class="label-small"><?php echo htmlspecialchars($usuario['cpf'], ENT_QUOTES, 'UTF-8'); ?></p>

        <label>Empresa:</label>
        <input type="text" name="empresa" value="<?php echo htmlspecialchars($usuario['empresa'], ENT_QUOTES, 'UTF-8'); ?>">

        <label>Endereço:</label>
        <textarea name="endereco"><?php echo htmlspecialchars($usuario['endereco'], ENT_QUOTES, 'UTF-8'); ?></textarea>

        <label>Telefone:</label>
        <input type="text" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone'], ENT_QUOTES, 'UTF-8'); ?>">

        <button type="submit">Salvar Alterações</button>
    </form>

    <div class="info">
        <strong>Último login:</strong>
        <?php
            if ($ultimo_login) {
                echo date('d/m/Y H:i', strtotime($ultimo_login));
            } else {
                echo "Nenhum login registrado.";
            }
        ?>
    </div>

    <div class="info">
        <strong>Dashboards (BI) deste cliente:</strong>
        <?php if (empty($dashboards)): ?>
            <p class="label-small">Nenhum dashboard associado.</p>
        <?php else: ?>
            <ul class="dash-list">
                <?php foreach ($dashboards as $dash): ?>
                    <li class="dash-item">
                        <strong><?php echo htmlspecialchars($dash['nome'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        <br>
                        <a href="<?php echo htmlspecialchars($dash['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                            Ver Dashboard
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <p><a href="<?php echo $base_url; ?>/views/admin/admin_dashboard.php">Voltar</a></p>
</body>
</html>
