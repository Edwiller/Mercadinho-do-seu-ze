<?php

$host = '127.0.0.1';
$porta = '3306';
$banco = 'mercadinho_seu_ze';
$usuario = 'root';
$senha = 'admin';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Erro na conexão com o banco: ' . $e->getMessage());
}