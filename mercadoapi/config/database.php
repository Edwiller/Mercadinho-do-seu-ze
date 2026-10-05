<?php

class Conexao
{
    private static function carregarEnv(): array
    {
        $arquivoEnv = dirname(__DIR__, 2) . '/.env';

        if (!file_exists($arquivoEnv)) {
            throw new Exception('Arquivo .env não encontrado.');
        }

        $variaveis = [];
        $linhas = file($arquivoEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($linhas as $linha) {
            $linha = trim($linha);

            if ($linha === '' || str_starts_with($linha, '#')) {
                continue;
            }

            [$chave, $valor] = array_pad(explode('=', $linha, 2), 2, '');
            $variaveis[trim($chave)] = trim($valor);
        }

        return $variaveis;
    }

    public static function criar(): PDO
    {
        $env = self::carregarEnv();

        $dsn = "mysql:host={$env['DB_HOST']};port={$env['DB_PORT']};"
             . "dbname={$env['DB_NAME']};charset=utf8mb4";

        return new PDO($dsn, $env['DB_USER'], $env['DB_PASSWORD'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}