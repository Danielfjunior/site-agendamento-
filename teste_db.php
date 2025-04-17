<?php
require __DIR__ . '/includes/config.php';
$stmt = $pdo->query("SELECT usuario FROM administradores");
print_r($stmt->fetchAll());