<?php

require_once dirname(__DIR__) . '/controllers/UsuarioController.php';

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit;
}

$usuarioController = new UsuarioController();
$usuarios = $usuarioController->listar();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo $_SESSION['usuario']; ?>!</h1>

    <h2>Usuários cadastrados</h2>
    <table border="1" cellpadding="6">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>E-mail</th>
        </tr>
        <?php foreach ($usuarios as $u) { ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo $u['nome']; ?></td>
            <td><?php echo $u['email']; ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>