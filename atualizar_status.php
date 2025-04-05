<?php
require 'conexao.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);
$status = $conn->real_escape_string($input['status'] ?? '');

if ($id <= 0 || !in_array($status, ['confirmado', 'cancelado', 'pendente'])) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

$sql = "UPDATE agendamentos SET status = '$status' WHERE id = $id";

if ($conn->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>