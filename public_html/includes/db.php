<?php
// Ativar a exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuração do banco de dados
$servername = "193.203.175.215";
$username = "u332555040_rechlytics_use";
$password = "Fo27&ofDS~";
$dbname = "u332555040_rechlytics_db";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
