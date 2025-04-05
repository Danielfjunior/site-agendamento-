<?php

$host = "localhost";
$user = "root";
$pass = "root";
$dbname = "agendamento";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
$conexao = new mysqli("localhost", "root", "root", "agendamento");
?>