# Desafio técnico desenvolvedor backend

##Objetivo

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
