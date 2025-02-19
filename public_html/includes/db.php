<?php
$servername = "193.203.175.215";
$username = "u332555040_rechlytics_use";
$password = "Fo27&ofDS~";
$dbname = "u332555040_rechlytics_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
