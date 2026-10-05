<?php

require_once dirname(__DIR__) . '/models/Usuario.php';

class UsuarioDAO
{
    public function salvar(Usuario $usuario, PDO $conn): int
    {
        $sql = 'INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $usuario->getNome());
        $stmt->bindValue(2, $usuario->getEmail());
        $stmt->bindValue(3, $usuario->getSenha());
        $stmt->bindValue(4, $usuario->getPerfil());
        $stmt->execute();

        return (int) $conn->lastInsertId();
    }

    public function buscarPeloEmail(string $email, PDO $conn): ?Usuario
    {
        $stmt = $conn->prepare('SELECT * FROM usuarios WHERE email = ?');
        $stmt->bindValue(1, $email);
        $stmt->execute();

        $linha = $stmt->fetch();

        return $linha ? $this->paraModelo($linha) : null;
    }

    public function listar(PDO $conn): array
    {
        $stmt = $conn->query('SELECT * FROM usuarios ORDER BY id DESC');

        return array_map([$this, 'paraModelo'], $stmt->fetchAll());
    }

    private function paraModelo(array $linha): Usuario
    {
        $usuario = new Usuario();
        $usuario->setId((int) $linha['id']);
        $usuario->setNome($linha['nome']);
        $usuario->setEmail($linha['email']);
        $usuario->setSenha($linha['senha']);
        $usuario->setPerfil($linha['perfil']);
        $usuario->setAtivo((bool) $linha['ativo']);
        $usuario->setCriadoEm($linha['criado_em']);

        return $usuario;
    }
}