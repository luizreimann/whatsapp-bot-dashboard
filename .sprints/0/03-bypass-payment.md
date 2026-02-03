# Como Fazer Bypass do Bloqueio de Pagamento

## 🔒 Sistema de Bloqueio Implementado

O sistema agora possui um middleware `RequiresPaidSubscription` que bloqueia o acesso à plataforma para usuários que:
- Não possuem assinatura
- Possuem assinatura com status diferente de `active` (pending, suspended, expired, canceled)

### Middleware Aplicado em:
- Todas as rotas do dashboard (`/dashboard/*`)
- Rotas de integração
- Rotas de leads
- Rotas de bot

### Rotas Liberadas (sem bloqueio):
- Login e Registro
- Checkout (para permitir pagamento)
- Webhooks do Stripe
- Rotas públicas

---

## 🛠️ Como Fazer Bypass via Banco de Dados

### Método 1: Ativar Assinatura Manualmente (Recomendado)

```sql
-- 1. Encontre o tenant/usuário
SELECT 
    t.id as tenant_id,
    t.name as tenant_name,
    u.email,
    s.id as subscription_id,
    s.status
FROM tenants t
JOIN users u ON u.tenant_id = t.id
LEFT JOIN subscriptions s ON s.tenant_id = t.id
WHERE u.email = 'email@exemplo.com';

-- 2. Ative a assinatura
UPDATE subscriptions 
SET status = 'active',
    current_period_start = NOW(),
    current_period_end = DATE_ADD(NOW(), INTERVAL 1 MONTH)
WHERE tenant_id = [TENANT_ID];

-- 3. Ative o tenant
UPDATE tenants 
SET status = 'active' 
WHERE id = [TENANT_ID];
```

### Método 2: Via Laravel Tinker (Mais Seguro)

```bash
php artisan tinker
```

```php
// Encontrar usuário
$user = User::where('email', 'email@exemplo.com')->first();

// Ativar assinatura
$subscription = $user->tenant->subscription;
$subscription->update([
    'status' => 'active',
    'current_period_start' => now(),
    'current_period_end' => now()->addMonth(),
]);

// Ativar tenant
$user->tenant->update(['status' => 'active']);

// Verificar
$user->tenant->subscription->isActive(); // deve retornar true
```

### Método 3: Criar Assinatura Ativa do Zero

```php
php artisan tinker
```

```php
$user = User::where('email', 'email@exemplo.com')->first();

Subscription::create([
    'tenant_id' => $user->tenant_id,
    'status' => 'active',
    'amount' => 297.00,
    'currency' => 'BRL',
    'billing_cycle' => 'monthly',
    'current_period_start' => now(),
    'current_period_end' => now()->addMonth(),
    'payment_method' => 'manual',
]);

$user->tenant->update(['status' => 'active']);
```

---

## 🧪 Testando o Bypass

### 1. Verificar Status Atual

```bash
php artisan tinker
```

```php
$user = User::where('email', 'email@exemplo.com')->first();
$subscription = $user->tenant->subscription;

echo "Tenant Status: " . $user->tenant->status . "\n";
echo "Subscription Status: " . $subscription->status . "\n";
echo "Is Active: " . ($subscription->isActive() ? 'Yes' : 'No') . "\n";
echo "Period: " . $subscription->current_period_start . " to " . $subscription->current_period_end . "\n";
```

### 2. Testar Login

1. Acesse `/login`
2. Faça login com o email que você ativou
3. Você deve ser redirecionado para `/dashboard` sem bloqueios

---

## 📋 Status de Assinatura Possíveis

| Status | Descrição | Acesso Permitido? |
|--------|-----------|-------------------|
| `active` | Assinatura ativa e paga | ✅ Sim |
| `pending` | Aguardando pagamento | ❌ Não |
| `suspended` | Suspensa por inadimplência | ❌ Não |
| `expired` | Período expirado | ❌ Não |
| `canceled` | Cancelada pelo usuário | ❌ Não |

---

## 🔧 Comandos Úteis

### Listar Todos os Tenants e Status

```sql
SELECT 
    t.id,
    t.name,
    t.status as tenant_status,
    s.status as subscription_status,
    s.current_period_end,
    u.email
FROM tenants t
LEFT JOIN subscriptions s ON s.tenant_id = t.id
LEFT JOIN users u ON u.tenant_id = t.id
ORDER BY t.created_at DESC;
```

### Ativar Todos os Tenants Pending (Cuidado!)

```sql
-- Apenas para ambiente de desenvolvimento/testes
UPDATE subscriptions SET status = 'active' WHERE status = 'pending';
UPDATE tenants SET status = 'active' WHERE status = 'pending';
```

### Resetar Período de Assinatura

```sql
UPDATE subscriptions 
SET current_period_start = NOW(),
    current_period_end = DATE_ADD(NOW(), INTERVAL 1 MONTH)
WHERE tenant_id = [TENANT_ID];
```

---

## ⚠️ Avisos Importantes

1. **Ambiente de Produção**: Nunca faça bypass em produção sem documentar o motivo
2. **Auditoria**: Considere criar um log de ativações manuais
3. **Pagamentos**: Lembre-se que o bypass não cria registros de pagamento
4. **Stripe**: O bypass local não sincroniza com o Stripe

---

## 🎯 Fluxo Normal (Sem Bypass)

1. Usuário se cadastra → `tenant.status = pending`, `subscription.status = pending`
2. Usuário faz login → Redirecionado para `/checkout`
3. Usuário paga via Stripe → Webhook ativa assinatura
4. Sistema atualiza → `subscription.status = active`, `tenant.status = active`
5. Usuário acessa dashboard → Acesso liberado ✅

---

## 📝 Exemplo Completo de Bypass

```bash
# 1. Conectar ao banco
php artisan tinker

# 2. Executar
$email = 'teste@exemplo.com';
$user = User::where('email', $email)->first();

if ($user) {
    // Ativar assinatura
    $sub = $user->tenant->subscription;
    $sub->update([
        'status' => 'active',
        'current_period_start' => now(),
        'current_period_end' => now()->addMonth(),
    ]);
    
    // Ativar tenant
    $user->tenant->update(['status' => 'active']);
    
    echo "✅ Bypass aplicado com sucesso!\n";
    echo "Email: {$user->email}\n";
    echo "Tenant: {$user->tenant->name}\n";
    echo "Status: {$sub->status}\n";
} else {
    echo "❌ Usuário não encontrado\n";
}
```

---

**Criado em:** 03/02/2026  
**Última atualização:** 03/02/2026
