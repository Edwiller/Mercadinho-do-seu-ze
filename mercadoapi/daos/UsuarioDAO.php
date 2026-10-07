<?php

require_once dirname(__DIR__) . '/models/Usuario.php';

class UsuarioDAO {

    public function salvar($usuario, $conn) {
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $usuario->getNome());
        $stmt->bindValue(2, $usuario->getEmail());
        $stmt->bindValue(3, password_hash($usuario->getSenha(), PASSWORD_DEFAULT));
        $stmt->execute();
    }

    public function buscarPeloEmail($usuario, $conn) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $usuario->getEmail());
        $stmt->execute();
        $usuarioBase = $stmt->fetch(PDO::FETCH_OBJ);

        if ($usuarioBase) {
            // converter o objeto do banco para o modelo
            $usuarioModelo = new Usuario();
            $usuarioModelo->setId($usuarioBase->id);
            $usuarioModelo->setNome($usuarioBase->nome);
            $usuarioModelo->setEmail($usuarioBase->email);
            $usuarioModelo->setSenha($usuarioBase->senha);
            return $usuarioModelo;
        } else {
            return null;
        }
    }

    public function listar($conn) {
        $sql = "SELECT id, nome, email FROM usuarios";
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}