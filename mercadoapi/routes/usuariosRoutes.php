<?php

require_once dirname(__DIR__) . '/config/cors.php';
require_once dirname(__DIR__) . '/dtos/UsuarioDTO.php';
require_once dirname(__DIR__) . '/controllers/UsuarioController.php';

header('Content-Type: application/json; charset=utf-8');

session_start();

function responder(array $dados, int $status = 200): void
{
    http_response_code($status);

    echo json_encode(
        $dados,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

$acao = $_GET['acao'] ?? '';

$entrada = file_get_contents('php://input');

$corpo = [];

if ($entrada !== '') {
    $corpo = json_decode($entrada, true);

    if (!is_array($corpo)) {
        responder([
            'sucesso' => false,
            'erro' => 'Os dados enviados possuem formato inválido.'
        ], 400);
    }
}

try {

    switch ($acao) {

        /*
        |--------------------------------------------------------------------------
        | CADASTRAR USUÁRIO
        |--------------------------------------------------------------------------
        */
        case 'salvar':

            $usuarioDTO = new UsuarioDTO();

            $usuarioDTO->nome = $corpo['nome'] ?? null;
            $usuarioDTO->email = $corpo['email'] ?? null;
            $usuarioDTO->senha = $corpo['senha'] ?? null;

            $usuarioController = new UsuarioController();

            $usuarioController->salvar($usuarioDTO);

            responder([
                'sucesso' => true,
                'mensagem' => 'Usuário cadastrado com sucesso.'
            ]);

            break;


        /*
        |--------------------------------------------------------------------------
        | LOGIN
        |--------------------------------------------------------------------------
        */
        case 'autenticar':

            $usuarioDTO = new UsuarioDTO();

            $usuarioDTO->email = $corpo['email'] ?? null;
            $usuarioDTO->senha = $corpo['senha'] ?? null;

            $usuarioController = new UsuarioController();

            try {

                $usuario = $usuarioController->autenticar($usuarioDTO);

            } catch (InvalidArgumentException $erro) {

                responder([
                    'sucesso' => false,
                    'erro' => $erro->getMessage()
                ], 400);

            } catch (DomainException $erro) {

                responder([
                    'sucesso' => false,
                    'erro' => $erro->getMessage()
                ], 401);
            }

            session_regenerate_id(true);

            $_SESSION['usuario'] = [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'perfil' => $usuario->perfil
            ];

            responder([
                'sucesso' => true,
                'mensagem' => 'Autenticação realizada com sucesso.',
                'usuario' => $_SESSION['usuario']
            ]);

            break;


        /*
        |--------------------------------------------------------------------------
        | VERIFICAR SESSÃO
        |--------------------------------------------------------------------------
        */
        case 'sessao':

            if (!isset($_SESSION['usuario'])) {

                responder([
                    'sucesso' => false,
                    'erro' => 'Acesso não autenticado. Efetue o login para continuar.'
                ], 401);
            }

            responder([
                'sucesso' => true,
                'usuario' => $_SESSION['usuario']
            ]);

            break;


        /*
        |--------------------------------------------------------------------------
        | LISTAR USUÁRIOS
        |--------------------------------------------------------------------------
        */
        case 'listar':

            if (!isset($_SESSION['usuario'])) {

                responder([
                    'sucesso' => false,
                    'erro' => 'Acesso não autenticado. Efetue o login para continuar.'
                ], 401);
            }

            if ($_SESSION['usuario']['perfil'] !== 'ADMIN') {

                responder([
                    'sucesso' => false,
                    'erro' => 'Acesso negado. Esta operação requer privilégios administrativos.'
                ], 403);
            }

            $usuarioController = new UsuarioController();

            responder([
                'sucesso' => true,
                'dados' => $usuarioController->listar()
            ]);

            break;


        /*
        |--------------------------------------------------------------------------
        | INATIVAR USUÁRIO
        |--------------------------------------------------------------------------
        */
        case 'excluir':

            if (!isset($_SESSION['usuario'])) {

                responder([
                    'sucesso' => false,
                    'erro' => 'Acesso não autenticado. Efetue o login para continuar.'
                ], 401);
            }

            if ($_SESSION['usuario']['perfil'] !== 'ADMIN') {

                responder([
                    'sucesso' => false,
                    'erro' => 'Acesso negado. Esta operação requer privilégios administrativos.'
                ], 403);
            }

            $id = filter_var(
                $_GET['id'] ?? null,
                FILTER_VALIDATE_INT
            );

            if ($id === false || $id === null || $id <= 0) {

                responder([
                    'sucesso' => false,
                    'erro' => 'O identificador do usuário informado é inválido.'
                ], 400);
            }

            // Impede que o administrador desative a própria conta
            if ($id === (int) $_SESSION['usuario']['id']) {

                responder([
                    'sucesso' => false,
                    'erro' => 'Não é permitido desativar o próprio usuário durante esta sessão.'
                ], 400);
            }

            $usuarioController = new UsuarioController();

            $usuarioController->excluir($id);

            responder([
                'sucesso' => true,
                'mensagem' => 'Usuário desativado com sucesso.'
            ]);

            break;

        case 'editar':

            if (!isset($_SESSION['usuario'])) {

                responder([
                    'sucesso' => false,
                    'erro' => 'Acesso não autenticado. Efetue o login para continuar.'
                ], 401);
            }

            if ($_SESSION['usuario']['perfil'] !== 'ADMIN') {

                responder([
                    'sucesso' => false,
                    'erro' => 'Acesso negado. Esta operação requer privilégios administrativos.'
                ], 403);
            }

            $id = filter_var(
                $corpo['id'] ?? null,
                FILTER_VALIDATE_INT
            );

            if ($id === false || $id === null || $id <= 0) {

                responder([
                    'sucesso' => false,
                    'erro' => 'O identificador do usuário informado é inválido.'
                ], 400);
            }

            /*
             * Impede alteração do próprio perfil por esta operação.
             */
            if ($id === (int) $_SESSION['usuario']['id']) {

                responder([
                    'sucesso' => false,
                    'erro' => 'A alteração do próprio usuário não está disponível nesta operação.'
                ], 400);
            }

            $usuarioDTO = new UsuarioDTO();

            $usuarioDTO->id = $id;
            $usuarioDTO->nome = $corpo['nome'] ?? null;
            $usuarioDTO->email = $corpo['email'] ?? null;

            $usuarioController = new UsuarioController();

            $usuarioController->editar($usuarioDTO);

            responder([
                'sucesso' => true,
                'mensagem' => 'Usuário atualizado com sucesso.'
            ]);

            break;
        /*
        |--------------------------------------------------------------------------
        | LOGOUT
        |--------------------------------------------------------------------------
        */
        case 'sair':

            $_SESSION = [];

            if (ini_get('session.use_cookies')) {

                $params = session_get_cookie_params();

                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();

            responder([
                'sucesso' => true,
                'mensagem' => 'Sessão encerrada com sucesso.'
            ]);

            break;


        /*
        |--------------------------------------------------------------------------
        | AÇÃO INVÁLIDA
        |--------------------------------------------------------------------------
        */
        default:

            responder([
                'sucesso' => false,
                'erro' => 'A ação solicitada não é válida.'
            ], 400);
    }

} catch (DomainException $erro) {

    responder([
        'sucesso' => false,
        'erro' => $erro->getMessage()
    ], 409);

} catch (InvalidArgumentException $erro) {

    responder([
        'sucesso' => false,
        'erro' => $erro->getMessage()
    ], 400);

} catch (Throwable $erro) {

    // Não expor detalhes internos do servidor para o navegador.
    error_log($erro->getMessage());

    responder([
        'sucesso' => false,
        'erro' => 'Não foi possível concluir a operação no momento. Tente novamente.'
    ], 500);
}