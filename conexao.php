<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "cibelly_agendamentos";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>