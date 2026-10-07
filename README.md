# 🛒 Mercadinho do Seu Zé

Projeto desenvolvido **para a disciplina de Programação Web I**, com o objetivo de criar uma aplicação web para gerenciamento de usuários de um pequeno mercado.

O sistema possui autenticação, controle de acesso por perfil (**Administrador e Funcionário**) e gerenciamento de usuários, incluindo cadastro, edição e desativação.

---

## 🚀 Tecnologias utilizadas

| Tecnologia | Utilização |
|---|---|
| 🐘 **PHP** | Backend e regras de negócio |
| 🗄️ **MySQL** | Banco de dados |
| 🌐 **HTML5** | Estrutura das páginas |
| 🎨 **CSS3** | Estilização da interface |
| ⚡ **JavaScript** | Interações e comunicação com a API |
| 🔌 **PDO** | Conexão com o banco de dados |
| 🔗 **API REST** | Comunicação entre frontend e backend |
| 🔐 **Password Hash** | Proteção das senhas |

---

## 🔐 Segurança

As senhas dos usuários não são armazenadas diretamente no banco de dados.

O projeto utiliza:

- `password_hash()` para gerar o hash da senha;
- `password_verify()` para validar a senha durante o login;
- Controle de acesso através dos perfis **ADMIN** e **FUNCIONARIO**.

---

## 📚 Disciplina

**Programação Web I**

Projeto acadêmico desenvolvido como atividade prática da disciplina.