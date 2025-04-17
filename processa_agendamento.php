<?php
include 'includes/config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coletar dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $servico = $_POST['servico'];
    $observacoes = $_POST['observacoes'] ?? '';
    
    try {
        // Inserir no banco de dados
        $sql = "INSERT INTO agendamentos (nome_cliente, email, telefone, data_agendamento, hora_agendamento, servico, observacoes) 
                VALUES (:nome, :email, :telefone, :data, :hora, :servico, :observacoes)";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':servico', $servico);
        $stmt->bindParam(':observacoes', $observacoes);
        
        $stmt->execute();
        
        // Redirecionar com mensagem de sucesso
        header('Location: agendar.php?sucesso=1');
        exit();
        
    } catch(PDOException $e) {
        die("ERRO: Não foi possível executar a inserção. " . $e->getMessage());
    }
} else {
    header('Location: agendar.php');
    exit();
}
?>