<?php
// Configurações do banco de dados
$servername = "45.151.120.2";
$username = "u332555040_bancodedados";
$password = "K2|mU~!W;";
$dbname = "u332555040_bancodedados";
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
