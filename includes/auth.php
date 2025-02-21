<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nome, $hash_senha, $tipo);
        $stmt->fetch();

        if (password_verify($senha, $hash_senha)) {
            $_SESSION['usuario_id'] = $id;
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_tipo'] = $tipo;

            include 'log.php';
            registrarLog($conn, $id, "Login realizado");

              if ($tipo == 'admin') {
                header("Location: ../admin_dashboard.php");
            } else {
                header("Location: ../dashboard.php");
            }
            exit();
        }
    }
    
    header("Location: ../index.php?erro=1");
    exit();
}
?>
