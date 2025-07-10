# desafio-backend-Oner

Projeto backend desenvolvido com Laravel para simular operações financeiras entre usuários, no contexto de um desafio técnico.

## Tecnologias utilizadas

- **PHP** (Laravel)
- **Laravel Sanctum** para autenticação de API
- **MySQL** (ou outro compatível) - gerenciamento das tabelas via migrations
- **Composer** para gerenciamento de dependências PHP

## Como rodar o projeto

### Modo tradicional

1. Instale as dependências:
   ```bash
   composer install
   ```

2. Configure o `.env` com as credenciais do banco de dados e serviços externos.

3. Rode as migrations e gere a key:
   ```bash
   php artisan key:generate
   php artisan migrate
   ```

4. Inicie o servidor:
   ```bash
   php artisan serve
   ```

### Modo Docker

1. Renomeie o arquivo `.env.example` para `.env` e ajuste as variáveis de ambiente conforme necessário.

2. Suba os containers:
   ```bash
   docker-compose up -d
   ```

3. Acesse o container da aplicação:
   ```bash
   docker exec -it <nome_do_container_app> bash
   ```

4. Rode as migrations dentro do container:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate 
   ```

5. O serviço estará disponível em: [http://localhost:8000](http://localhost:8000) (ajuste conforme mapeamento de portas no `docker-compose.yml`).

## Endpoints principais

- `POST /api/register`: cadastro de usuário
- `POST /api/login`: login do usuário
- `POST /api/logout`: logout (necessário token)
- `POST /api/transfer`: transferência de valores (token)
- `POST /api/refund`: reembolso de transferência (token)
- `POST /api/withdraw`: saque da carteira (token)
- `POST /api/deposit`: depósito na carteira (token)


## POST /api/register

**Request Body:**
```json
{
  "name": "User Name",
  "email": "user@example.com",
  "password": "password",
  "document": "12345678900",
  "type": "customer" // customer ou shopkeeper
}

```

**Response (201 OK):**

```json

{
	"response_code": 201,
	"status": "success",
	"message": "Successfully registered",
	"user": {
		"name": "User Name",
		"email": "user@example.com",
		"type": "customer"
	}
}
```

## POST /api/login

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password",
}

```

**Response (200 OK):**

```json

{
	"response_code": 200,
	"status": "success",
	"message": "Login successful",
	"user_info": {
		"id": 2,
		"name": "User Name",
		"email": "user@example.com",
		"type": "customer"
	},
	"token": "4|60y1Xds8mZg2PbIkUW51HYQ7ch8ocy35lFGxxIhM87c69cdb",
	"token_type": "Bearer"
}
```


## POST /api/logout

**Header:**
*Bearer Token*

**Response (200 OK):**

```json
{
	"response_code": 200,
	"status": "success",
	"message": "Successfully logged out"
}

```

## POST /api/transfer

**Header:**
*Bearer Token*

**Request Body:**
```json
{
  "payee_id": 2,
  "amount": "10"
}
```

**Response (200 OK):**

```json
{
	"response_code": 200,
	"status": "success",
	"message": "Transfer completed successfully",
	"data": {
		"payer_id": 1,
		"payee_id": 2,
		"type": "transfer",
		"amount": "10",
		"status": "approved",
		"updated_at": "2025-07-10T08:04:38.000000Z",
		"created_at": "2025-07-10T08:04:38.000000Z",
		"id": 12
	}
}
```

## POST /api/withdraw

**Header:**
*Bearer Token*

**Request Body:**
```json
{
  "amount": "100"
}
```

**Response (200 OK):**

```json
{
	"response_code": 200,
	"status": "success",
	"message": "Withdraw completed successfully",
	"data": {
		"payer_id": 3,
		"payee_id": 3,
		"type": "withdraw",
		"amount": "100",
		"status": "approved",
		"updated_at": "2025-07-10T07:44:14.000000Z",
		"created_at": "2025-07-10T07:44:14.000000Z",
		"id": 24,
		"payer": {
			"id": 3,
			"name": "User name",
			"email": "user@example.com",
			"cpf_cnpj": "12345678900",
			"type": "customer",
			"balance": "399.32",
			"created_at": "2025-07-10T07:30:31.000000Z",
			"updated_at": "2025-07-10T07:44:14.000000Z"
		}
	}
}
```

## POST /api/deposit

**Header:**
*Bearer Token*

**Request Body:**
```json
{
  "amount": "0.22"
}
```

**Response (200 OK):**

```json
{
	"response_code": 200,
	"status": "success",
	"message": "Deposit completed successfully",
	"data": {
		"payer_id": 2,
		"payee_id": 2,
		"type": "deposit",
		"amount": "0.22",
		"status": "approved",
		"updated_at": "2025-07-10T08:04:19.000000Z",
		"created_at": "2025-07-10T08:04:19.000000Z",
		"id": 11,
		"payer": {
			"id": 2,
			"name": "User Name",
			"email": "user@example.com",
			"cpf_cnpj": "123345678900",
			"type": "costumer",
			"balance": "40.44",
			"created_at": "2025-07-10T07:58:33.000000Z",
			"updated_at": "2025-07-10T08:04:19.000000Z"
		}
	}
}
```

## POST /api/refund

**Header:**
*Bearer Token*

**Request Body:**
```json
{
  "transaction_id": "1"
}
```

**Response (200 OK):**

```json
{
	"response_code": 200,
	"status": "success",
	"message": "Refund completed successfully",
	"data": {
		"payer_id": 2,
		"payee_id": 1,
		"type": "refund",
		"amount": "10.00",
		"status": "approved",
		"updated_at": "2025-07-10T08:05:49.000000Z",
		"created_at": "2025-07-10T08:05:49.000000Z",
		"id": 15
	}
}
```
> Todas as rotas (exceto register e login) exigem autenticação via token.

## Testes

Execute os testes com:
```bash
php artisan test
```