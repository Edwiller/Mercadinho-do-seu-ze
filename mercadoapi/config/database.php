<?php

class Database
{
    private static $conn = null;

    public static function getConnection()
    {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO("mysql:host=127.0.0.1;dbname=mercadinho_seu_ze;charset=utf8mb4","root","admin" );

                self::$conn->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                self::$conn->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );
            } catch (PDOException $e) {
                throw new RuntimeException(
                    "Não foi possível estabelecer conexão com o banco de dados."
                );
            }
        }

        return self::$conn;
    }
}