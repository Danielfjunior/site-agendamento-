<?php
require_once 'conexao.php';

header('Content-Type: application/json');

// Verifica campos obrigatórios
if (empty($_POST['nome']) || empty($_POST['whatsapp']) || empty($_POST['servico']) || empty($_POST['data']) || empty($_POST['hora'])) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios']);
    exit;
}

// Combina data e hora corretamente
$data = $_POST['data'];
$hora = $_POST['hora'];
$data_agendamento = date('Y-m-d H:i:s', strtotime("$data $hora"));

// Prepara os dados
$nome = $conn->real_escape_string($_POST['nome']);
$whatsapp = $conn->real_escape_string($_POST['whatsapp']);
$servico = $conn->real_escape_string($_POST['servico']);
$observacoes = $conn->real_escape_string($_POST['observacoes'] ?? '');

// Prepara a query SQL
$sql = "INSERT INTO agendamentos (nome, whatsapp, servico, data_agendamento, observacoes) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nome, $whatsapp, $servico, $data_agendamento, $observacoes);

if ($stmt->execute()) {
    $response = [
        'success' => true,
        'message' => 'Agendamento realizado com sucesso!',
        'id' => $conn->insert_id
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Erro ao agendar: ' . $conn->error
    ];
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>