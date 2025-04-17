<?php
require __DIR__ . '/includes/config.php';

try {
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Conexão com o banco de dados funcionando perfeitamente!";
} catch (PDOException $e) {
    echo "❌ Erro na conexão: " . $e->getMessage();
}