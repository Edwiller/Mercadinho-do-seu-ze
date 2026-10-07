<?php

require_once dirname(__DIR__) . '/dtos/UsuarioDTO.php';
require_once dirname(__DIR__) . '/models/Usuario.php';
require_once dirname(__DIR__) . '/services/UsuarioService.php';

class UsuarioController {

    public function salvar($usuarioDTO) {
        // converter DTO para modelo
        $usuario = new Usuario();
        $usuario->setNome($usuarioDTO->nome);
        $usuario->setEmail($usuarioDTO->email);
        $usuario->setSenha($usuarioDTO->senha);

        $usuarioService = new UsuarioService();
        $usuarioService->salvar($usuario);
    }

    public function autenticar($usuarioDTO) {
        $usuario = new Usuario();
        $usuario->setEmail($usuarioDTO->email);
        $usuario->setSenha($usuarioDTO->senha);

        $usuarioService = new UsuarioService();
        return $usuarioService->autenticar($usuario);
    }

    public function listar() {
        $usuarioService = new UsuarioService();
        return $usuarioService->listar();
    }
}