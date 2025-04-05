<?php
session_start();

// Credenciais válidas (altere depois!)
$usuario_valido = 'admin';
$senha_valida = 'senha123';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['usuario'] === $usuario_valido && $_POST['senha'] === $senha_valida) {
        $_SESSION['logado'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}

// Se não está logado, mostra o formulário
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #ff69b4; color: white; border: none; padding: 10px; width: 100%; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Painel Admin - Login</h2>
        <?php if (isset($erro)) echo "<p style='color:red'>$erro</p>"; ?>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuário" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>