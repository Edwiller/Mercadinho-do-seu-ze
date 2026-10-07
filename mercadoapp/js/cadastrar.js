const API = 'http://localhost:8000/usuarios.php';

const formulario = document.getElementById('form-cadastro');
const mensagem = document.getElementById('mensagem');

formulario.addEventListener('submit', async (e) => {
    e.preventDefault();

    mensagem.textContent = '';
    mensagem.className = 'mensagem';

    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value;

    try {
        const resposta = await fetch(`${API}?acao=salvar`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                nome,
                email,
                senha
            })
        });

        const dados = await resposta.json();

        if (dados.sucesso) {

            mensagem.className = 'mensagem mensagem-sucesso';
            mensagem.textContent = dados.mensagem;

            formulario.reset();

            setTimeout(() => {
                window.location.href = 'login.html';
            }, 1200);

            return;
        }

        mensagem.className = 'mensagem mensagem-erro';
        mensagem.textContent =
            dados.erro || 'Não foi possível concluir o cadastro.';

    } catch (erro) {

        console.error(erro);

        mensagem.className = 'mensagem mensagem-erro';
        mensagem.textContent =
            'Não foi possível estabelecer comunicação com o servidor.';
    }
});