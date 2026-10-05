<?php

require_once '../controllers/UsuarioController.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido.']);
    exit;
}

$corpo = json_decode(file_get_contents('php://input'), true) ?? [];

$dto = new UsuarioDTO();
$dto->email = $corpo['email'] ?? null;
$dto->senha = $corpo['senha'] ?? null;

try {
    $usuario = (new UsuarioController())->autenticar($dto);
    echo json_encode(['sucesso' => true, 'usuario' => $usuario]);
} catch (InvalidArgumentException $e) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Erro interno no servidor.']);
}