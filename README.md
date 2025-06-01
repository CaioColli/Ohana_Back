# Ohana Travel

OhanaTravel é um projeto fullstack que oferece uma API completa para gerenciamento de reservas — desde quartos de hotéis e passagens aéreas até aluguel de veículos.

Na aplicação, o usuário pode criar uma conta para gerenciar suas reservas e participar de um clube fictício chamado OhanaClub, onde recebe cupons de desconto para reservas e aluguéis de veículos.

* A API é robusta e conta com:
* Autenticação via JWT Token
* Criptografia de senhas e tokens
* Sistema de reset de senha
* Confirmação de e-mail através de envio automático pelo sistema

Além disso, qualquer usuário pode cadastrar seus próprios serviços na plataforma, que serão analisados e aprovados (ou recusados) por um administrador. O sistema também possui funcionalidades de avaliação e comentários para os serviços oferecidos.

## Rodando localmente

Clone o projeto

```bash
 git clone git@github.com:CaioColli/Ohana_Back.git
```

Entre no diretório do projeto

```bash
 cd Ohana_Back
```

Instale as dependências

```bash
 composer install
```

Inicie o servidor

```bash
 php -S 0.0.0.0:8000 -t ./public/
```

## Instalando banco de dados

para ter total experiência da aplicação crie em seu editor MySQL as tabelas fornecidas na pasta `public/database`

## Uso/Exemplos

Na pasta `/public` crie um arquivo `.env` e insira os códigos a seguir:

Email usuado para disparar email
```
 MAIL_USERNAME="Email"
 MAIL_PASSWORD="Senha"
```

Configuração do banco de dados
```
 DB_HOST="localhost"
 DB_PORT="3306"
 DB_DATABASE="NomeDoBanco"
 DB_USER="Usuário"
 DB_PASSWORD="SenhaDoBanco"
```
## Documentação da API

#### Usuário

| Endpoint  | Método | Descrição | Obs |
| :---------- | :--------- | :---------------------------------- | :--- |
| `/user/cadaster` | `post` | Cadastra um novo usuário | `null` |
| `/user/login` | `post` | Efetua login de um usuário | `null` |
| `/user/logout` | `post` | Efetura o logout do usuário | **Necessário token autenticação**  |
| `/user/verify_email` | `post` | Manda email de verificação | **Necessário token autenticação** |
| `/user/verify_email/confirm` | `post` | Confirma email do usuário | `null` |
| `/user/reset` | `post` |  Manda código para mudar senha de usuário | `null` |
| `/user/reset/change_password` | `post` |  Muda senha do usuário | `null` |
| `/user/edit` | `patch` | Edição de perfil | **Necessário token autenticação** |
| `/user/edit/image` | `post` | Edição foto perfil | **Necessário token autenticação** |
| `/user/delete` | `delete` | Apaga conta do usuário | **Necessário token autenticação** |
| `/user` | `get` |  Retorna dados do usuário | **Necessário token autenticação** |

## Autores

- [@GitHub - Caio Colli](https://github.com/CaioColli)
- [@Linkedin - Caio Colli](https://www.linkedin.com/in/caiocolli/)

## Stack utilizada

**Back-end:** PHP, SlimFramework

**Bibliotecas usadas** Respect validation, Ramsey UUID, PHPMailer, vlucas phpdotenv.

**Banco de dados:** MySQL

## Feedback

Se você tiver algum feedback, por favor nos deixe saber por meio de CaioColliDev@gmail.com
