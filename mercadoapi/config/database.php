<?php

class Conexao {

    static public function criar() {
        $conn = new PDO("mysql:host=127.0.0.1;dbname=mercadinho_seu_ze",
            "root", "root");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}