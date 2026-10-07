<?php

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/daos/UsuarioDAO.php';

class UsuarioService {

    public function salvar($usuario) {
        $conn = Conexao::criar();
        $usuarioDAO = new UsuarioDAO();
        $usuarioDAO->salvar($usuario, $conn);
    }

    public function autenticar($usuario) {
        $conn = Conexao::criar();
        $usuarioDAO = new UsuarioDAO();
        $usuarioBase = $usuarioDAO->buscarPeloEmail($usuario, $conn);

        if ($usuarioBase != null) {
            if (password_verify($usuario->getSenha(), $usuarioBase->getSenha())) {
                return $usuarioBase;
            }
        }
        throw new Exception("E-mail ou senha não conferem");
    }

    public function listar() {
        $conn = Conexao::criar();
        $usuarioDAO = new UsuarioDAO();
        return $usuarioDAO->listar($conn);
    }
}