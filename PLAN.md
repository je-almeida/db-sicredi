# Plano de Ação - Projeto AWS + Laravel - Customers & Balance

## Objetivo
Criar MVP funcional para demonstrar Laravel + SQS + Lambda + PHPUnit + AWS.

## Sequência de trabalho

1. **Setup Laravel**
   - Criar projeto Laravel mínimo
   - Configurar DB (MySQL ou SQLite)
   - Criar tabelas: 
     - `customers` → id, name, balance
     - `operations` → id, customer_id, type, amount, status, result

2. **CRUD Customers**
   - `GET /customers` → lista customers + saldo
   - `POST /customers` → criar customer
   - `PUT /customers/{id}` → atualizar
   - `DELETE /customers/{id}` → remover

3. **Operações**
   - `POST /operations` → insere operação na fila SQS
   - `GET /operations/{id}` → retorna status + resultado

4. **Job Laravel**
   - Criar Job `ProcessOperationJob` que envia mensagens para SQS

5. **AWS Setup**
   - Criar conta Free Tier
   - Criar **SQS Queue**
   - Criar **Lambda** com trigger SQS
   - Lambda processa operação, atualiza DB Laravel via HTTP request
   - Simular delay de 5 segundos por operação

6. **Frontend**
   - Tela **Customers com saldo**
   - Tela **Operações com status**
   - Polling 5–10s para atualizar status dinamicamente

7. **Testes**
   - PHPUnit tests no Laravel
   - Mocks para SQS e Lambda
   - Testar endpoints CRUD + envio e consumo de operações

8. **Deploy**
   - Laravel: Elastic Beanstalk ou EC2 t2.micro
   - Testar URL pública
   - Garantir Free Tier e custo zero
