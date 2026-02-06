# 02 - MVP: Escopo e Roadmap de Lançamento

**Data de criação:** 03/02/2026  
**Versão:** 1.1  
**Objetivo:** Definir o escopo mínimo viável para lançamento do Zaptria

---

## 📋 Índice

1. [Visão Geral do MVP](#visão-geral-do-mvp)
2. [Status Atual vs MVP](#status-atual-vs-mvp)
3. [Funcionalidades Core do MVP](#funcionalidades-core-do-mvp)
4. [Painel Admin e Sistema de Pagamentos](#painel-admin-e-sistema-de-pagamentos)
5. [Integrações do MVP](#integrações-do-mvp)
6. [Roadmap de Desenvolvimento](#roadmap-de-desenvolvimento)
7. [Critérios de Aceitação](#critérios-de-aceitação)
8. [Pós-MVP](#pós-mvp)

---

## 🎯 Visão Geral do MVP

### Proposta de Valor
O **Zaptria MVP** é um SaaS multi-tenant que permite empresas automatizarem conversas no WhatsApp através de fluxos visuais, capturarem leads qualificados e sincronizarem automaticamente com suas ferramentas de CRM, marketing e vendas.

### Diferencial
- **Flow Builder Visual:** Criação de fluxos conversacionais sem código
- **Multi-integração:** 14 integrações nativas no lançamento
- **Multi-tenant:** Isolamento completo de dados por cliente
- **WhatsApp Nativo:** Conexão real via WhatsApp Web

### Público-Alvo MVP
- Pequenas e médias empresas
- Agências de marketing digital
- E-commerces
- Empresas de serviços (consultoria, educação, saúde)

---

## 📊 Status Atual vs MVP

### ✅ Já Implementado (90% do MVP)

#### Infraestrutura
- [x] Arquitetura multi-tenant
- [x] Autenticação e sessões
- [x] Docker + PostgreSQL 16
- [x] API de webhooks para bot WhatsApp
- [x] Sistema de queue (database driver)

#### Dashboard
- [x] Interface principal
- [x] Theme toggle (dark/light)
- [x] Navegação e layout responsivo

#### Gerenciamento de Leads
- [x] CRUD completo de leads
- [x] Filtros avançados (fluxo, status, data)
- [x] Ordenação de colunas
- [x] Visualização de detalhes
- [x] Edição inline de notas
- [x] Paginação e AJAX

#### Bot WhatsApp
- [x] Modelo de dados (WhatsappInstance)
- [x] Recebimento de QR Code
- [x] Atualização de status (connected, disconnected)
- [x] Recebimento de mensagens (estrutura)
- [x] Autenticação via bot token

#### Sistema de Integrações
- [x] Arquitetura extensível (Registry + Contracts)
- [x] RD Station CRM (completo)
- [x] Pipedrive (completo)
- [x] Interface de conexão/desconexão
- [x] Teste de credenciais

#### Painel Admin e Pagamentos (Sprint 0) ✅
- [x] Checkout transparente com Stripe Elements
- [x] Assinaturas recorrentes (R$ 297/mês)
- [x] Bloqueio de acesso sem pagamento
- [x] Painel administrativo completo
- [x] Provisionamento automático após pagamento
- [x] Suspensão por inadimplência

#### Flow Builder Visual (Sprint 1) ✅
- [x] Interface visual drag & drop com React Flow
- [x] 18 tipos de nós implementados
- [x] Validação de fluxos
- [x] CRUD completo de fluxos
- [x] Ativar/desativar fluxos

#### Onboarding em 3 Etapas (Sprint 1.5) ✅
- [x] Cadastro multi-step (Dados Pessoais → Empresa → Checkout)
- [x] Tabela `companies` (dados opcionais da empresa)
- [x] Campos `phone`, `document`, `document_type` em `users`
- [x] Validação de CPF/CNPJ (Rules customizadas)
- [x] Layout `onboarding` com stepper visual
- [x] Máscaras de input vanilla JS (CPF, CNPJ, telefone, CEP)
- [x] Busca de CEP via ViaCEP (auto-preenchimento)

### 🔄 Pendente para MVP (10%)

#### Engine de Execução (CRÍTICO — Sprint 2) ← PRÓXIMA
- [ ] Máquina de estados para conversas
- [ ] Sessões de conversa com contexto
- [ ] Processamento de mensagens recebidas
- [ ] Geração de respostas baseadas no fluxo
- [ ] Transições entre nós
- [ ] Timeout e expiração de sessão

#### Gerenciamento de Fluxos
- [ ] CRUD completo (criar, editar, duplicar, deletar)
- [ ] Ativar/desativar fluxos
- [ ] Listagem de fluxos
- [ ] Atribuir fluxo a instância WhatsApp

#### Bot WhatsApp - Melhorias
- [ ] Envio de mensagens via API
- [ ] Envio de mídia (imagens, documentos)
- [ ] Logs de mensagens enviadas/recebidas
- [ ] Retry de mensagens falhadas
- [ ] Deploy automático no Fly.io

#### Integrações (12 novas)
- [ ] Mailchimp
- [ ] Mercado Pago
- [ ] Pagarme
- [ ] Nuvemshop
- [ ] WooCommerce
- [ ] Meta Business CAPI
- [ ] Google Ads API
- [ ] Google Analytics 4
- [ ] Zendesk
- [ ] Google Sheets
- [ ] Pluga
- [ ] Webhook genérico

#### Analytics Básico
- [ ] Métricas reais no dashboard
- [ ] Contatos iniciados (por fluxo)
- [ ] Taxa de conclusão de fluxos
- [ ] Leads capturados (por período)
- [ ] Gráficos simples

---

## 🚀 Funcionalidades Core do MVP

### 1. Autenticação e Onboarding
**Status:** ✅ Implementado (atualizado Sprint 1.5)

**Funcionalidades Implementadas:**
- Login com email/senha
- Logout seguro
- Sessão persistente
- Proteção CSRF
- Checkout com Stripe Elements

**Sprint 1.5 — Onboarding em 3 Etapas (✅ Concluído):**
- Cadastro multi-step: Dados Pessoais → Empresa (opcional) → Checkout
- Tabela `companies` separada (dados jurídicos/comerciais opcionais)
- Campos `phone`, `document` e `document_type` em `users`
- Validação de CPF/CNPJ (Rules customizadas com dígitos verificadores)
- Layout `onboarding.blade.php` dedicado com stepper visual
- Máscaras de input vanilla JS (CPF, CNPJ, telefone, CEP)
- Busca de CEP via ViaCEP (módulo reutilizável)
- Checkout usa layout onboarding (sem navbar), success usa layout padrão
- 37 testes automatizados (20 unit + 5 model + 12 feature)
- Ver especificação em `.sprints/1.5/01-spec.md` e desenvolvimento em `.sprints/1.5/02-dev.md`

**Melhorias Futuras (Pós-MVP):**
- Verificação de email
- Recuperação de senha
- 2FA

---

### 2. Dashboard Principal
**Status:** ✅ Implementado

**Funcionalidades:**
- Visão geral do status do bot
- Métricas rápidas (contatos, jornadas, leads)
- Atalhos para módulos principais
- Informações do tenant

**Pendente:**
- Métricas reais (atualmente placeholder)
- Gráficos de tendência
- Alertas e notificações

---

### 3. Flow Builder Visual
**Status:** ✅ Implementado (Sprint 1 — 18 tipos de nós, React Flow)

**Funcionalidades Necessárias:**

#### Interface Drag & Drop
- Canvas infinito com zoom/pan
- Biblioteca de nós disponíveis
- Arrastar e soltar nós
- Conectar nós com edges
- Deletar nós e conexões
- Undo/redo

#### Tipos de Nós

##### 3.1. Nó de Início (Start)
- Ponto de entrada do fluxo
- Configuração de trigger (palavra-chave, horário)
- Apenas um por fluxo

##### 3.2. Nó de Mensagem (Message)
- Enviar texto simples
- Suporte a variáveis ({{nome}}, {{email}})
- Delay opcional antes de enviar
- Preview da mensagem

##### 3.3. Nó de Pergunta (Question)
- Fazer uma pergunta ao usuário
- Capturar resposta
- Validação de resposta (texto, número, email, telefone)
- Salvar em variável
- Timeout de resposta

##### 3.4. Nó de Condição (Condition)
- If/else baseado em variáveis
- Operadores: igual, diferente, contém, maior, menor
- Múltiplas saídas (true/false)
- Condições compostas (AND/OR)

##### 3.5. Nó de Ação (Action)
- Salvar lead no banco
- Atualizar dados do lead
- Adicionar tags
- Marcar como convertido

##### 3.6. Nó de Integração (Integration)
- Enviar lead para CRM
- Criar deal/oportunidade
- Enviar para planilha
- Webhook customizado
- Seleção de integração conectada

##### 3.7. Nó de Delay (Wait)
- Aguardar X segundos/minutos/horas
- Útil para sequências

##### 3.8. Nó de Fim (End)
- Finalizar conversa
- Mensagem de encerramento opcional
- Marcar sessão como concluída

#### Validações
- Fluxo deve ter início e fim
- Nós devem estar conectados
- Não pode ter loops infinitos
- Validar configuração de cada nó

#### Persistência
- Salvar fluxo automaticamente (auto-save)
- Versionamento básico
- Exportar/importar JSON

**Tecnologia Sugerida:**
- React Flow ou Xyflow
- Ou biblioteca similar (Drawflow, jsPlumb)

---

### 4. Engine de Execução de Fluxos ⚠️ CRÍTICO
**Status:** 🔄 Pendente

**Arquitetura:**

```
Mensagem Recebida
       ↓
WhatsappWebhookService
       ↓
FlowExecutionService
       ↓
SessionManager (recupera/cria sessão)
       ↓
FlowEngine (processa nó atual)
       ↓
NodeProcessor (executa lógica do nó)
       ↓
ActionGenerator (gera ações de resposta)
       ↓
WhatsappBotAPI (envia mensagens)
```

#### 4.1. SessionManager
**Responsabilidade:** Gerenciar sessões de conversa

**Funcionalidades:**
- Criar nova sessão ao iniciar conversa
- Recuperar sessão existente por tenant + phone
- Armazenar contexto da conversa (variáveis)
- Armazenar estado atual (nó atual, histórico)
- Expirar sessões inativas (30 min padrão)
- Limpar sessões antigas

**Modelo de Dados:**
```php
ConversationSession {
  id
  tenant_id
  flux_id
  lead_id (nullable)
  phone
  current_node_id
  context (JSON: variáveis capturadas)
  history (JSON: nós visitados)
  status (active, completed, expired)
  started_at
  last_interaction_at
  completed_at
  expires_at
}
```

#### 4.2. FlowEngine
**Responsabilidade:** Executar lógica do fluxo

**Funcionalidades:**
- Carregar fluxo do banco
- Identificar nó atual da sessão
- Processar mensagem recebida
- Determinar próximo nó
- Atualizar contexto
- Gerar ações de resposta

**Métodos Principais:**
```php
processMessage(Session $session, string $message): array
executeNode(Session $session, Node $node): NodeResult
transitionToNext(Session $session, string $output): Node
```

#### 4.3. NodeProcessors
**Responsabilidade:** Processar cada tipo de nó

**Implementação:**
- `StartNodeProcessor`
- `MessageNodeProcessor`
- `QuestionNodeProcessor`
- `ConditionNodeProcessor`
- `ActionNodeProcessor`
- `IntegrationNodeProcessor`
- `DelayNodeProcessor`
- `EndNodeProcessor`

**Interface:**
```php
interface NodeProcessorInterface {
  public function process(Session $session, Node $node, ?string $userInput): NodeResult;
  public function validate(Node $node): bool;
}
```

#### 4.4. ActionGenerator
**Responsabilidade:** Gerar ações para o bot

**Tipos de Ações:**
```json
{
  "actions": [
    {
      "type": "send_message",
      "text": "Olá! Qual seu nome?",
      "delay": 0
    },
    {
      "type": "save_lead",
      "data": {"name": "João", "phone": "5511999999999"}
    },
    {
      "type": "sync_crm",
      "provider": "rd_station_crm",
      "lead_id": 123
    }
  ]
}
```

#### 4.5. Tratamento de Erros
- Timeout de resposta → enviar lembrete ou finalizar
- Resposta inválida → solicitar novamente (max 3 tentativas)
- Erro em integração → logar e continuar fluxo
- Nó não encontrado → finalizar sessão com erro

---

### 5. Gerenciamento de Fluxos
**Status:** ✅ Implementado (Sprint 1)

**Funcionalidades Necessárias:**

#### 5.1. Listagem de Fluxos
- Tabela com todos os fluxos do tenant
- Colunas: Nome, Status, Leads, Taxa de Conversão, Última Edição
- Filtros: Status (ativo, inativo, rascunho)
- Busca por nome
- Ordenação

#### 5.2. Criar Fluxo
- Modal ou página para criar novo fluxo
- Campos: Nome, Descrição, Meta de Conversão
- Redirecionar para Flow Builder

#### 5.3. Editar Fluxo
- Abrir Flow Builder com fluxo existente
- Salvar alterações
- Validar antes de salvar

#### 5.4. Duplicar Fluxo
- Copiar fluxo existente
- Adicionar sufixo "(Cópia)"
- Manter como rascunho

#### 5.5. Ativar/Desativar
- Toggle para ativar/desativar fluxo
- Fluxo inativo não processa mensagens
- Confirmação antes de desativar

#### 5.6. Deletar Fluxo
- Confirmação obrigatória
- Não permitir deletar se houver sessões ativas
- Soft delete (manter histórico)

#### 5.7. Atribuir a Instância
- Vincular fluxo a uma instância WhatsApp
- Apenas um fluxo ativo por instância
- Configurar trigger (palavra-chave, sempre ativo)

---

### 6. Bot WhatsApp - Melhorias
**Status:** 🔄 Básico implementado, melhorias pendentes

**Funcionalidades Necessárias:**

#### 6.1. Envio de Mensagens
- Endpoint para enviar mensagens via API
- Suporte a texto simples
- Suporte a formatação (bold, italic)
- Suporte a emojis
- Retry automático em caso de falha

#### 6.2. Envio de Mídia
- Enviar imagens
- Enviar documentos (PDF, DOCX)
- Enviar áudios
- Enviar vídeos
- Upload via URL ou base64

#### 6.3. Logs de Mensagens
- Tabela `whatsapp_messages`
- Registrar todas as mensagens (enviadas e recebidas)
- Campos: tenant_id, phone, direction, type, content, status, sent_at
- Útil para debug e analytics

#### 6.4. Deploy Automático
- Script para criar container no Fly.io
- Variáveis de ambiente configuradas
- URL pública gerada automaticamente
- Atualizar `whatsapp_instances` com dados do deploy

#### 6.5. Health Check
- Endpoint `/health` no bot
- Dashboard verifica status periodicamente
- Alertar se bot estiver offline

---

### 7. Gerenciamento de Leads
**Status:** ✅ Implementado

**Funcionalidades:**
- Listagem com paginação
- Filtros por fluxo, status, data
- Ordenação de colunas
- Visualização de detalhes
- Edição de notas
- Exportação (Pós-MVP)

**Melhorias Futuras:**
- Tags e categorias
- Campos customizados
- Histórico de interações
- Atribuição a usuários
- Segmentação avançada

---

### 8. Analytics Básico
**Status:** 🔄 Estrutura no dashboard, dados reais pendentes

**Métricas Necessárias:**

#### Dashboard Principal
- **Contatos Iniciados:** Total de sessões criadas (hoje, semana, mês)
- **Jornadas Interrompidas:** Sessões expiradas sem completar
- **Leads Coletados:** Total de leads salvos
- **Taxa de Conversão:** % de sessões que viraram leads

#### Por Fluxo
- Visualizações do fluxo
- Sessões iniciadas
- Sessões completadas
- Taxa de conclusão
- Tempo médio de conclusão
- Pontos de abandono (qual nó)

#### Gráficos
- Linha: Leads coletados por dia (últimos 30 dias)
- Barra: Top 5 fluxos por conversão
- Funil: Etapas do fluxo com drop-off

**Implementação:**
- Queries otimizadas no banco
- Cache de métricas (atualizar a cada 5 min)
- Jobs assíncronos para cálculos pesados

---

## 💼 Painel Admin e Sistema de Pagamentos

**Status:** 🔄 Pendente (CRÍTICO para MVP)

Esta é uma funcionalidade **essencial** para o MVP, permitindo self-service completo: desde o cadastro até o provisionamento automático do tenant após pagamento.

**⚠️ IMPORTANTE:** No MVP inicial, **não haverá sistema de planos**. Será um **produto único com preço fixo e sem limites** para validar o mercado. O sistema de planos (Starter, Pro, Enterprise) será implementado no Pós-MVP.

---

### Arquitetura do Sistema de Pagamentos (MVP Simplificado)

```
Landing Page (Preço Único)
     ↓
Etapa 1: Cadastro do Dono (nome, email, senha, telefone, CPF)
     ↓
Etapa 2: Cadastro da Empresa (opcional — nome, CNPJ, segmento, endereço)
     ↓
Etapa 3: Checkout Stripe Elements (layout onboarding, sem navbar)
     ↓
Webhook de Confirmação
     ↓
Provisionamento Automático
     ↓
Tenant Ativo (SEM LIMITES) + Tela de Sucesso (layout padrão com navbar)
```

> **Atualizado em 06/02/2026:** Fluxo de cadastro dividido em 3 etapas (Sprint 1.5).
> Dados da empresa são opcionais, permitindo cadastro como pessoa física.
> Tabela `companies` criada separada de `tenants` para isolamento de dados.

**Simplificação para MVP:**
- ✅ Produto único com preço fixo (ex: R$ 297/mês)
- ✅ Sem limites de fluxos, leads ou mensagens
- ✅ Todas as 14 integrações incluídas
- ✅ Foco em validar o produto, não o modelo de precificação

---

### Modelos de Dados (MVP Simplificado)

#### 1. **subscriptions** (Assinaturas)
Controla status de pagamento do tenant (sem planos).

```sql
CREATE TABLE subscriptions (
  id BIGSERIAL PRIMARY KEY,
  tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
  
  -- Status da assinatura
  status VARCHAR(30) NOT NULL DEFAULT 'pending',
  -- pending, active, past_due, canceled, suspended, trialing
  
  -- Dados de pagamento
  payment_method VARCHAR(50),           -- stripe, mercado_pago, manual
  external_subscription_id VARCHAR(255), -- ID no gateway
  external_customer_id VARCHAR(255),     -- ID do cliente no gateway
  
  -- Período
  billing_cycle VARCHAR(20) NOT NULL DEFAULT 'monthly',
  current_period_start DATE,
  current_period_end DATE,
  
  -- Trial (opcional)
  trial_ends_at TIMESTAMP,
  
  -- Valor FIXO para MVP
  amount DECIMAL(10,2) NOT NULL DEFAULT 297.00, -- R$ 297/mês (exemplo)
  currency VARCHAR(3) DEFAULT 'BRL',
  
  -- Controle
  canceled_at TIMESTAMP,
  suspended_at TIMESTAMP,
  
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW(),
  
  UNIQUE(tenant_id)  -- Um tenant tem apenas uma assinatura ativa
);
```

**Simplificação:**
- ❌ Sem tabela `plans` no MVP
- ❌ Sem campo `plan_id`
- ✅ Valor fixo (R$ 297/mês como exemplo)
- ✅ Todos os tenants têm acesso completo

---

#### 3. **payments** (Histórico de Pagamentos)
Registra todos os pagamentos realizados.

```sql
CREATE TABLE payments (
  id BIGSERIAL PRIMARY KEY,
  subscription_id BIGINT NOT NULL REFERENCES subscriptions(id),
  tenant_id BIGINT NOT NULL REFERENCES tenants(id),
  
  -- Dados do pagamento
  amount DECIMAL(10,2) NOT NULL,
  currency VARCHAR(3) DEFAULT 'BRL',
  status VARCHAR(30) NOT NULL,          -- pending, paid, failed, refunded
  
  -- Gateway
  payment_method VARCHAR(50) NOT NULL,  -- stripe, mercado_pago
  external_payment_id VARCHAR(255),     -- ID no gateway
  payment_link VARCHAR(500),            -- Link de pagamento gerado
  
  -- Metadados
  metadata JSONB,                       -- Dados adicionais do gateway
  
  -- Datas
  paid_at TIMESTAMP,
  failed_at TIMESTAMP,
  refunded_at TIMESTAMP,
  
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW(),
  
  INDEX(tenant_id),
  INDEX(external_payment_id)
);
```

---

**Nota:** A tabela `usage_tracking` será implementada no Pós-MVP quando houver sistema de planos com limites. No MVP, todos os tenants têm acesso ilimitado.

---

### Funcionalidades do Painel Admin (MVP Simplificado)

#### 1. **Gestão de Tenants**
**Rota:** `/admin/tenants`

**Funcionalidades:**
- ✅ Listar todos os tenants
- ✅ Criar tenant manualmente (para casos especiais)
- ✅ Visualizar detalhes do tenant
- ✅ Ver assinatura e histórico de pagamentos
- ✅ Ver estatísticas de uso (leads, mensagens, fluxos)
- ✅ Suspender/reativar tenant
- ✅ Gerar link de pagamento manual

**Tela:**
- Tabela com: Empresa, Status, MRR, Última Atividade, Ações
- Filtros: Status (ativo, suspenso, trial)
- Busca por nome/email
- Detalhes do tenant:
  - Informações básicas
  - Assinatura atual
  - Histórico de pagamentos
  - Estatísticas de uso (informativo, sem limites)
  - Logs de atividade

---

#### 2. **Dashboard Admin**
**Rota:** `/admin`

**Métricas:**
- Total de tenants (ativos, trial, suspensos)
- MRR (Monthly Recurring Revenue)
- Churn rate
- Novos tenants (hoje, semana, mês)
- Receita total
- Crescimento de receita (gráfico linha)
- Tenants em risco (pagamento vencido)
- Estatísticas de uso agregadas (total de leads, mensagens, fluxos)

---

### Fluxo de Cadastro e Pagamento (MVP Simplificado)

#### Passo 1: Landing Page
**Rota:** `/` ou `/pricing` (pública)

- Apresentar o produto e valor único (ex: R$ 297/mês)
- Listar todas as funcionalidades incluídas
- Destacar "Sem limites" como diferencial
- Botão "Começar Agora" único

---

#### Passo 2: Cadastro (✅ Atualizado Sprint 1.5)
**Rota:** `/register` e `/register/company` (públicas)

**Etapa 1 — Dados Pessoais (`/register`):**
```
- Nome completo *
- Email *
- Senha *
- Confirmar Senha *
- Telefone (opcional, máscara)
- CPF (opcional, validado)
```

**Etapa 2 — Dados da Empresa (`/register/company`, opcional):**
```
- Nome da Empresa
- CNPJ (validado)
- Telefone Comercial
- Email Comercial
- Segmento (select)
- Endereço (CEP com auto-preenchimento via ViaCEP)
```

**Ação:**
1. Etapa 1: Validar dados pessoais, salvar na session (senha já hasheada)
2. Etapa 2: Validar dados da empresa (se preenchidos)
3. Criar tenant (status: pending)
4. Criar usuário (vinculado ao tenant, com phone/document)
5. Criar company (se dados preenchidos, vinculada ao tenant)
6. Criar subscription (status: pending, amount: 297.00)
7. Login automático
8. Redirecionar para checkout

---

#### Passo 3: Pagamento
**Rota:** `/checkout/{subscription}`

**Opções:**

##### Opção A: Stripe (Recomendado)
- Usar Stripe Checkout (hosted page)
- Configurar webhook para receber confirmação
- Suporta cartão de crédito e PIX

##### Opção B: Mercado Pago
- Gerar link de pagamento via API
- Configurar webhook (IPN)
- Suporta cartão, boleto e PIX

**Implementação:**
```php
// Controller
public function checkout(Subscription $subscription)
{
    $paymentService = app(PaymentService::class);
    
    $paymentLink = $paymentService->createPaymentLink($subscription, [
        'success_url' => route('checkout.success'),
        'cancel_url' => route('checkout.cancel'),
    ]);
    
    // Salvar payment record
    Payment::create([
        'subscription_id' => $subscription->id,
        'tenant_id' => $subscription->tenant_id,
        'amount' => $subscription->amount,
        'status' => 'pending',
        'payment_method' => 'stripe',
        'payment_link' => $paymentLink->url,
        'external_payment_id' => $paymentLink->id,
    ]);
    
    return redirect($paymentLink->url);
}
```

---

#### Passo 4: Webhook de Confirmação
**Rota:** `/webhooks/stripe` ou `/webhooks/mercadopago`

**Ação ao receber confirmação de pagamento:**
1. Validar webhook (assinatura)
2. Buscar payment pelo external_payment_id
3. Atualizar payment (status: paid, paid_at)
4. Atualizar subscription (status: active, current_period_start, current_period_end)
5. Atualizar tenant (status: active)
6. **Provisionar recursos:**
   - Criar WhatsappInstance
   - Criar fluxo de exemplo (opcional)
   - Configurar limites
7. Enviar email de boas-vindas
8. Disparar evento `TenantActivated`

**Implementação:**
```php
// WebhookController
public function stripe(Request $request)
{
    $payload = $request->all();
    $signature = $request->header('Stripe-Signature');
    
    // Validar webhook
    $event = \Stripe\Webhook::constructEvent(
        $request->getContent(),
        $signature,
        config('services.stripe.webhook_secret')
    );
    
    if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object;
        
        $payment = Payment::where('external_payment_id', $session->id)->firstOrFail();
        
        DB::transaction(function () use ($payment, $session) {
            // Atualizar payment
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'metadata' => $session,
            ]);
            
            // Atualizar subscription (sem plano, valor fixo)
            $subscription = $payment->subscription;
            $subscription->update([
                'status' => 'active',
                'external_subscription_id' => $session->subscription,
                'external_customer_id' => $session->customer,
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
                'amount' => 297.00, // Valor fixo do MVP
            ]);
            
            // Ativar tenant
            $tenant = $subscription->tenant;
            $tenant->update(['status' => 'active']);
            
            // Provisionar recursos
            app(TenantProvisioningService::class)->provision($tenant);
            
            // Email de boas-vindas
            Mail::to($tenant->users->first()->email)
                ->send(new WelcomeEmail($tenant));
        });
    }
    
    return response()->json(['received' => true]);
}
```

---

#### Passo 5: Provisionamento Automático
**Service:** `TenantProvisioningService`

**Ações:**
```php
class TenantProvisioningService
{
    public function provision(Tenant $tenant): void
    {
        // 1. Criar WhatsappInstance
        WhatsappInstance::create([
            'tenant_id' => $tenant->id,
            'status' => 'inactive',
            'bot_token' => Str::random(40),
        ]);
        
        // 2. Criar fluxo de exemplo (opcional)
        Flux::create([
            'tenant_id' => $tenant->id,
            'name' => 'Fluxo de Boas-vindas',
            'status' => 'draft',
            'data' => $this->getWelcomeFlowTemplate(),
        ]);
        
        // 3. Log
        Log::info("Tenant provisionado com sucesso", [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
        ]);
    }
}
```

---

### Controle de Acesso (MVP Simplificado)

#### Middleware: `CheckSubscriptionStatus`

Validar apenas se assinatura está ativa (sem limites):

```php
class CheckSubscriptionStatus
{
    public function handle(Request $request, Closure $next)
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription;
        
        // Verificar apenas se tem assinatura ativa
        if (!$subscription || $subscription->status !== 'active') {
            return redirect()->route('subscription.expired')
                ->with('error', 'Sua assinatura está inativa. Por favor, regularize seu pagamento.');
        }
        
        // No MVP: SEM LIMITES, apenas verifica se está pagando
        return $next($request);
    }
}
```

**Nota:** O sistema de limites (`CheckPlanLimits`) será implementado no Pós-MVP quando houver planos diferenciados.

---

### Suspensão Automática por Inadimplência

#### Job: `CheckExpiredSubscriptions`

Executar diariamente via cron:

```php
class CheckExpiredSubscriptions implements ShouldQueue
{
    public function handle(): void
    {
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('current_period_end', '<', now()->subDays(7)) // 7 dias de tolerância
            ->get();
        
        foreach ($expiredSubscriptions as $subscription) {
            // Suspender tenant
            $subscription->tenant->update(['status' => 'suspended']);
            
            // Atualizar subscription
            $subscription->update([
                'status' => 'suspended',
                'suspended_at' => now(),
            ]);
            
            // Enviar email de notificação
            Mail::to($subscription->tenant->users->first()->email)
                ->send(new SubscriptionSuspendedEmail($subscription));
            
            Log::warning("Tenant suspenso por inadimplência", [
                'tenant_id' => $subscription->tenant_id,
            ]);
        }
    }
}
```

---

### Telas Necessárias (MVP Simplificado)

#### 1. Landing Page (`/` ou `/pricing`)
- Apresentação do produto
- Preço único destacado (R$ 297/mês)
- Lista de funcionalidades incluídas
- Destaque "Sem Limites"
- FAQ
- Botão CTA único "Começar Agora"

#### 2. Cadastro (`/register` + `/register/company`) — Sprint 1.5
- Formulário em 2 etapas com stepper visual
- Etapa 1: Dados pessoais (nome, email, senha, telefone, CPF)
- Etapa 2: Dados da empresa (opcional — nome, CNPJ, segmento, endereço)
- Máscaras de input (CPF, CNPJ, telefone, CEP)
- Busca de CEP via ViaCEP
- Validação de CPF/CNPJ
- Layout `onboarding` dedicado

#### 3. Checkout (`/checkout/{subscription}`)
- Resumo do valor (R$ 297/mês)
- Lista do que está incluído
- Botão para gerar link de pagamento
- Redirecionamento para gateway

#### 4. Sucesso (`/checkout/success`)
- Mensagem de confirmação
- Próximos passos
- Botão para acessar dashboard

#### 5. Admin - Dashboard (`/admin`)
- Métricas principais (MRR, tenants, churn)
- Gráficos de crescimento
- Lista de tenants recentes

#### 6. Admin - Tenants (`/admin/tenants`)
- Tabela de tenants
- Detalhes do tenant
- Ações (suspender, reativar, gerar link de pagamento)

#### 7. Tenant - Assinatura (`/dashboard/subscription`)
- Status da assinatura
- Próximo vencimento
- Histórico de pagamentos
- Estatísticas de uso (informativo)
- Botão para cancelar

---

### Integrações de Pagamento

#### Stripe (Recomendado)
**Vantagens:**
- Melhor UX
- Suporte a PIX no Brasil
- Webhooks confiáveis
- Dashboard completo
- Suporte a assinaturas recorrentes

**Setup:**
```bash
composer require stripe/stripe-php
```

**Config:**
```php
// config/services.php
'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
],
```

---

#### Mercado Pago (Alternativa)
**Vantagens:**
- Popular no Brasil
- Suporte a boleto
- Taxas competitivas

**Setup:**
```bash
composer require mercadopago/dx-php
```

**Config:**
```php
// config/services.php
'mercadopago' => [
    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
],
```

---

### Segurança

#### Validação de Webhooks
- Sempre validar assinatura do webhook
- Usar HTTPS obrigatório
- Logar todas as tentativas

#### Proteção de Dados
- Nunca armazenar dados de cartão
- Encriptar tokens de gateway
- Compliance com PCI-DSS (via gateway)

#### Rate Limiting
- Limitar criação de tenants por IP
- Prevenir abuso de trial

---

### Testes Necessários

#### Unitários
- [ ] Validação de limites de plano
- [ ] Cálculo de MRR
- [ ] Lógica de suspensão

#### Integração
- [ ] Fluxo completo de cadastro
- [ ] Webhook de pagamento (mock)
- [ ] Provisionamento de tenant

#### End-to-End
- [ ] Cadastro → Pagamento → Ativação
- [ ] Suspensão por inadimplência
- [ ] Upgrade de plano

---

## � Integrações do MVP

### Arquitetura de Integrações

Todas as integrações seguem o padrão estabelecido:

```php
app/Integrations/
├── Contracts/
│   ├── IntegrationInterface.php
│   ├── CrmIntegrationContract.php
│   ├── EmailMarketingContract.php
│   ├── PaymentGatewayContract.php
│   ├── EcommerceContract.php
│   ├── TrafficContract.php
│   ├── SupportContract.php
│   └── AutomationContract.php
├── Crm/
├── EmailMarketing/
├── Payment/
├── Ecommerce/
├── Traffic/
├── Support/
├── Automation/
└── IntegrationRegistry.php
```

---

### 1. CRM e Vendas

#### 1.1. RD Station CRM ✅
**Status:** Implementado

**Funcionalidades:**
- ✅ Autenticação via API Token
- ✅ Teste de conexão
- ✅ Criar/atualizar contatos
- ✅ Adicionar notas
- ✅ Incluir UTMs

**Campos Mapeados:**
- Nome, Email, Telefone, Notas

---

#### 1.2. Pipedrive ✅
**Status:** Implementado

**Funcionalidades:**
- ✅ Autenticação via API Token
- ✅ Teste de conexão
- ✅ Criar pessoas (contacts)
- ✅ Criar negócios (deals)
- ✅ Vincular pessoa ao negócio
- ✅ Campos customizados

**Campos Mapeados:**
- Nome, Email, Telefone, Organização, Pipeline, Estágio

---

### 2. E-mail Marketing

#### 2.1. Mailchimp 🔄
**Status:** Pendente

**Autenticação:** API Key

**Funcionalidades Necessárias:**
- Listar audiências (listas)
- Adicionar/atualizar subscriber
- Adicionar tags
- Atualizar campos customizados (merge fields)
- Verificar status de inscrição

**Endpoints:**
```
GET  /3.0/lists
POST /3.0/lists/{list_id}/members
PUT  /3.0/lists/{list_id}/members/{subscriber_hash}
```

**Casos de Uso:**
- Adicionar lead a lista de newsletter
- Segmentar por tags (ex: "lead-whatsapp")
- Atualizar dados do contato

**Documentação:** https://mailchimp.com/developer/marketing/api/

---

### 3. Gateways de Pagamento

#### 3.1. Mercado Pago 🔄
**Status:** Pendente

**Autenticação:** Access Token (OAuth 2.0)

**Funcionalidades Necessárias:**
- Criar link de pagamento
- Criar cobrança (charge)
- Consultar status de pagamento
- Webhook de notificações (IPN)
- Listar pagamentos do cliente

**Endpoints:**
```
POST /v1/payment_links
POST /v1/payments
GET  /v1/payments/{id}
GET  /v1/payments/search
```

**Casos de Uso:**
- Enviar link de pagamento via WhatsApp
- Confirmar pagamento antes de prosseguir no fluxo
- Notificar vendedor quando pagamento aprovado

**Documentação:** https://www.mercadopago.com.br/developers/

---

#### 3.2. Pagar.me 🔄
**Status:** Pendente

**Autenticação:** API Key

**Funcionalidades Necessárias:**
- Criar link de pagamento
- Criar transação (cartão, boleto, PIX)
- Consultar status de transação
- Webhook de postback
- Listar transações do cliente

**Endpoints:**
```
POST /1/transactions
GET  /1/transactions/{id}
POST /1/payables
```

**Casos de Uso:**
- Gerar boleto e enviar via WhatsApp
- Criar link de checkout
- Validar pagamento PIX em tempo real

**Documentação:** https://docs.pagar.me/

---

### 4. E-commerce

#### 4.1. Nuvemshop 🔄
**Status:** Pendente

**Autenticação:** OAuth 2.0

**Funcionalidades Necessárias:**
- Listar produtos
- Buscar produto por ID/SKU
- Criar pedido (order)
- Atualizar status de pedido
- Consultar estoque
- Webhook de novos pedidos

**Endpoints:**
```
GET  /v1/{store_id}/products
GET  /v1/{store_id}/products/{id}
POST /v1/{store_id}/orders
GET  /v1/{store_id}/orders/{id}
```

**Casos de Uso:**
- Enviar catálogo de produtos via WhatsApp
- Criar pedido a partir da conversa
- Notificar cliente sobre status do pedido
- Recuperar carrinho abandonado

**Documentação:** https://tiendanube.github.io/api-documentation/

---

#### 4.2. WooCommerce 🔄
**Status:** Pendente

**Autenticação:** Consumer Key + Consumer Secret (OAuth 1.0a)

**Funcionalidades Necessárias:**
- Listar produtos
- Buscar produto por ID/SKU
- Criar pedido (order)
- Atualizar status de pedido
- Consultar estoque
- Webhook de novos pedidos

**Endpoints:**
```
GET  /wp-json/wc/v3/products
GET  /wp-json/wc/v3/products/{id}
POST /wp-json/wc/v3/orders
GET  /wp-json/wc/v3/orders/{id}
```

**Casos de Uso:**
- Enviar catálogo de produtos
- Criar pedido via WhatsApp
- Notificar sobre status de entrega
- Suporte pós-venda

**Documentação:** https://woocommerce.github.io/woocommerce-rest-api-docs/

---

### 5. Tráfego

#### 5.1. Meta Business CAPI (Conversions API) 🔄
**Status:** Pendente

**Autenticação:** Access Token + Pixel ID

**Funcionalidades Necessárias:**
- Enviar evento de conversão (Lead, Purchase, etc)
- Enviar dados do usuário (email, phone, nome)
- Enviar dados do evento (value, currency, content_name)
- Deduplicação com Pixel (event_id)

**Endpoint:**
```
POST https://graph.facebook.com/v18.0/{pixel_id}/events
```

**Eventos Suportados:**
- Lead (captura de lead)
- Purchase (compra realizada)
- AddToCart (adicionar ao carrinho)
- InitiateCheckout (iniciar checkout)
- ViewContent (visualizar produto)

**Casos de Uso:**
- Enviar conversão quando lead é capturado
- Otimizar campanhas do Facebook Ads
- Melhorar atribuição de conversões

**Documentação:** https://developers.facebook.com/docs/marketing-api/conversions-api

---

#### 5.2. Google Ads API 🔄
**Status:** Pendente

**Autenticação:** OAuth 2.0 + Developer Token

**Funcionalidades Necessárias:**
- Enviar conversão offline (OfflineConversionUpload)
- Associar conversão a GCLID
- Enviar dados de conversão (value, currency)
- Listar campanhas (opcional)

**Endpoint:**
```
POST https://googleads.googleapis.com/v14/customers/{customer_id}/offlineUserDataJobs
```

**Casos de Uso:**
- Enviar conversão quando lead é qualificado
- Otimizar lances de campanhas
- Melhorar ROAS (Return on Ad Spend)

**Documentação:** https://developers.google.com/google-ads/api/docs/conversions/upload-offline-conversions

---

#### 5.3. Google Analytics 4 (GA4) 🔄
**Status:** Pendente

**Autenticação:** Measurement Protocol API Key

**Funcionalidades Necessárias:**
- Enviar evento customizado
- Enviar parâmetros do evento
- Associar a client_id ou user_id
- Enviar dados de e-commerce (opcional)

**Endpoint:**
```
POST https://www.google-analytics.com/mp/collect?measurement_id={measurement_id}&api_secret={api_secret}
```

**Eventos Suportados:**
- generate_lead
- purchase
- begin_checkout
- add_to_cart
- view_item

**Casos de Uso:**
- Rastrear conversões do WhatsApp no GA4
- Analisar funil de conversão
- Atribuir valor a leads

**Documentação:** https://developers.google.com/analytics/devguides/collection/protocol/ga4

---

### 6. Suporte

#### 6.1. Zendesk 🔄
**Status:** Pendente

**Autenticação:** API Token ou OAuth 2.0

**Funcionalidades Necessárias:**
- Criar ticket
- Atualizar ticket
- Adicionar comentário
- Buscar ticket por usuário
- Listar tickets
- Webhook de atualizações

**Endpoints:**
```
POST /api/v2/tickets
PUT  /api/v2/tickets/{id}
GET  /api/v2/tickets/{id}
GET  /api/v2/users/{id}/tickets
```

**Casos de Uso:**
- Criar ticket de suporte via WhatsApp
- Atualizar cliente sobre status do ticket
- Escalar conversa para atendimento humano
- Histórico unificado de interações

**Documentação:** https://developer.zendesk.com/api-reference/

---

### 7. Automação

#### 7.1. Google Sheets 🔄
**Status:** Pendente

**Autenticação:** OAuth 2.0 (Service Account ou User)

**Funcionalidades Necessárias:**
- Adicionar linha em planilha
- Atualizar célula específica
- Ler dados de planilha
- Criar nova aba
- Formatar células (opcional)

**API:** Google Sheets API v4

**Endpoints:**
```
POST /v4/spreadsheets/{spreadsheetId}/values/{range}:append
PUT  /v4/spreadsheets/{spreadsheetId}/values/{range}
GET  /v4/spreadsheets/{spreadsheetId}/values/{range}
```

**Casos de Uso:**
- Adicionar lead em planilha de controle
- Sincronizar dados com equipe
- Backup simples de leads
- Relatórios customizados

**Documentação:** https://developers.google.com/sheets/api

---

#### 7.2. Pluga 🔄
**Status:** Pendente

**Autenticação:** Webhook URL + Secret

**Funcionalidades Necessárias:**
- Enviar dados via webhook
- Formato JSON padronizado
- Retry em caso de falha
- Logs de envio

**Implementação:**
- Pluga funciona recebendo webhooks
- Não há API para consultar, apenas enviar
- Usuário configura automação no Pluga

**Casos de Uso:**
- Conectar com ferramentas não suportadas nativamente
- Automações complexas (ex: Notion, Trello, Asana)
- Flexibilidade para integrações customizadas

**Documentação:** https://pluga.co/ferramentas/webhooks/

---

#### 7.3. Webhook Genérico 🔄
**Status:** Pendente

**Autenticação:** Opcional (Bearer Token, Basic Auth, API Key)

**Funcionalidades Necessárias:**
- Configurar URL de destino
- Escolher método HTTP (POST, PUT, PATCH)
- Configurar headers customizados
- Configurar body (JSON, form-data)
- Mapear variáveis do lead
- Retry automático
- Logs de requisições

**Interface de Configuração:**
```json
{
  "url": "https://api.example.com/leads",
  "method": "POST",
  "headers": {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json"
  },
  "body": {
    "name": "{{lead.name}}",
    "email": "{{lead.email}}",
    "phone": "{{lead.phone}}",
    "source": "whatsapp"
  }
}
```

**Casos de Uso:**
- Integrar com API proprietária
- Enviar para sistema interno
- Notificar equipe via Slack/Discord
- Máxima flexibilidade

---

## 📅 Roadmap de Desenvolvimento

### Sprint 0: Painel Admin e Sistema de Pagamentos ✅ CONCLUÍDA
**Data:** 03/02/2026

**Entregas:** 52 testes, 35+ arquivos, sistema completo de pagamentos com Stripe, painel admin, provisionamento automático.

---

### Sprint 1: Flow Builder Visual ✅ CONCLUÍDA
**Data:** 03-04/02/2026

**Entregas:** 51 testes, 40+ arquivos, Flow Builder com React Flow, 18 tipos de nós, CRUD completo.

---

### Sprint 1.5: Onboarding em 3 Etapas ✅ CONCLUÍDA
**Data:** 06/02/2026

**Entregas:** 37 testes, 16 novos arquivos + 6 editados, onboarding 3 etapas, validação CPF/CNPJ, máscaras de input, busca CEP, layout dedicado com stepper visual.

---

### Sprint 2: Engine de Execução (2-3 semanas) ← PRÓXIMA
**Objetivo:** Processar mensagens e executar fluxos

**Tarefas:**
- [ ] Criar modelo `ConversationSession`
- [ ] Implementar `SessionManager`
- [ ] Implementar `FlowEngine`
- [ ] Criar `NodeProcessors` para cada tipo de nó
- [ ] Implementar `ActionGenerator`
- [ ] Integrar com `WhatsappWebhookService`
- [ ] Tratamento de erros e timeouts
- [ ] Logs estruturados
- [ ] Testes unitários e de integração

**Entregável:** Fluxos executando conversas reais no WhatsApp

---

### Sprint 3: CRUD de Fluxos + Bot Melhorias (1-2 semanas)
**Objetivo:** Gerenciar fluxos e melhorar bot

**Tarefas:**
- [ ] Listagem de fluxos
- [ ] Criar/editar/duplicar/deletar fluxos
- [ ] Ativar/desativar fluxos
- [ ] Atribuir fluxo a instância
- [ ] Envio de mensagens via API
- [ ] Envio de mídia (imagens, docs)
- [ ] Logs de mensagens
- [ ] Deploy automático no Fly.io
- [ ] Health check do bot

**Entregável:** Gerenciamento completo de fluxos e bot robusto

---

### Sprint 4: Integrações - Parte 1 (2 semanas)
**Objetivo:** Implementar 6 integrações

**Tarefas:**
- [ ] Mailchimp (Email Marketing)
- [ ] Mercado Pago (Pagamento)
- [ ] Pagarme (Pagamento)
- [ ] Nuvemshop (E-commerce)
- [ ] WooCommerce (E-commerce)
- [ ] Google Sheets (Automação)

**Entregável:** 6 integrações funcionais + testes

---

### Sprint 5: Integrações - Parte 2 (2 semanas)
**Objetivo:** Implementar 6 integrações restantes

**Tarefas:**
- [ ] Meta Business CAPI (Tráfego)
- [ ] Google Ads API (Tráfego)
- [ ] Google Analytics 4 (Tráfego)
- [ ] Zendesk (Suporte)
- [ ] Pluga (Automação)
- [ ] Webhook Genérico (Automação)

**Entregável:** Todas as 14 integrações funcionais

---

### Sprint 6: Analytics + Polimento (1-2 semanas)
**Objetivo:** Métricas reais e refinamentos finais

**Tarefas:**
- [ ] Implementar métricas do dashboard
- [ ] Gráficos de tendência
- [ ] Analytics por fluxo
- [ ] Cache de métricas
- [ ] Otimização de queries
- [ ] Testes de carga
- [ ] Correção de bugs
- [ ] Melhorias de UX
- [ ] Documentação de usuário

**Entregável:** MVP completo e polido

---

### Sprint 8: Testes e Homologação (1 semana)
**Objetivo:** Garantir qualidade antes do lançamento

**Tarefas:**
- [ ] Testes end-to-end
- [ ] Testes de todas as integrações
- [ ] Testes de carga (stress test)
- [ ] Testes de segurança
- [ ] Homologação com usuários beta
- [ ] Correção de bugs críticos
- [ ] Preparar ambiente de produção
- [ ] Documentação final

**Entregável:** MVP pronto para lançamento

---

## ✅ Critérios de Aceitação do MVP

### Funcionalidades Obrigatórias

#### 1. Flow Builder
- [x] Usuário consegue criar fluxo visual
- [x] Todos os 8 tipos de nós funcionam
- [x] Validação impede salvar fluxo inválido
- [x] Fluxo salva automaticamente
- [x] Preview funciona corretamente

#### 2. Engine de Execução
- [x] Mensagem recebida inicia fluxo correto
- [x] Conversa segue o fluxo configurado
- [x] Variáveis são capturadas corretamente
- [x] Condições funcionam (if/else)
- [x] Integrações são acionadas no nó correto
- [x] Sessão expira após inatividade
- [x] Erros são tratados graciosamente

#### 3. Gerenciamento de Fluxos
- [x] Listar todos os fluxos
- [x] Criar novo fluxo
- [x] Editar fluxo existente
- [x] Duplicar fluxo
- [x] Ativar/desativar fluxo
- [x] Deletar fluxo (com confirmação)
- [x] Atribuir fluxo a instância WhatsApp

#### 4. Bot WhatsApp
- [x] Conectar via QR Code
- [x] Receber mensagens
- [x] Enviar mensagens de texto
- [x] Enviar imagens
- [x] Status atualiza corretamente
- [x] Logs de mensagens funcionam
- [x] Deploy automático funciona

#### 5. Integrações (14 total)
- [x] Todas as 14 integrações conectam
- [x] Teste de conexão funciona
- [x] Sincronização de leads funciona
- [x] Erros são logados
- [x] Retry funciona em falhas temporárias

#### 6. Analytics
- [x] Métricas do dashboard são reais
- [x] Gráficos carregam corretamente
- [x] Analytics por fluxo funcionam
- [x] Performance é aceitável (<2s)

### Critérios de Qualidade

#### Performance
- [ ] Dashboard carrega em <2 segundos
- [ ] Flow Builder responde em <100ms
- [ ] Mensagens processadas em <1 segundo
- [ ] Suporta 100 conversas simultâneas
- [ ] Banco otimizado (índices corretos)

#### Segurança
- [ ] Autenticação funciona corretamente
- [ ] Isolamento multi-tenant perfeito
- [ ] Credenciais de integração encriptadas
- [ ] CSRF protection ativo
- [ ] Rate limiting em APIs

#### Usabilidade
- [ ] Interface intuitiva (teste com 5 usuários)
- [ ] Mensagens de erro claras
- [ ] Loading states em todas as ações
- [ ] Responsivo (mobile-friendly)
- [ ] Acessibilidade básica (WCAG 2.0 A)

#### Confiabilidade
- [ ] Uptime >99% em testes
- [ ] Retry automático funciona
- [ ] Logs estruturados
- [ ] Monitoramento ativo
- [ ] Backup automático do banco

---

## 🚀 Pós-MVP (Roadmap Futuro)

### Fase 2: Crescimento (3-6 meses após MVP)

#### Funcionalidades Avançadas
- [ ] Templates de fluxos prontos
- [ ] Marketplace de fluxos
- [ ] A/B testing de fluxos
- [ ] Segmentação avançada de leads
- [ ] Campos customizados
- [ ] Tags e categorias
- [ ] Atribuição de leads a usuários
- [ ] Funil de vendas visual

#### Mais Integrações
- [ ] HubSpot CRM
- [ ] Salesforce
- [ ] ActiveCampaign
- [ ] Stripe
- [ ] Shopify
- [ ] Hotmart
- [ ] Eduzz
- [ ] Intercom

#### Analytics Avançado
- [ ] Relatórios customizados
- [ ] Exportação de relatórios
- [ ] Dashboards por usuário
- [ ] Previsão de conversões (ML)
- [ ] Análise de sentimento

#### Automação Avançada
- [ ] Agendamento de mensagens
- [ ] Broadcast de mensagens
- [ ] Sequências de follow-up
- [ ] Remarketing automático
- [ ] Chatbot com IA (GPT)

#### Administração
- [ ] Painel de admin multi-tenant
- [ ] Gerenciamento de usuários
- [ ] Roles e permissões granulares
- [ ] Auditoria completa
- [ ] Faturamento e planos
- [ ] API pública para desenvolvedores

---

### Fase 3: Escala (6-12 meses após MVP)

#### Infraestrutura
- [ ] Migrar para Kubernetes
- [ ] Redis para cache e queue
- [ ] CDN para assets
- [ ] Multi-região (latência baixa)
- [ ] Auto-scaling
- [ ] Disaster recovery

#### Recursos Enterprise
- [ ] SSO (Single Sign-On)
- [ ] SAML/LDAP
- [ ] SLA garantido
- [ ] Suporte dedicado
- [ ] Onboarding personalizado
- [ ] White-label

#### Novos Canais
- [ ] Telegram
- [ ] Instagram Direct
- [ ] Facebook Messenger
- [ ] SMS
- [ ] Email
- [ ] Omnichannel unificado

---

## 📊 Métricas de Sucesso do MVP

### Métricas de Produto
- **Adoção:** 50+ tenants ativos em 3 meses
- **Engajamento:** 70%+ dos tenants criam pelo menos 1 fluxo
- **Retenção:** 80%+ dos tenants ativos após 30 dias
- **NPS:** >50 (Net Promoter Score)

### Métricas Técnicas
- **Uptime:** >99.5%
- **Performance:** <2s tempo de resposta médio
- **Bugs Críticos:** <5 por mês
- **Taxa de Erro:** <1% das requisições

### Métricas de Negócio
- **MRR:** R$ 50k em 6 meses
- **CAC:** <R$ 500 por tenant
- **LTV:** >R$ 3.000 por tenant
- **Churn:** <5% ao mês

---

## 🎯 Resumo Executivo

### O que temos hoje (90%)
✅ Infraestrutura multi-tenant  
✅ Autenticação e dashboard  
✅ Gerenciamento de leads completo  
✅ Webhooks do bot WhatsApp  
✅ 2 integrações CRM funcionais  
✅ Arquitetura de integrações extensível  
✅ Painel Admin + Sistema de Pagamentos (Sprint 0)  
✅ Flow Builder Visual com 18 nós (Sprint 1)  
✅ Onboarding em 3 Etapas com CPF/CNPJ (Sprint 1.5)  

### O que falta para MVP (10%)
🔄 **Engine de execução de fluxos** (CRÍTICO — Sprint 2)  
🔄 Melhorias no bot (envio de mensagens/mídia)  

### Sprints Concluídas
| Sprint | Descrição | Data | Testes |
|--------|-----------|------|--------|
| 0 | Admin + Pagamentos | 03/02/2026 | 52 |
| 1 | Flow Builder Visual | 03-04/02/2026 | 51 |
| 1.5 | Onboarding 3 Etapas | 06/02/2026 | 37 |
| **Total** | | | **140** |

### Próxima Sprint
**Sprint 2: Engine de Execução de Fluxos** — Máquina de estados, sessões de conversa, processamento de mensagens, 18 NodeProcessors.

---

**Última atualização:** 06/02/2026  
**Próxima revisão:** Após Sprint 2 (Engine de Execução)  
**Mantido por:** Equipe de Desenvolvimento Zaptria
