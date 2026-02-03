# Como Configurar o Stripe

## 🔑 Erro Atual

```
Stripe\Exception\InvalidArgumentException
$config must be a string or an array
```

**Causa:** As variáveis `STRIPE_KEY` e `STRIPE_SECRET` não estão configuradas no arquivo `.env`

---

## ✅ Solução Rápida

### 1. Copiar `.env.example` para `.env` (se ainda não fez)

```bash
cp .env.example .env
```

### 2. Adicionar as Chaves do Stripe no `.env`

Abra o arquivo `.env` e adicione/atualize:

```env
# Stripe Payment Gateway
STRIPE_KEY=pk_test_51...
STRIPE_SECRET=sk_test_51...
STRIPE_WEBHOOK_SECRET=whsec_...
```

---

## 🧪 Para Testes (Modo Test)

### Opção 1: Usar Chaves de Teste do Stripe

1. Acesse: https://dashboard.stripe.com/test/apikeys
2. Copie as chaves de teste
3. Cole no `.env`:

```env
STRIPE_KEY=pk_test_51HqL...
STRIPE_SECRET=sk_test_51HqL...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### Opção 2: Usar Valores Fake (Apenas para Desenvolvimento Local)

**⚠️ ATENÇÃO:** Isso NÃO funcionará com pagamentos reais, apenas para testar a interface!

```env
STRIPE_KEY=pk_test_fake_key_for_development
STRIPE_SECRET=sk_test_fake_key_for_development
STRIPE_WEBHOOK_SECRET=whsec_fake_webhook_secret
```

Com valores fake, o checkout vai carregar mas falhará ao tentar processar pagamento.

---

## 🎯 Configuração Completa (Recomendado)

### 1. Criar Conta no Stripe

1. Acesse: https://dashboard.stripe.com/register
2. Crie uma conta gratuita
3. Ative o modo de teste

### 2. Obter Chaves de API

1. Acesse: https://dashboard.stripe.com/test/apikeys
2. Copie:
   - **Publishable key** (começa com `pk_test_`)
   - **Secret key** (começa com `sk_test_`)

### 3. Configurar Webhook (Opcional para testes)

1. Acesse: https://dashboard.stripe.com/test/webhooks
2. Clique em "Add endpoint"
3. URL: `https://seu-dominio.com/webhooks/stripe`
4. Eventos a ouvir:
   - `checkout.session.completed`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
5. Copie o **Signing secret** (começa com `whsec_`)

### 4. Atualizar `.env`

```env
STRIPE_KEY=pk_test_51HqL8xKj...
STRIPE_SECRET=sk_test_51HqL8xKj...
STRIPE_WEBHOOK_SECRET=whsec_1234567890...
```

### 5. Limpar Cache do Laravel

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🧪 Testar Configuração

### Via Tinker

```bash
php artisan tinker
```

```php
config('services.stripe.key');
// Deve retornar: "pk_test_..."

config('services.stripe.secret');
// Deve retornar: "sk_test_..."
```

### Via Navegador

1. Acesse: `http://localhost:8080/checkout/1`
2. Se configurado corretamente, verá o formulário de cartão do Stripe
3. Se não configurado, verá mensagem de erro

---

## 💳 Cartões de Teste do Stripe

Quando usar chaves de teste, use estes cartões:

| Cenário | Número do Cartão | CVC | Data |
|---------|------------------|-----|------|
| ✅ Sucesso | 4242 4242 4242 4242 | Qualquer | Futuro |
| ❌ Falha | 4000 0000 0000 0002 | Qualquer | Futuro |
| 🔐 3D Secure | 4000 0025 0000 3155 | Qualquer | Futuro |

**Qualquer CVC:** 123, 456, 789, etc.  
**Qualquer data futura:** 12/25, 01/26, etc.

---

## 🚀 Após Configurar

1. Recarregue a página de checkout
2. O formulário de cartão do Stripe deve aparecer
3. Use um cartão de teste para simular pagamento
4. Verifique se a assinatura é ativada

---

## 🔍 Verificar se Funcionou

```bash
php artisan tinker
```

```php
// Verificar configuração
dump(config('services.stripe'));

// Testar conexão (apenas com chaves reais)
$stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
$stripe->balance->retrieve();
```

---

## ❌ Problemas Comuns

### 1. "Configuração do Stripe não encontrada"
- ✅ Verifique se o `.env` tem as variáveis
- ✅ Execute `php artisan config:clear`

### 2. "Invalid API Key"
- ✅ Verifique se copiou a chave completa
- ✅ Certifique-se de usar chaves de **test** (não production)

### 3. "No such customer"
- ✅ Normal em ambiente de teste
- ✅ Cada pagamento cria um novo customer

---

**Criado em:** 03/02/2026  
**Última atualização:** 03/02/2026
