<?php
// Arquivo: salvar_agendamento_debug.php

// Permite CORS para desenvolvimento
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Configurações de erro
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log de requisição
file_put_contents('debug.log', date('Y-m-d H:i:s') . " - Requisição recebida\n", FILE_APPEND);
file_put_contents('debug.log', "Método: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
file_put_contents('debug.log', "Headers: " . print_r(getallheaders(), true) . "\n", FILE_APPEND);
file_put_contents('debug.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents('debug.log', "Input: " . file_get_contents('php://input') . "\n\n", FILE_APPEND);

try {
    // Simula um banco de dados (substitua pelo seu real)
    $host = 'localhost';
    $db   = 'agendamentos';
    $user = 'root';
    $pass = 'root';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Processa os dados
    $input = file_get_contents('php://input');
    $data = json_decode($input, true) ?: $_POST;

    if (json_last_error() !== JSON_ERROR_NONE && empty($_POST)) {
        throw new Exception("Dados inválidos recebidos");
    }

    // Validação básica
    $required = ['nome', 'whatsapp', 'servico', 'data', 'hora'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Campo obrigatório faltando: $field");
        }
    }

    // Simula inserção no banco de dados
    $stmt = $pdo->prepare("INSERT INTO agendamentos 
                          (nome_cliente, telefone, servico, data_agendamento, hora_agendamento, tipo, status) 
                          VALUES 
                          (:nome, :whatsapp, :servico, :data, :hora, 'unha', 'confirmado')");
    
    $stmt->execute([
        ':nome' => $data['nome'],
        ':whatsapp' => preg_replace('/[^0-9]/', '', $data['whatsapp']),
        ':servico' => $data['servico'],
        ':data' => $data['data'],
        ':hora' => $data['hora']
    ]);

    $response = [
        'success' => true,
        'message' => 'Agendamento salvo com sucesso!',
        'id' => $pdo->lastInsertId()
    ];

} catch (PDOException $e) {
    $response = [
        'success' => false,
        'message' => 'Erro no banco de dados: ' . $e->getMessage(),
        'error_code' => $e->getCode()
    ];
    file_put_contents('debug.log', "PDO Error: " . $e->getMessage() . "\n", FILE_APPEND);
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    file_put_contents('debug.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
}

header('Content-Type: application/json');
echo json_encode($response);
file_put_contents('debug.log', "Resposta: " . json_encode($response) . "\n\n", FILE_APPEND);