<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica login
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/../includes/config.php';

// Definir seção ativa
$secao = isset($_GET['secao']) ? $_GET['secao'] : 'agendamentos';

// Processar agendamentos
$statusValidos = ['pendente', 'confirmado', 'cancelado'];

if ($secao === 'agendamentos') {
    // Query para agendamentos
    $agendamentos = $pdo->query("
        SELECT *,
        CASE 
            WHEN status IS NULL THEN 'pendente'
            WHEN status = 'confirmar' THEN 'confirmado'
            WHEN status = 'cancelar' THEN 'cancelado'
            ELSE status
        END AS status
        FROM agendamentos 
        ORDER BY data_agendamento DESC, hora_agendamento DESC
    ")->fetchAll();

    // Atualiza status
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $acao = filter_input(INPUT_POST, 'acao', FILTER_SANITIZE_STRING);
        
        $mapeamentoAcoes = [
            'confirmar' => 'confirmado',
            'cancelar' => 'cancelado',
            'pendente' => 'pendente'
        ];
        
        if ($id && array_key_exists($acao, $mapeamentoAcoes)) {
            $novoStatus = $mapeamentoAcoes[$acao];
            $stmt = $pdo->prepare("UPDATE agendamentos SET status = ? WHERE id = ?");
            $stmt->execute([$novoStatus, $id]);
            header("Location: index.php?secao=agendamentos");
            exit;
        }
    }

    // Contagens
    $totalAgendamentos = count($agendamentos);
    $confirmados = count(array_filter($agendamentos, fn($a) => $a['status'] === 'confirmado'));
    $pendentes = count(array_filter($agendamentos, fn($a) => $a['status'] === 'pendente'));
    $cancelados = count(array_filter($agendamentos, fn($a) => $a['status'] === 'cancelado'));
}

