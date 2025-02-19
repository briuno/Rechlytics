<?php
// Ativar a exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuração do banco de dados
$servername = "127.0.0.1:3306"; // Verifique se Hostinger indicou um servidor diferente
$username = "u332555040_rechlytics_use"; // Seu usuário MySQL
$password = "B1?r~s?"; // Sua senha real
$dbname = "u332555040_rechlytics_db"; // Nome do banco de dados

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
echo "Conectado com sucesso!!";
?>

