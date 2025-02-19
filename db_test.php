<?php
// Ativar a exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Credenciais corrigidas
$servername = "193.203.175.215"; // Servidor correto do banco de dados
$username = "u332555040_rechlytics_use"; // Usuário correto
$password = "B1?r~s?"; // Senha correta
$dbname = "u332555040_rechlytics_db"; // Nome correto do banco de dados

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
echo "Conectado com sucesso!";
?>
