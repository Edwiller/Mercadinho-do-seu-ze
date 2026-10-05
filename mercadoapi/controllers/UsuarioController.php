<?php

require_once dirname(__DIR__) . '/dtos/UsuarioDTO.php';
require_once dirname(__DIR__) . '/models/Usuario.php';
require_once dirname(__DIR__) . '/services/UsuarioService.php';

class UsuarioController
{
    private UsuarioService $service;

    public function __construct()
    {
        $this->service = new UsuarioService();
    }

    public function salvar(UsuarioDTO $dto): int
    {
        $usuario = new Usuario();
        $usuario->setNome($dto->nome);
        $usuario->setEmail($dto->email);
        $usuario->setSenha($dto->senha);

        return $this->service->salvar($usuario);
    }

    public function autenticar(UsuarioDTO $dto): array
    {
        $usuario = new Usuario();
        $usuario->setEmail($dto->email);
        $usuario->setSenha($dto->senha);

        return $this->paraArray($this->service->autenticar($usuario));
    }

    public function listar(): array
    {
        return array_map([$this, 'paraArray'], $this->service->listar());
    }

    // Nunca devolve a senha (nem o hash) para o frontend
    private function paraArray(Usuario $u): array
    {
        return [
            'id'        => $u->getId(),
            'nome'      => $u->getNome(),
            'email'     => $u->getEmail(),
            'perfil'    => $u->getPerfil(),
            'ativo'     => $u->getAtivo(),
            'criado_em' => $u->getCriadoEm(),
        ];
    }
}