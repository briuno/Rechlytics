<?php
$servername = "127.0.0.1:3306"; // Servidor do banco de dados (Hostinger forneceu esse IP)
$username = "u332555040_rechlytics_use"; // Seu usuário MySQL
$password = "B1?r~s?"; // SUA SENHA AQUI
$dbname = "u332555040_rechlytics_db"; // Nome do banco de dados

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
echo "Conectado com sucesso!";
?>
