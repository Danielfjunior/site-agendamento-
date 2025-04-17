<?php
header('Content-Type: application/json');
require __DIR__ . '/includes/config.php';

try {
    if (!isset($_GET['data'])) {
        throw new Exception('Data não fornecida');
    }

    $data = $_GET['data'];
    
    // Validação da data
    if (!DateTime::createFromFormat('Y-m-d', $data)) {
        throw new Exception('Formato de data inválido');
    }

    // Consulta os horários ocupados
    $stmt = $pdo->prepare("SELECT TIME_FORMAT(hora_agendamento, '%H:%i') as hora 
                          FROM agendamentos 
                          WHERE data_agendamento = ?");
    $stmt->execute([$data]);
    $horariosOcupados = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Define horários base conforme dia da semana
    $ehSabado = (date('w', strtotime($data)) == 6);
    $horariosBase = $ehSabado 
        ? ['09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30']
        : ['09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30'];

    // Filtra horários disponíveis
    $horariosDisponiveis = array_values(array_diff($horariosBase, $horariosOcupados));

    echo json_encode($horariosDisponiveis);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}