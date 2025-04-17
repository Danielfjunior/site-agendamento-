<?php
include '../includes/config.php';

// Destruir a sessão
$_SESSION = array();
session_destroy();

// Redirecionar para login
header('Location: login.php');
exit();
?>