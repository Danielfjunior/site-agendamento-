<?php
require 'conexao.php';

$sql = "INSERT INTO agendamentos (nome, whatsapp, servico, data_agendamento) 
        VALUES ('Teste', '11999999999', 'Volume Russo', NOW())";

if ($conn->query($sql) === TRUE) {
    echo "Dados inseridos com sucesso!";
} else {
    echo "Erro: " . $conn->error;
}

$conn->close();
?>