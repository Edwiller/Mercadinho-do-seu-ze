const API = 'http://localhost:8000/usuarios.php';

const formulario = document.getElementById('form-editar');
const mensagem = document.getElementById('mensagem');

const parametros = new URLSearchParams(window.location.search);

const id = parametros.get('id');

if (!id) {
    window.location.href = 'home.html';
}

async function carregarUsuario() {

    try {

        const resposta = await fetch(
            `${API}?acao=listar`,
            {
                credentials: 'include'
            }
        );

        const dados = await resposta.json();

        if (!resposta.ok || !dados.sucesso) {

            alert(
                dados.erro ||
                'Não foi possível carregar os usuários.'
            );

            window.location.href = 'home.html';

            return;
        }

        const usuario = dados.dados.find(
            item => Number(item.id) === Number(id)
        );

        if (!usuario) {

            alert('Usuário não encontrado.');

            window.location.href = 'home.html';

            return;
        }

        document.getElementById('id').value = usuario.id;
        document.getElementById('nome').value = usuario.nome;
        document.getElementById('email').value = usuario.email;

    } catch (erro) {

        console.error(erro);

        alert(
            'Não foi possível estabelecer comunicação com o servidor.'
        );

        window.location.href = 'home.html';
    }
}

formulario.addEventListener('submit', async (e) => {

    e.preventDefault();

    mensagem.textContent = '';
    mensagem.className = 'mensagem';

    const dadosUsuario = {
        id: Number(document.getElementById('id').value),
        nome: document.getElementById('nome').value.trim(),
        email: document.getElementById('email').value.trim()
    };

    try {

        const resposta = await fetch(
            `${API}?acao=editar`,
            {
                method: 'POST',

                credentials: 'include',

                headers: {
                    'Content-Type': 'application/json'
                },

                body: JSON.stringify(dadosUsuario)
            }
        );

        const dados = await resposta.json();

        if (!resposta.ok || !dados.sucesso) {

            mensagem.className = 'mensagem mensagem-erro';

            mensagem.textContent =
                dados.erro ||
                'Não foi possível atualizar o usuário.';

            return;
        }

        mensagem.className = 'mensagem mensagem-sucesso';

        mensagem.textContent =
            dados.mensagem ||
            'Usuário atualizado com sucesso.';

        setTimeout(() => {

            window.location.href = 'home.html';

        }, 1000);

    } catch (erro) {

        console.error(erro);

        mensagem.className = 'mensagem mensagem-erro';

        mensagem.textContent =
            'Não foi possível estabelecer comunicação com o servidor.';
    }
});

carregarUsuario();