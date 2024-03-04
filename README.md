# Desafio técnico desenvolvedor backend

## Objetivo

Criar uma API Rest, utilizando o Laravel, que permita a Loja ABC LTDA, vender seus produtos 
de diferentes nichos, registrando a venda de celulares.

O retorno dos dados dos produtos é no seguinte formato:

```json
[
    {
        "name": "Celular 1",
        "price": 1.800,
        "description": "Lorenzo Ipsulum"
    },
    {
        "name": "Celular 2",
        "price": 3.200,
        "description": "Lorem ipsum dolor"
    },
    {
        "name": "Celular 3",
        "price": 9.800,
        "description": "Lorem ipsum dolor sit amet"
    }
]
```

O registro da venda dos celulares, será cadastrado e retornado no seguinte formato:

```json
{
  "sales_id": "202301011",
  "amount": 8200,
  "products": [
    {
      "product_id": 1,
      "nome": "Celular 1",
      "price": 1.800,
      "amount": 1
    },
    {
      "product_id": 2,
      "nome": "Celular 2",
      "price": 3.200,
      "amount": 2
    },
  ]
}
```

A API possui os seguintes endpoints:

- Listar produtos disponíveis
- Cadastrar nova venda
- Consultar vendas realizadas
- Consultar uma venda específica
- Cancelar uma venda
- Cadastrar novas produtos a uma venda


## Instalação

Clone do projeto:

```sh
$ git clone git@github.com:juniorari/adoorei-store.git
```

Acessar a pasta do projeto:

```sh
$ cd adoorei-store
```

Criar e subir os containers:

```sh
$ docker-compose up --build -d
```

Baixar as dependências do composer
```sh
$ docker exec -it adoorei_app composer install -vvv
```

Copiar o arquivo `.env`

```sh
$ cp .env.example .env
```

Executar as migrations, no banco principal e no banco de teste.

```sh
$ docker exec -it adoorei_app php artisan migrate
$ docker exec -it adoorei_app php artisan migrate --database=dbtest
```

Executar o seeder para criar alguns produtos

```sh
$ docker exec -it adoorei_app php artisan db:seed ProductSeeder
```

##### Executando os testes

Para rodar os testes, execute o seguinte comando:

```sh
$ docker exec -it adoorei_app php artisan test
```
O projeto está rodando em [http://localhost:8080/](http://localhost:8080/).

Foi disponibilizada uma [API no Postman](https://documenter.getpostman.com/view/2889430/2sA2xb7wLb#bc1a78d6-acac-474e-ab90-a36d28f5531a) para acesso aos endpoints
OBS: Não esquecer de alterar a variável `{{base_url}}` para apontar ao projeto local
 

