<?php

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/dtos/UsuarioDTO.php';

class UsuarioDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function salvar(UsuarioDTO $usuario)
    {
        $sql = "
            INSERT INTO usuarios
                (nome, email, senha, perfil)
            VALUES
                (:nome, :email, :senha, 'FUNCIONARIO')
        ";

        $stmt = $this->conn->prepare($sql);

        $hash = password_hash(
            $usuario->senha,
            PASSWORD_DEFAULT
        );

        $stmt->bindValue(':nome', $usuario->nome);
        $stmt->bindValue(':email', $usuario->email);
        $stmt->bindValue(':senha', $hash);

        try {
            $stmt->execute();
        } catch (PDOException $e) {

            // E-mail duplicado
            if ($e->getCode() === '23000') {
                throw new DomainException(
                    'O endereço de e-mail informado já está cadastrado.'
                );
            }

            throw new RuntimeException(
                'Não foi possível concluir o cadastro.'
            );
        }
    }

    public function autenticar(UsuarioDTO $usuario): UsuarioDTO
    {
        $sql = "
            SELECT
                id,
                nome,
                email,
                senha,
                perfil,
                ativo
            FROM usuarios
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $usuario->email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new DomainException(
                'Não foi possível realizar a autenticação. Verifique o e-mail e a senha informados.'
            );
        }

        if ((int) $row['ativo'] !== 1) {
            throw new DomainException(
                'O usuário informado está inativo. Entre em contato com um administrador.'
            );
        }

        if (!password_verify($usuario->senha, $row['senha'])) {
            throw new DomainException(
                'Não foi possível realizar a autenticação. Verifique o e-mail e a senha informados.'
            );
        }

        $dto = new UsuarioDTO();

        $dto->id = $row['id'];
        $dto->nome = $row['nome'];
        $dto->email = $row['email'];
        $dto->perfil = $row['perfil'];
        $dto->ativo = $row['ativo'];

        return $dto;
    }

    public function listar(): array
    {
        $sql = "
            SELECT
                id,
                nome,
                email,
                perfil,
                ativo,
                criado_em
            FROM usuarios
            ORDER BY nome ASC
        ";

        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function excluir(int $id)
    {
        $sql = "
            UPDATE usuarios
            SET ativo = 0
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new DomainException(
                'Usuário não encontrado.'
            );
        }
    }
    public function editar(UsuarioDTO $usuario): void
{
    $sql = "
        UPDATE usuarios
        SET
            nome = :nome,
            email = :email
        WHERE id = :id
    ";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindValue(':nome', $usuario->nome);
    $stmt->bindValue(':email', $usuario->email);
    $stmt->bindValue(':id', $usuario->id, PDO::PARAM_INT);

    try {

        $stmt->execute();

    } catch (PDOException $e) {

        if ($e->getCode() === '23000') {
            throw new DomainException(
                'O endereço de e-mail informado já está cadastrado.'
            );
        }

        throw new RuntimeException(
            'Não foi possível atualizar os dados do usuário.'
        );
    }

    if ($stmt->rowCount() === 0) {

        $verificar = $this->conn->prepare(
            "SELECT id FROM usuarios WHERE id = :id"
        );

        $verificar->bindValue(
            ':id',
            $usuario->id,
            PDO::PARAM_INT
        );

        $verificar->execute();

        if (!$verificar->fetch()) {
            throw new DomainException(
                'Usuário não encontrado.'
            );
        }
    }
}
}