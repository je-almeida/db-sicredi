# Projeto especial para empresa DB

# Laravel + AWS Queue + Lambda - Customers & Balance Demo

## Descrição
Aplicação demonstrativa para entrevista, implementando:

- Laravel API com **CRUD de Customers** e gerenciamento de saldo.
- Endpoint para **enviar operações de withdraw/deposit** para fila AWS SQS.
- Lambda Function consumindo a fila SQS e processando operações com delay simulado de 5 segundos.
- Status das operações: `pending`, `processing`, `done`, `fail`.
- Tela para visualizar **customers e balance** e outra para **visualizar operações**.
- Testes unitários com PHPUnit e mocks para SQS e Lambda.

## Funcionalidades

1. **CRUD de Customers**
   - Endpoints REST: `GET /customers`, `POST /customers`, `PUT /customers/{id}`, `DELETE /customers/{id}`

2. **Operações**
   - Endpoint: `POST /operations`
     - Dados: `customer_id`, `type` (withdraw/deposit), `amount`
     - Insere operação na fila SQS
   - Endpoint: `GET /operations/{id}` → retorna status e resultado

3. **Lambda**
   - Trigger: SQS
   - Processa operação (aplica deposit/withdraw no saldo do customer)
   - Simula **delay de 5s**
   - Atualiza status da operação (`done` ou `fail`)

4. **Frontend simples**
   - Tela de **Customers com saldo**
   - Tela de **Operações com status**
   - Polling a cada 5–10s para atualizar status

## Estrutura do Monorepo

