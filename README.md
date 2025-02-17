# Desafio PHP Backend

## Requisitos

-   [PHP 7.4](https://www.php.net/)
-   [Composer](https://getcomposer.org/)
-   [Extensão de PDO MySQL e SQLite](https://www.php.net/manual/en/pdo.installation.php) para o banco escolhido

## Arquitetura

[Explicação da Arquitetura comentada](https://www.loom.com/share/21ae653c6f1e41ba838e34bb953c8f5d)

https://www.loom.com/share/21ae653c6f1e41ba838e34bb953c8f5d

![Arquitetura](./docs/assets/diagrama.png?raw=true)


## Setup

### Com Docker

Para facilitar o ambiente de execução do projeto, pode ser levantado o ambiente com docker compose, siga os passos:

-   Copiar o `.env.example`, renomear a cópia para `.env` e parametrizar conforme necessidade.
-   Rodar o comando `docker-compose up -d --build`.
-   Acessar o container `docker exec -it ${nome-do-servico} bash`.
-   Navegar até /var/www `cd /var/www`.
-   Instalar depêndencias: `composer install`.
-   Rodar migrations: `php artisan migrate`.
-   Rodar seeders: `php artisan db:seed`. O comando cria uma conta inicial com um saldo R$ 500,00.
-   Acessar `http://localhost:8080`

### Setup Manual

-   Instalar depêndencias: `composer install`.
-   Copiar o `.env.example` e renomear para `.env`.
-   Copiar o `.env.example`, renomear a cópia para `.env` e parametrizar conforme necessidade.
-   Rodar migrations: `php artisan migrate`.
-   Rodar seeders: `php artisan db:seed`. O comando cria uma conta inicial com um saldo R$ 500,00.
-   Levantar um servidor para rodar o projeto: `php -S localhost:8080 -t public`

### Considerações do Setup

Para começar a usar o projeto serão criadas duas contas com o objetivo de facilitar o setup e já existir contas para transferência. As contas criadas possuem os seguintes dados:

Conta Lojista

```json
{
    "id": "8b04b926-2d92-4977-ab57-82a6f03ba39c",
    "cpfcnpj": "97114152000157",
    "titular": "Conta Lojista",
    "email": "conta.lojista@youpay.com.br",
    "senha": "conta.master",
    "celular": "27988887777",
    "saldo": 0
}
```

Conta Comum

```json
{
    "id": "f4ca258a-3e68-4a83-984d-02a7c8bab5c7",
    "cpfcnpj": "71961965038",
    "titular": "Conta Comum",
    "email": "conta.comum@youpay.com.br",
    "senha": "conta.comum",
    "celular": "27988887654",
    "saldo": 500.0
}
```

Agora com as contas exemplos é possível fazer transferência e também é possível criar novas contas, para isso, consulte a documentação da api feita usando [OpenAPI Specification - swagger](https://swagger.io/specification/). Para acessar a documentação, na sua própria instalação acesse o endereço: `http://localhost:8080/api-docs/index.html`.


## Regra de Negócio e Premissas

O negócio principal deste desafio é bem simplificado limitando-se APENAS em transferência de recursos entre conta. Para isso, é necessário que tenha um cadastro de contas, autenticação e transferencia de recursos.

- Existem dois grupos/perfils de contas, sendo conta comum e conta do lojista.
- Todo cadastro com CPF é considerado automaticamente conta comum.
- Todo cadastro com CNPJ é considerado automaticamente conta do lojista.
- Não pode haver mais de uma conta com CPF/CNPJ ou e-mail.
- Apenas usuários comuns podem transferir dinheiro, contas do perfil de logista não transferem dinheiro por este serviço, apenas recebem.
- Contas comuns podem enviar e receber dinheiro.
- Antes de finalizar a transferência, deve-se consultar um serviço autorizador externo, use este mock para simular (https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6).
- A operação de transferência deve ser uma transação (ou seja, revertida em qualquer caso de inconsistência) e o dinheiro deve voltar para a carteira do usuário que envia.
- No recebimento de pagamento, o usuário ou lojista precisa receber notificação enviada por um serviço de terceiro e eventualmente este serviço pode estar indisponível/instável. Use este mock para simular o envio (https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04).
- Este serviço deve ser RESTFul

**Importante**

Ao consumir o serviço de notificação externa após ter efetivado transferência, como forma de acompanhar, será gravado um log em texto no diretório `./storage/logs/lumen-${date}.log`. É uma forma simplificada de acompanhar como a mensagem é enviada para serviço de notificação.

## Testes

Este projeto usa os recursos do framework [Lumen](https://lumen.laravel.com/) para rodar testes, o [Lumen](https://lumen.laravel.com/) por sua vez usa [PHPUnit](https://phpunit.de/) como framework de testes. O projeto tem cobertua de testes unitário e testes de integração (api).

Os testes de integração que usam banco de dados necessita da extensão PDO SQLite, então certique que atenda os requisitos.

Para rodar os testes execute pelo menos um comando das alternativas abaixo:

Unix:

`./vendor/bin/phpunit` ou `composer run tests`

Windows:

`.\vendor\bin\phpunit` ou `composer run tests-windows`

Para efetivar testes de integração o banco de dado utilizado é banco SQLite em memória. A configuração dos testes está parametrizada no arquivo `.env.testing`.

## Extras

Para testar a API de forma visual, pode ser feito tanto pelo swagger `http://localhost:8080/api-docs/index.html` ou pelo [insomnia](https://insomnia.rest/products/insomnia). Caso faça pelo [insomnia](https://insomnia.rest/products/insomnia), no projeto já existe um [arquivo](./Endpoints-Insomnia.json) base que pode ser importado na sua instalação.

Apesar de haver ambas opções de teste visual da API, o teste pode ser feito com qualquer client rest.
