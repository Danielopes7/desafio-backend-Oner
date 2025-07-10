# desafio-backend-Oner

Projeto backend desenvolvido com Laravel para simular opera��es financeiras entre usu�rios, no contexto de um desafio t�cnico.

## Tecnologias utilizadas

- **PHP** (Laravel)
- **Laravel Sanctum** para autentica��o de API
- **MySQL** (ou outro compat�vel) - gerenciamento das tabelas via migrations
- **Composer** para gerenciamento de depend�ncias PHP

## Como rodar o projeto

### Modo tradicional

1. Instale as depend�ncias:
   ```bash
   composer install
   ```

2. Configure o `.env` com as credenciais do banco de dados e servi�os externos.

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

1. Renomeie o arquivo `.env.example` para `.env` e ajuste as vari�veis de ambiente conforme necess�rio.

2. Suba os containers:
   ```bash
   docker-compose up -d
   ```

3. Acesse o container da aplica��o:
   ```bash
   docker exec -it <nome_do_container_app> bash
   ```

4. Rode as migrations dentro do container:
   ```bash
   php artisan migrate 
   ```

5. O servi�o estar� dispon�vel em: [http://localhost:8000](http://localhost:8000) (ajuste conforme mapeamento de portas no `docker-compose.yml`).

## Endpoints principais

- `POST /api/register`: cadastro de usu�rio
- `POST /api/login`: login do usu�rio
- `POST /api/logout`: logout (necess�rio token)
- `POST /api/transfer`: transfer�ncia de valores (token)
- `POST /api/refund`: reembolso de transfer�ncia (token)
- `POST /api/withdraw`: saque da carteira (token)
- `POST /api/deposit`: dep�sito na carteira (token)

> Todas as rotas (exceto register e login) exigem autentica��o via token.

## Testes

Execute os testes com:
```bash
php artisan test
```