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

> Todas as rotas (exceto register e login) exigem autenticação via token.

## Testes

Execute os testes com:
```bash
php artisan test
```