// Processar clientes
if ($secao === 'clientes') {
    $clientes = $pdo->query("
        SELECT 
            nome_cliente,
            telefone,
            COUNT(*) as total_agendamentos,
            MAX(data_agendamento) as ultima_visita
        FROM agendamentos
        GROUP BY nome_cliente, telefone
        ORDER BY ultima_visita DESC
    ")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin | Cibelly Lash</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #8a7fff;
            --primary-dark: #6a5acd;
            --secondary: #ff7eb9;
            --success: #4caf50;
            --danger: #f44336;
            --warning: #ff9800;
            --light: #f8f9fa;
            --dark: #2e2e3a;
            --white: #ffffff;
            --gray: #e0e0e0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--dark);
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            background: var(--white);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            padding: 2rem 0;
            position: relative;
            z-index: 100;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }
        
        .logo img {
            max-width: 80%;
        }
        
        .nav-menu {
            list-style: none;
        }
        
        .nav-item {
            margin: 0.5rem 0;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(138, 127, 255, 0.1);
            color: var(--primary-dark);
            border-left: 3px solid var(--primary);
        }
        
        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-content {
            padding: 2rem;
            overflow-x: auto;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 0.75rem;
        }
        
        .btn-logout {
            background: var(--danger);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-logout:hover {
            background: #d32f2f;
        }
        
        /* Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card {
            background: var(--white);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .card-title {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .card-icon.confirmados {
            background: var(--success);
        }
        
        .card-icon.pendentes {
            background: var(--warning);
        }
        
        .card-icon.cancelados {
            background: var(--danger);
        }
        
        .card-icon.total {
            background: var(--primary);
        }
        
        .card-icon.clientes {
            background: var(--secondary);
        }
        
        .card-value {
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        /* Tabela */
        .table-container {
            background: var(--white);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--gray);
        }
        
        th {
            background: var(--light);
            font-weight: 600;
            color: #555;
        }
        
        tr:hover {
            background: rgba(138, 127, 255, 0.05);
        }
        
        .status {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status.pendente {
            background: #fff3e0;
            color: var(--warning);
        }
        
        .status.confirmado {
            background: #e8f5e9;
            color: var(--success);
        }
        
        .status.cancelado {
            background: #ffebee;
            color: var(--danger);
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-action {
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-confirm {
            background: var(--success);
            color: white;
        }
        
        .btn-cancel {
            background: var(--danger);
            color: white;
        }
        
        .btn-pending {
            background: var(--warning);
            color: white;
        }
        
        .btn-action:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }
        
        /* Menu Mobile */
        .menu-toggle {
            display: none;
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 1rem;
        }
        
        /* Responsivo */
        @media (max-width: 992px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                height: 100vh;
                transition: all 0.3s;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <h2>Cibelly Lash</h2>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="?secao=agendamentos" class="nav-link <?= $secao === 'agendamentos' ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Agendamentos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?secao=clientes" class="nav-link <?= $secao === 'clientes' ? 'active' : '' ?>">
                        <i class="fas fa-users"></i>
                        <span>Clientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-concierge-bell"></i>
                        <span>Serviços</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Relatórios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Configurações</span>
                    </a>
                </li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i> Menu
            </button>
            
            <div class="header">
                <h1>Painel Administrativo</h1>
                <div class="user-info">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=8a7fff&color=fff" alt="Admin">
                    <a href="logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </div>
            </div>
            
            <?php if ($secao === 'agendamentos'): ?>
                <!-- Seção de Agendamentos -->
                <div class="stats-cards">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Total Agendamentos</span>
                            <div class="card-icon total">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </div>
                        <div class="card-value"><?= $totalAgendamentos ?></div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Confirmados</span>
                            <div class="card-icon confirmados">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="card-value"><?= $confirmados ?></div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Pendentes</span>
                            <div class="card-icon pendentes">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="card-value"><?= $pendentes ?></div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Cancelados</span>
                            <div class="card-icon cancelados">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                        <div class="card-value"><?= $cancelados ?></div>
                    </div>
                </div>
                
                <!-- Tabela de Agendamentos -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Contato</th>
                                <th>Serviço</th>
                                <th>Data/Hora</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agendamentos as $agendamento): ?>
                            <tr>
                                <td><?= htmlspecialchars($agendamento['nome_cliente']) ?></td>
                                <td><?= htmlspecialchars($agendamento['telefone']) ?></td>
                                <td><?= htmlspecialchars($agendamento['servico']) ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($agendamento['data_agendamento'])) ?>
                                    <br>
                                    <small><?= htmlspecialchars($agendamento['hora_agendamento']) ?></small>
                                </td>
                                <td>
                                    <span class="status <?= htmlspecialchars($agendamento['status']) ?>">
                                        <?= ucfirst(htmlspecialchars($agendamento['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $agendamento['id'] ?>">
                                        <div class="actions">
                                            <button type="submit" name="acao" value="confirmar" 
                                                    class="btn-action btn-confirm" title="Confirmar"
                                                    <?= $agendamento['status'] === 'confirmado' ? 'disabled' : '' ?>>
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="submit" name="acao" value="cancelar" 
                                                    class="btn-action btn-cancel" title="Cancelar"
                                                    <?= $agendamento['status'] === 'cancelado' ? 'disabled' : '' ?>>
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <button type="submit" name="acao" value="pendente" 
                                                    class="btn-action btn-pending" title="Marcar como Pendente"
                                                    <?= $agendamento['status'] === 'pendente' ? 'disabled' : '' ?>>
                                                <i class="fas fa-clock"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            
            <?php elseif ($secao === 'clientes'): ?>
                <!-- Seção de Clientes -->
                <div class="stats-cards">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Total Clientes</span>
                            <div class="card-icon clientes">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="card-value"><?= count($clientes) ?></div>
                    </div>
                </div>
                
                <!-- Tabela de Clientes -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Telefone</th>
                                <th>Total Agendamentos</th>
                                <th>Última Visita</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?= htmlspecialchars($cliente['nome_cliente']) ?></td>
                                <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                                <td><?= htmlspecialchars($cliente['total_agendamentos']) ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($cliente['ultima_visita'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Menu mobile toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Fechar menu ao clicar em um item (mobile)
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('sidebar').classList.remove('active');
            });
        });
    </script>
</body>
</html>