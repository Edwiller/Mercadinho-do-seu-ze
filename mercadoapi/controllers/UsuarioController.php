<?php

require_once dirname(__DIR__) . '/services/UsuarioService.php';
require_once dirname(__DIR__) . '/dtos/UsuarioDTO.php';

class UsuarioController
{
    private UsuarioService $service;

    public function __construct()
    {
        $this->service = new UsuarioService();
    }

    public function salvar(UsuarioDTO $usuario): void
    {
        $this->service->salvar($usuario);
    }

    public function autenticar(UsuarioDTO $usuario): UsuarioDTO
    {
        return $this->service->autenticar($usuario);
    }

    public function listar(): array
    {
        return $this->service->listar();
    }

    public function excluir(int $id): void
    {
        $this->service->excluir($id);
    }
    public function editar(UsuarioDTO $usuario): void
    {
        $this->service->editar($usuario);
    }
}