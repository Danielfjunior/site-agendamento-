<?php
require_once 'conexao.php';

// Consulta SQL
$sql = "SELECT * FROM agendamentos ORDER BY data_agendamento DESC";
$result = $conn->query($sql);

// Array para armazenar os agendamentos
$agendamentos = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $agendamentos[] = $row;
    }
}

// Retorna os dados em formato JSON
header('Content-Type: application/json');
echo json_encode($agendamentos);

$conn->close();
?>