<?php
require __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    // Substitua por suas credenciais reais
    $usuarioValido = 'admin';
    $senhaValida = 'senha123'; // Na prática, use password_hash()

    if ($usuario === $usuarioValido && $senha === $senhaValida) {
        session_start();
        $_SESSION['admin_logado'] = true;
        header('Location: index.php');
        exit;
    } else {
        $erro = 'Credenciais inválidas';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Restrito | Painel Admin</title>
    <style>
        :root {
            --primary: #8a7fff;
            --primary-dark: #6a5acd;
            --secondary: #ff7eb9;
            --text: #2e2e3a;
            --light: #f8f9fa;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text);
        }
        
        .login-container {
            background: var(--white);
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
            text-align: center;
            animation: fadeIn 0.6s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo {
            width: 80px;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }
        
        h2 {
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .erro {
            color: #ff4757;
            background: #ffebee;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            animation: shake 0.5s;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        
        input {
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(138, 127, 255, 0.2);
        }
        
        input::placeholder {
            color: #a0a0a0;
        }
        
        button {
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(106, 90, 205, 0.4);
        }
        
        .footer-text {
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #7a7a8c;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
                margin: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Adicione sua logo aqui -->
        <svg class="logo" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
            <path fill="currentColor" d="M12 6c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/>
        </svg>
        
        <h2>Acesso Restrito</h2>
        
        <?php if (isset($erro)): ?>
            <p class="erro"><?= $erro ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuário" required autofocus>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Acessar Painel</button>
        </form>
        
        <p class="footer-text">Cibelly Lash Design • © <?= date('Y') ?></p>
    </div>
</body>
</html>