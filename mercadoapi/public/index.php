<?php

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'sucesso' => true,
    'mensagem' => 'Backend do Mercadinho do Seu Zé funcionando!'
]);