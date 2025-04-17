<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendamento</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Agendar Serviço</h1>
        
        <?php if(isset($_GET['sucesso'])): ?>
            <div class="alert success">
                Agendamento realizado com sucesso!
            </div>
        <?php endif; ?>
        
        <form action="processa_agendamento.php" method="post">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="tel" id="telefone" name="telefone" required>
            </div>
            
            <div class="form-group">
                <label for="data">Data:</label>
                <input type="date" id="data" name="data" required>
            </div>
            
            <div class="form-group">
                <label for="hora">Hora:</label>
                <input type="time" id="hora" name="hora" required>
            </div>
            
            <div class="form-group">
                <label for="servico">Serviço:</label>
                <select id="servico" name="servico" required>
                    <option value="">Selecione...</option>
                    <option value="Corte de Cabelo">Corte de Cabelo</option>
                    <option value="Barba">Barba</option>
                    <option value="Corte + Barba">Corte + Barba</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="observacoes">Observações:</label>
                <textarea id="observacoes" name="observacoes"></textarea>
            </div>
            
            <button type="submit">Agendar</button>
        </form>
    </div>
</body>
</html>