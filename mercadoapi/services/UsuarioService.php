<?php

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/daos/UsuarioDAO.php';

class UsuarioService
{
    public function salvar(Usuario $usuario): int
    {
        if (trim((string) $usuario->getNome()) === ''
            || !filter_var($usuario->getEmail(), FILTER_VALIDATE_EMAIL)
            || strlen((string) $usuario->getSenha()) < 6) {
            throw new InvalidArgumentException(
                'Informe nome, e-mail válido e senha com no mínimo 6 caracteres.'
            );
        }

        $conn = Conexao::criar();
        $dao = new UsuarioDAO();

        if ($dao->buscarPeloEmail($usuario->getEmail(), $conn) !== null) {
            throw new InvalidArgumentException('E-mail já cadastrado.');
        }

        $usuario->setSenha(password_hash($usuario->getSenha(), PASSWORD_DEFAULT));

        return $dao->salvar($usuario, $conn);
    }

    public function autenticar(Usuario $usuario): Usuario
    {
        $conn = Conexao::criar();
        $dao = new UsuarioDAO();

        $usuarioBase = $dao->buscarPeloEmail($usuario->getEmail(), $conn);

        if ($usuarioBase !== null
            && $usuarioBase->getAtivo()
            && password_verify($usuario->getSenha(), $usuarioBase->getSenha())) {
            return $usuarioBase;
        }

        throw new InvalidArgumentException('E-mail ou senha não conferem.');
    }

    public function listar(): array
    {
        return (new UsuarioDAO())->listar(Conexao::criar());
    }
}