<?php
session_start();
require('conexao.php');
// Verifica login (simplificado - você deve melhorar isso!)
if (!isset($_SESSION['logado'])) {
    header('Location: login.php');
    exit;
}

// Busca agendamentos
$sql = "SELECT * FROM agendamentos ORDER BY data_agendamento DESC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Cibelly Lash</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #ff69b4;
            --secondary: #ff8ac5;
            --dark: #333;
            --light: #fff;
            --gray: #f5f5f5;
            --danger: #ff4757;
            --success: #2ed573;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Quicksand', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: var(--dark);
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-header h2 {
            font-size: 1.3rem;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            background: rgba(255,255,255,0.1);
            border-left: 3px solid white;
        }
        
        .sidebar-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            flex: 1;
            padding: 30px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .header h1 {
            color: var(--primary);
        }
        
        .logout-btn {
            background: var(--danger);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--primary);
        }
        
        .stat-card h3 {
            color: #777;
            font-size: 1rem;
            margin-bottom: 10px;
        }
        
        .stat-card p {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--dark);
        }
        
        .appointments-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 20px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f9f9f9;
            color: #555;
            font-weight: 600;
        }
        
        tr:hover {
            background: #f5f5f5;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3e0;
            color: #ff6d00;
        }
        
        .status-confirmed {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-canceled {
            background: #ffebee;
            color: #c62828;
        }
        
        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 0.8rem;
        }
        
        .confirm-btn {
            background: var(--success);
            color: white;
        }
        
        .cancel-btn {
            background: var(--danger);
            color: white;
        }
        
        .edit-btn {
            background: #3498db;
            color: white;
        }
        
        .search-filter {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 15px;
        }
        
        .search-box {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .date-filter {
            display: flex;
            gap: 10px;
        }
        
        .date-filter input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
            
            .search-filter {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Cibelly Lash</h2>
                <p>Painel Administrativo</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="#" class="active"><i class="fas fa-calendar-alt"></i> Agendamentos</a></li>
                <li><a href="#"><i class="fas fa-chart-line"></i> Relatórios</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Configurações</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Clientes</a></li>
                <li><a href="#"><i class="fas fa-list"></i> Serviços</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>Agendamentos</h1>
                <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Sair</button>
            </div>
            
            <div class="stats-cards">
                <div class="stat-card">
                    <h3>Agendamentos Hoje</h3>
                    <p>8</p>
                </div>
                <div class="stat-card">
                    <h3>Agendamentos Semana</h3>
                    <p>32</p>
                </div>
                <div class="stat-card">
                    <h3>Confirmados</h3>
                    <p>28</p>
                </div>
                <div class="stat-card">
                    <h3>Cancelados</h3>
                    <p>4</p>
                </div>
            </div>
            
            <div class="search-filter">
                <input type="text" class="search-box" placeholder="Pesquisar por nome, telefone ou serviço...">
                <div class="date-filter">
                    <input type="date" id="startDate">
                    <input type="date" id="endDate">
                    <button class="action-btn confirm-btn">Filtrar</button>
                </div>
            </div>
            
            <div class="appointments-table">
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Data</th>
                            <th>WhatsApp</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php while($agendamento = $resultado->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($agendamento['nome']) ?></td>
        <td><?= htmlspecialchars($agendamento['servico']) ?></td>
        <td><?= date('d/m/Y H:i', strtotime($agendamento['data_agendamento'])) ?></td>
        <td><?= htmlspecialchars($agendamento['whatsapp']) ?></td>
        <td>
            <span class="status status-<?= $agendamento['status'] ?>">
                <?= ucfirst($agendamento['status']) ?>
            </span>
        </td>
        <td>
        <button class="action-btn confirm-btn" 
        onclick="atualizarStatus(<?= $agendamento['id'] ?>, 'confirmado')">
    <i class="fas fa-check"></i>
</button>

<button class="action-btn cancel-btn" 
        onclick="atualizarStatus(<?= $agendamento['id'] ?>, 'cancelado')">
    <i class="fas fa-times"></i>
</button>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Aqui você pode adicionar a lógica para:
        // 1. Carregar os agendamentos do banco de dados
        // 2. Filtrar os agendamentos
        // 3. Confirmar/cancelar agendamentos
        
        // Exemplo de como carregar dados (substitua por chamada AJAX real)
        function loadAppointments() {
            /*
            fetch('get_appointments.php')
                .then(response => response.json())
                .then(data => {
                    // Atualizar a tabela com os dados recebidos
                    console.log(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            */
        }
        
        // Carregar agendamentos quando a página é aberta
        document.addEventListener('DOMContentLoaded', loadAppointments);
    </script>
    <script>
        // Função para carregar agendamentos
        function carregarAgendamentos() {
            fetch('listar_agendamentos.php')
                .then(response => response.json())
                .then(data => {
                    const tabela = document.querySelector('tbody');
                    tabela.innerHTML = '';
                    
                    data.forEach(agendamento => {
                        const tr = document.createElement('tr');
                        
                        // Formata a data
                        const dataFormatada = new Date(agendamento.data_agendamento).toLocaleString('pt-BR');
                        
                        // Determina a classe de status
                        let statusClass = 'status-pending';
                        if (agendamento.status === 'confirmado') statusClass = 'status-confirmed';
                        if (agendamento.status === 'cancelado') statusClass = 'status-canceled';
                        
                        tr.innerHTML = `
                            <td>${agendamento.nome}</td>
                            <td>${agendamento.servico}</td>
                            <td>${dataFormatada}</td>
                            <td>${agendamento.whatsapp}</td>
                            <td><span class="status ${statusClass}">${agendamento.status}</span></td>
                            <td>
                                <button class="action-btn confirm-btn" onclick="atualizarStatus(${agendamento.id}, 'confirmado')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="action-btn cancel-btn" onclick="atualizarStatus(${agendamento.id}, 'cancelado')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        `;
                        
                        tabela.appendChild(tr);
                    });
                });
        }
        
        // Função para atualizar status
        function atualizarStatus(id, status) {
            fetch('atualizar_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id, status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    carregarAgendamentos();
                    enviarNotificacao(id, status);
                } else {
                    alert('Erro ao atualizar status');
                }
            });
        }
        
        // Carrega os agendamentos quando a página abre
        document.addEventListener('DOMContentLoaded', carregarAgendamentos);
        </script>
        <script>
function atualizarStatus(id, status) {
    fetch('../atualizar_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id, status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recarrega a página
        } else {
            alert('Erro ao atualizar!');
        }
    });
}
</script>
<script>
function atualizarStatus(id, status) {
    fetch('atualizar_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id, status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recarrega a página
        } else {
            alert('Erro ao atualizar: ' + (data.error || ''));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Falha na comunicação com o servidor');
    });
}
</script>
</body>
</html>