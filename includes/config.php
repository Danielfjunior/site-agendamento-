<?php
// Configurações do Banco de Dados
define('DB_HOST', 'localhost');     // Servidor MySQL (geralmente localhost)
define('DB_USER', 'root');          // Seu usuário do MySQL
define('DB_PASS', 'root');          // Sua senha do MySQL (vazia se não tiver)
define('DB_NAME', 'agendamentos');  // Nome do banco que você criou

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME,
        DB_USER, 
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    // Em produção, não mostre detalhes do erro diretamente
    error_log("Erro de conexão: " . $e->getMessage());
    die(json_encode([
        'success' => false,
        'message' => 'Erro na conexão com o banco de dados'
    ]));
}

// Sessão deve ser iniciada apenas uma vez (remova se já estiver no index.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}