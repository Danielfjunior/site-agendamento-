// conexao.php
<?php
$host = "localhost";
$user = "usuario";
$password = "senha";
$database = "nome_do_banco";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>