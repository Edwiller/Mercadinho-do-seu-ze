<?php

require_once dirname(__DIR__) . '/dtos/UsuarioDTO.php';
require_once dirname(__DIR__) . '/controllers/UsuarioController.php';

$acao = $_GET['acao'] ?? '';

switch ($acao) {

    case 'salvar':
        $usuarioDTO = new UsuarioDTO();
        $usuarioDTO->nome = $_POST['nome'];
        $usuarioDTO->email = $_POST['email'];
        $usuarioDTO->senha = $_POST['senha'];

        $usuarioController = new UsuarioController();
        try {
            $usuarioController->salvar($usuarioDTO);
            header("Location: login.html");
        } catch (PDOException $erro) {
            echo "Erro na base de dados" . $erro->getMessage();
        } catch (Exception $erro) {
            echo "Erro inesperado" . $erro->getMessage();
        }
        break;

    case 'autenticar':
        $usuarioDTO = new UsuarioDTO();
        $usuarioDTO->email = $_POST['email'];
        $usuarioDTO->senha = $_POST['senha'];

        $usuarioController = new UsuarioController();
        try {
            $usuario = $usuarioController->autenticar($usuarioDTO);
            session_start();
            $_SESSION['usuario'] = $usuario->getNome();
            header("Location: home.php");
        } catch (Throwable $erro) {
            echo "Erro: " . $erro->getMessage();
        }
        break;
}