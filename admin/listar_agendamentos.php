<?php include('conexao.php'); ?>

// Consulta os agendamentos
$sql = "SELECT * FROM agendamentos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - Agendamentos</title>
    <link rel="stylesheet" href="estilo_admin.css">
</head>
<body>
    <h1>Agendamentos</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Data</th>
            <th>Serviço</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row["id"] . "</td>
                        <td>" . $row["cliente"] . "</td>
                        <td>" . $row["data"] . "</td>
                        <td>" . $row["servico"] . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Nenhum agendamento encontrado.</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>