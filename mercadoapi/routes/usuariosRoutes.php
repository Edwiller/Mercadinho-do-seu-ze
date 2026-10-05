<?php

require_once dirname(__DIR__) . '/dtos/UsuarioDTO.php';
require_once dirname(__DIR__) . '/controllers/UsuarioController.php';

header('Content-Type: application/json; charset=utf-8');

$controller = new UsuarioController();

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            echo json_encode(['sucesso' => true, 'dados' => $controller->listar()]);
            break;

        case 'POST':
            $corpo = json_decode(file_get_contents('php://input'), true) ?? [];

            $dto = new UsuarioDTO();
            $dto->nome  = $corpo['nome'] ?? null;
            $dto->email = $corpo['email'] ?? null;
            $dto->senha = $corpo['senha'] ?? null;

            $id = $controller->salvar($dto);

            http_response_code(201);
            echo json_encode(['sucesso' => true, 'id' => $id]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido.']);
    }
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Erro interno no servidor.']);
}