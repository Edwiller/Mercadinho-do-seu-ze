const API = 'http://localhost:8000/usuarios.php';

/**
 * Função padrão para comunicação com a API.
 */
async function chamar(acao, opcoes = {}) {
    return fetch(`${API}?acao=${acao}`, {
        credentials: 'include',
        ...opcoes
    });
}


/**
 * Inicializa o painel.
 *
 * Verifica se existe uma sessão válida e,
 * caso o usuário seja ADMIN, libera a área
 * de gerenciamento de usuários.
 */
async function iniciar() {

    try {

        const resposta = await chamar('sessao');

        if (!resposta.ok) {
            window.location.href = 'login.html';
            return;
        }

        const dadosSessao = await resposta.json();

        if (!dadosSessao.sucesso || !dadosSessao.usuario) {
            window.location.href = 'login.html';
            return;
        }

        const usuario = dadosSessao.usuario;

        const titulo = document.getElementById('titulo');

        if (titulo) {
            titulo.textContent = `Bem-vindo, ${usuario.nome}!`;
        }

        /*
         * Somente ADMIN pode visualizar
         * o gerenciamento de usuários.
         */
        if (usuario.perfil === 'ADMIN') {

            const areaAdmin =
                document.getElementById('area-admin');

            if (areaAdmin) {
                areaAdmin.classList.remove('escondido');
            }

            await carregarTabela();
        }

    } catch (erro) {

        console.error(
            'Erro ao iniciar o painel:',
            erro
        );

        window.location.href = 'login.html';
    }
}


/**
 * Carrega todos os usuários cadastrados.
 *
 * A própria API também verifica se o usuário
 * possui perfil ADMIN.
 */
async function carregarTabela() {

    try {

        const resposta = await chamar('listar');

        const dados = await resposta.json();

        if (!resposta.ok || !dados.sucesso) {

            alert(
                dados.erro ||
                'Não foi possível carregar os usuários.'
            );

            return;
        }

        const tabela =
            document.getElementById('tabela');

        if (!tabela) {
            return;
        }

        tabela.innerHTML = '';

        /*
         * Nenhum usuário encontrado.
         */
        if (
            !dados.dados ||
            dados.dados.length === 0
        ) {

            tabela.innerHTML = `
                <tr>
                    <td colspan="6">
                        Nenhum usuário cadastrado.
                    </td>
                </tr>
            `;

            return;
        }


        /*
         * Percorre todos os usuários.
         */
        dados.dados.forEach((usuario) => {

            const ativo =
                Number(usuario.ativo) === 1;

            const statusClass = ativo
                ? 'status-ativo'
                : 'status-inativo';

            const statusText = ativo
                ? 'Ativo'
                : 'Inativo';


            /*
             * Usuários ativos podem ser editados
             * ou desativados.
             *
             * Usuários inativos não possuem ações.
             */
            const acoes = ativo

                ? `
                    <button
                        type="button"
                        class="btn-acao btn-editar"
                        onclick="editarUsuario(${usuario.id})"
                        title="Editar usuário">

                        Editar

                    </button>

                    <button
                        type="button"
                        class="btn-acao btn-excluir"
                        onclick="excluirUsuario(${usuario.id})"
                        title="Desativar usuário">

                        Desativar

                    </button>
                `

                : `
                    <span>
                        Sem ações disponíveis
                    </span>
                `;


            tabela.innerHTML += `
                <tr>

                    <td>
                        ${usuario.id}
                    </td>

                    <td>
                        ${usuario.nome}
                    </td>

                    <td>
                        ${usuario.email}
                    </td>

                    <td>
                        ${usuario.perfil}
                    </td>

                    <td class="${statusClass}">
                        ${statusText}
                    </td>

                    <td>
                        ${acoes}
                    </td>

                </tr>
            `;
        });

    } catch (erro) {

        console.error(
            'Erro ao carregar usuários:',
            erro
        );

        alert(
            'Não foi possível estabelecer comunicação com o servidor.'
        );
    }
}


/**
 * Redireciona o ADMIN para a tela de edição.
 */
function editarUsuario(id) {

    if (!id) {

        alert(
            'O identificador do usuário é inválido.'
        );

        return;
    }

    window.location.href =
        `editar-usuario.html?id=${encodeURIComponent(id)}`;
}


/**
 * Desativa um usuário.
 *
 * O registro não é removido do banco.
 * Apenas o campo "ativo" passa para 0.
 */
async function excluirUsuario(id) {

    if (!id) {

        alert(
            'O identificador do usuário é inválido.'
        );

        return;
    }


    const confirmar = confirm(
        'Deseja realmente desativar este usuário?\n\n' +
        'Após a desativação, o usuário não poderá ' +
        'acessar o sistema.'
    );


    if (!confirmar) {
        return;
    }


    try {

        const resposta = await chamar(
            `excluir&id=${encodeURIComponent(id)}`
        );

        const dados = await resposta.json();


        if (!resposta.ok || !dados.sucesso) {

            alert(
                dados.erro ||
                'Não foi possível desativar o usuário.'
            );

            return;
        }


        alert(
            dados.mensagem ||
            'Usuário desativado com sucesso.'
        );


        /*
         * Atualiza a tabela sem precisar
         * recarregar toda a página.
         */
        await carregarTabela();

    } catch (erro) {

        console.error(
            'Erro ao desativar usuário:',
            erro
        );

        alert(
            'Não foi possível estabelecer comunicação com o servidor.'
        );
    }
}


/**
 * Encerra a sessão atual.
 */
async function sair() {

    try {

        const resposta = await chamar('sair');

        /*
         * Mesmo que a API apresente algum erro,
         * o usuário deve ser levado para o login.
         */
        if (!resposta.ok) {

            console.warn(
                'A API retornou um erro ao encerrar a sessão.'
            );
        }

    } catch (erro) {

        console.error(
            'Erro ao encerrar sessão:',
            erro
        );

    } finally {

        window.location.href = 'login.html';
    }
}


/**
 * Botão de logout.
 */
const botaoSair =
    document.getElementById('sair');

if (botaoSair) {

    botaoSair.addEventListener(
        'click',
        sair
    );
}


/**
 * Torna as funções disponíveis para os
 * botões criados dinamicamente pela tabela.
 */
window.editarUsuario = editarUsuario;
window.excluirUsuario = excluirUsuario;


/**
 * Inicia o painel.
 */
iniciar();