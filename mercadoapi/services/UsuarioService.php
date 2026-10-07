<?php

require_once dirname(__DIR__) . '/daos/UsuarioDAO.php';
require_once dirname(__DIR__) . '/dtos/UsuarioDTO.php';

class UsuarioService
{
    private UsuarioDAO $dao;

    public function __construct()
    {
        $this->dao = new UsuarioDAO();
    }

    public function salvar(UsuarioDTO $usuario)
    {
        $nome = trim((string) $usuario->nome);
        $email = trim((string) $usuario->email);
        $senha = (string) $usuario->senha;

        if ($nome === '' || $email === '' || $senha === '') {
            throw new InvalidArgumentException(
                'Todos os campos obrigatórios devem ser preenchidos.'
            );
        }

        if (mb_strlen($nome) < 3) {
            throw new InvalidArgumentException(
                'O nome deve possuir pelo menos 3 caracteres.'
            );
        }

        if (mb_strlen($nome) > 100) {
            throw new InvalidArgumentException(
                'O nome deve possuir no máximo 100 caracteres.'
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                'O endereço de e-mail informado é inválido.'
            );
        }

        if (strlen($senha) < 6) {
            throw new InvalidArgumentException(
                'A senha deve possuir no mínimo 6 caracteres.'
            );
        }

        $usuario->nome = $nome;
        $usuario->email = strtolower($email);
        $usuario->senha = $senha;

        $this->dao->salvar($usuario);
    }

    public function autenticar(UsuarioDTO $usuario): UsuarioDTO
    {
        $email = trim((string) $usuario->email);
        $senha = (string) $usuario->senha;

        if ($email === '' || $senha === '') {
            throw new InvalidArgumentException(
                'O e-mail e a senha devem ser informados.'
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                'O endereço de e-mail informado é inválido.'
            );
        }

        $usuario->email = strtolower($email);
        $usuario->senha = $senha;

        return $this->dao->autenticar($usuario);
    }

    public function listar(): array
    {
        return $this->dao->listar();
    }

    public function excluir(int $id)
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                'O identificador do usuário informado é inválido.'
            );
        }

        $this->dao->excluir($id);
    }
    public function editar(UsuarioDTO $usuario): void
{
    if (!$usuario->id || $usuario->id <= 0) {
        throw new InvalidArgumentException(
            'O identificador do usuário é inválido.'
        );
    }

    $nome = trim((string) $usuario->nome);
    $email = trim((string) $usuario->email);

    if ($nome === '' || $email === '') {
        throw new InvalidArgumentException(
            'Nome e e-mail são obrigatórios.'
        );
    }

    if (mb_strlen($nome) < 3) {
        throw new InvalidArgumentException(
            'O nome deve possuir pelo menos 3 caracteres.'
        );
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException(
            'O endereço de e-mail informado é inválido.'
        );
    }

    $usuario->nome = $nome;
    $usuario->email = strtolower($email);

    $this->dao->editar($usuario);
}
}