<?php
// Configurações do banco de dados
$servername = "193.203.175.215";
$username = "u332555040_rechlytics_use";
$password = "Fo27&ofDS~";
$dbname = "u332555040_rechlytics_db";
date_default_timezone_set('America/Sao_Paulo');


// Ativar relatório de erros para MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4"); // Garante suporte a caracteres especiais
} catch (Exception $e) {
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Tente novamente mais tarde.");
}
?>
