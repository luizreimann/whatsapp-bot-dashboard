# Sprint 01 - Discovery: O que precisa ser feito

**Data:** 03/02/2026  
**Sprint:** 01  
**Objetivo:** Mapear todas as tarefas pendentes para completar o MVP do Zaptria

---

## 📋 Índice

1. [Painel Admin e Sistema de Pagamentos](#painel-admin-e-sistema-de-pagamentos)
2. [Flow Builder Visual](#flow-builder-visual)
3. [Engine de Execução de Fluxos](#engine-de-execução-de-fluxos)
4. [Gerenciamento de Fluxos](#gerenciamento-de-fluxos)
5. [Bot WhatsApp - Melhorias](#bot-whatsapp---melhorias)
6. [Integrações (12 novas)](#integrações-12-novas)
7. [Analytics Básico](#analytics-básico)
8. [Infraestrutura e DevOps](#infraestrutura-e-devops)

---

## 🔴 CRÍTICO: Painel Admin e Sistema de Pagamentos

### Database
- [ ] Criar migration `create_subscriptions_table`
- [ ] Criar migration `create_payments_table`
- [ ] Adicionar campos em `tenants` (se necessário)

### Models
- [ ] Criar model `Subscription`
- [ ] Criar model `Payment`
- [ ] Adicionar relacionamentos em `Tenant`
- [ ] Adicionar relacionamentos em `User`

### Services
- [ ] Criar `PaymentService` (Stripe ou Mercado Pago)
  - [ ] Método `createPaymentLink()`
  - [ ] Método `handleWebhook()`
  - [ ] Método `cancelSubscription()`
- [ ] Criar `TenantProvisioningService`
  - [ ] Método `provision()` (criar WhatsappInstance, Flux exemplo)
  - [ ] Método `suspend()`
  - [ ] Método `reactivate()`

### Controllers
- [ ] Criar `Admin/AdminController` (dashboard admin)
- [ ] Criar `Admin/TenantController` (gestão de tenants)
- [ ] Criar `CheckoutController` (fluxo de pagamento)
- [ ] Criar `WebhookController` (receber webhooks de pagamento)
- [ ] Criar `SubscriptionController` (tenant visualizar assinatura)

### Middleware
- [ ] Criar `CheckSubscriptionStatus` (verificar se assinatura está ativa)
- [ ] Criar `IsAdmin` (proteger rotas admin)

### Jobs
- [ ] Criar `CheckExpiredSubscriptions` (suspender inadimplentes)
- [ ] Configurar schedule no `Kernel.php`

### Views - Landing Page
- [ ] Criar `/` ou `/pricing`
  - [ ] Hero section com proposta de valor
  - [ ] Preço único destacado (R$ 297/mês)
  - [ ] Lista de funcionalidades incluídas
  - [ ] Destaque "Sem Limites"
  - [ ] FAQ
  - [ ] CTA "Começar Agora"

### Views - Cadastro
- [ ] Criar `/register`
  - [ ] Formulário de cadastro
  - [ ] Validação em tempo real
  - [ ] Indicador de força de senha
  - [ ] Aceite de termos

### Views - Checkout
- [ ] Criar `/checkout/{subscription}`
  - [ ] Resumo do valor
  - [ ] Lista do que está incluído
  - [ ] Botão para gerar link de pagamento
  - [ ] Redirecionamento para gateway

### Views - Sucesso
- [ ] Criar `/checkout/success`
  - [ ] Mensagem de confirmação
  - [ ] Próximos passos
  - [ ] Botão para acessar dashboard

### Views - Admin
- [ ] Criar `/admin` (dashboard admin)
  - [ ] Métricas principais (MRR, tenants, churn)
  - [ ] Gráficos de crescimento
  - [ ] Lista de tenants recentes
  - [ ] Tenants em risco
- [ ] Criar `/admin/tenants` (gestão de tenants)
  - [ ] Tabela de tenants
  - [ ] Filtros (status, busca)
  - [ ] Detalhes do tenant
  - [ ] Ações (suspender, reativar, gerar link)

### Views - Tenant
- [ ] Criar `/dashboard/subscription`
  - [ ] Status da assinatura
  - [ ] Próximo vencimento
  - [ ] Histórico de pagamentos
  - [ ] Estatísticas de uso (informativo)
  - [ ] Botão para cancelar

### Rotas
- [ ] Adicionar rotas públicas (landing, register, checkout)
- [ ] Adicionar rotas admin (protegidas)
- [ ] Adicionar rota webhook (sem auth)
- [ ] Adicionar rotas tenant (subscription)

### Integrações de Pagamento
- [ ] Configurar Stripe SDK
  - [ ] Instalar `stripe/stripe-php`
  - [ ] Configurar keys no `.env`
  - [ ] Configurar webhook secret
- [ ] OU Configurar Mercado Pago SDK
  - [ ] Instalar `mercadopago/dx-php`
  - [ ] Configurar access token no `.env`

### Testes
- [ ] Teste unitário: `PaymentService`
- [ ] Teste unitário: `TenantProvisioningService`
- [ ] Teste integração: Webhook de pagamento (mock)
- [ ] Teste E2E: Cadastro → Pagamento → Ativação

---

## 🔴 CRÍTICO: Flow Builder Visual

### Pesquisa e Setup
- [ ] Pesquisar bibliotecas (React Flow, Xyflow, Drawflow)
- [ ] Decidir arquitetura (Inertia.js vs API separada)
- [ ] Setup React no Laravel
  - [ ] Configurar Vite para React
  - [ ] Instalar dependências React
  - [ ] Configurar Inertia.js (se escolhido)

### Componentes do Canvas
- [ ] Criar componente `FlowCanvas`
  - [ ] Implementar drag & drop
  - [ ] Implementar zoom/pan
  - [ ] Implementar conexões entre nós
  - [ ] Implementar seleção de nós
  - [ ] Implementar delete de nós/conexões
  - [ ] Implementar undo/redo

### Biblioteca de Nós
- [ ] Criar componente `NodeLibrary` (sidebar)
  - [ ] Listar todos os tipos de nós disponíveis
  - [ ] Drag para adicionar ao canvas

### Tipos de Nós (8 tipos)
- [ ] Criar componente `StartNode`
  - [ ] Configuração de trigger
  - [ ] Validação (apenas 1 por fluxo)
- [ ] Criar componente `MessageNode`
  - [ ] Input de texto
  - [ ] Suporte a variáveis {{nome}}
  - [ ] Delay opcional
  - [ ] Preview da mensagem
- [ ] Criar componente `QuestionNode`
  - [ ] Input de pergunta
  - [ ] Tipo de resposta (texto, número, email, telefone)
  - [ ] Nome da variável para salvar
  - [ ] Timeout de resposta
  - [ ] Validação de resposta
- [ ] Criar componente `ConditionNode`
  - [ ] Configurar condição (if/else)
  - [ ] Operadores (igual, diferente, contém, maior, menor)
  - [ ] Múltiplas saídas (true/false)
  - [ ] Condições compostas (AND/OR)
- [ ] Criar componente `ActionNode`
  - [ ] Tipo de ação (salvar lead, atualizar, adicionar tag)
  - [ ] Configuração da ação
- [ ] Criar componente `IntegrationNode`
  - [ ] Selecionar integração conectada
  - [ ] Configurar dados a enviar
  - [ ] Mapear campos
- [ ] Criar componente `DelayNode`
  - [ ] Configurar tempo de espera
  - [ ] Unidade (segundos, minutos, horas)
- [ ] Criar componente `EndNode`
  - [ ] Mensagem de encerramento (opcional)
  - [ ] Marcar como concluído

### Validação de Fluxos
- [ ] Implementar validador de fluxo
  - [ ] Verificar se tem início e fim
  - [ ] Verificar se todos os nós estão conectados
  - [ ] Detectar loops infinitos
  - [ ] Validar configuração de cada nó
  - [ ] Exibir erros de validação

### Persistência
- [ ] Implementar auto-save
  - [ ] Salvar a cada X segundos
  - [ ] Indicador de "salvando..."
- [ ] Implementar salvar manual
- [ ] Implementar carregar fluxo do banco
- [ ] Implementar exportar/importar JSON

### Preview
- [ ] Criar modo preview
  - [ ] Simular execução do fluxo
  - [ ] Destacar nó atual
  - [ ] Mostrar variáveis capturadas

### UI/UX
- [ ] Toolbar do canvas
  - [ ] Botões: Salvar, Preview, Validar, Exportar
  - [ ] Zoom in/out
  - [ ] Fit to screen
- [ ] Painel de propriedades
  - [ ] Editar configurações do nó selecionado
  - [ ] Validação em tempo real

---

## 🔴 CRÍTICO: Engine de Execução de Fluxos

### Database
- [ ] Criar migration `create_conversation_sessions_table`
  - [ ] Campos: tenant_id, flux_id, lead_id, phone, current_node_id
  - [ ] Campos: context (JSON), history (JSON), status
  - [ ] Campos: started_at, last_interaction_at, completed_at, expires_at

### Models
- [ ] Criar model `ConversationSession`
  - [ ] Relacionamentos (tenant, flux, lead)
  - [ ] Casts (context, history como array)
  - [ ] Scopes (active, expired)

### Services - SessionManager
- [ ] Criar `SessionManager`
  - [ ] Método `createSession()` (nova conversa)
  - [ ] Método `getSession()` (recuperar por tenant + phone)
  - [ ] Método `updateContext()` (salvar variáveis)
  - [ ] Método `updateCurrentNode()` (avançar no fluxo)
  - [ ] Método `expireSession()` (timeout)
  - [ ] Método `completeSession()` (finalizar)
  - [ ] Método `cleanOldSessions()` (limpar antigas)

### Services - FlowEngine
- [ ] Criar `FlowEngine`
  - [ ] Método `processMessage()` (processar mensagem recebida)
  - [ ] Método `executeNode()` (executar lógica do nó)
  - [ ] Método `transitionToNext()` (determinar próximo nó)
  - [ ] Método `evaluateCondition()` (avaliar condições)
  - [ ] Método `replaceVariables()` (substituir {{variáveis}})

### Services - NodeProcessors
- [ ] Criar `StartNodeProcessor`
  - [ ] Implementar `process()`
  - [ ] Implementar `validate()`
- [ ] Criar `MessageNodeProcessor`
  - [ ] Implementar `process()` (enviar mensagem)
  - [ ] Aplicar delay se configurado
- [ ] Criar `QuestionNodeProcessor`
  - [ ] Implementar `process()` (fazer pergunta)
  - [ ] Validar resposta do usuário
  - [ ] Salvar resposta em variável
  - [ ] Retry se resposta inválida (max 3x)
- [ ] Criar `ConditionNodeProcessor`
  - [ ] Implementar `process()` (avaliar condição)
  - [ ] Determinar saída (true/false)
- [ ] Criar `ActionNodeProcessor`
  - [ ] Implementar `process()` (executar ação)
  - [ ] Salvar lead
  - [ ] Atualizar lead
  - [ ] Adicionar tags
- [ ] Criar `IntegrationNodeProcessor`
  - [ ] Implementar `process()` (chamar integração)
  - [ ] Sincronizar com CRM
  - [ ] Tratar erros de integração
- [ ] Criar `DelayNodeProcessor`
  - [ ] Implementar `process()` (agendar próximo nó)
  - [ ] Usar queue para delay
- [ ] Criar `EndNodeProcessor`
  - [ ] Implementar `process()` (finalizar sessão)
  - [ ] Enviar mensagem de encerramento

### Services - ActionGenerator
- [ ] Criar `ActionGenerator`
  - [ ] Método `generateActions()` (gerar ações para o bot)
  - [ ] Suportar tipos: send_message, save_lead, sync_crm, delay

### Integração com WhatsappWebhookService
- [ ] Atualizar `WhatsappWebhookService::handleIncoming()`
  - [ ] Chamar `SessionManager::getSession()`
  - [ ] Chamar `FlowEngine::processMessage()`
  - [ ] Retornar ações para o bot

### Tratamento de Erros
- [ ] Implementar timeout de resposta
  - [ ] Enviar lembrete após X minutos
  - [ ] Finalizar sessão após Y minutos
- [ ] Implementar retry de resposta inválida
  - [ ] Máximo 3 tentativas
  - [ ] Mensagem de erro customizada
- [ ] Implementar fallback para erros de integração
  - [ ] Logar erro
  - [ ] Continuar fluxo ou finalizar

### Jobs
- [ ] Criar `ProcessDelayedNode` (executar nó após delay)
- [ ] Criar `ExpireInactiveSessions` (expirar sessões inativas)

### Testes
- [ ] Teste unitário: `SessionManager`
- [ ] Teste unitário: `FlowEngine`
- [ ] Teste unitário: Cada `NodeProcessor`
- [ ] Teste integração: Fluxo completo (início ao fim)
- [ ] Teste E2E: Mensagem → Processamento → Resposta

---

## 🟡 Gerenciamento de Fluxos

### Controllers
- [ ] Criar `FluxController`
  - [ ] Método `index()` (listar fluxos)
  - [ ] Método `create()` (criar novo)
  - [ ] Método `store()` (salvar novo)
  - [ ] Método `edit()` (editar existente)
  - [ ] Método `update()` (salvar alterações)
  - [ ] Método `duplicate()` (duplicar fluxo)
  - [ ] Método `destroy()` (deletar)
  - [ ] Método `toggleStatus()` (ativar/desativar)

### Views
- [ ] Criar `/dashboard/fluxes` (listagem)
  - [ ] Tabela de fluxos
  - [ ] Colunas: Nome, Status, Leads, Taxa Conversão, Última Edição
  - [ ] Filtros (status)
  - [ ] Busca por nome
  - [ ] Ordenação
  - [ ] Botão "Criar Novo Fluxo"
- [ ] Criar `/dashboard/fluxes/create` (criar)
  - [ ] Formulário: Nome, Descrição, Meta de Conversão
  - [ ] Redirecionar para Flow Builder
- [ ] Criar `/dashboard/fluxes/{flux}/edit` (editar)
  - [ ] Abrir Flow Builder com fluxo carregado
- [ ] Criar modal de confirmação para deletar
- [ ] Criar modal de duplicar (renomear)

### Funcionalidades
- [ ] Implementar soft delete
  - [ ] Adicionar campo `deleted_at` em migration
  - [ ] Usar SoftDeletes trait
- [ ] Implementar atribuição de fluxo a instância
  - [ ] Adicionar campo `flux_id` em `whatsapp_instances`
  - [ ] Apenas 1 fluxo ativo por instância
- [ ] Implementar trigger de fluxo
  - [ ] Palavra-chave específica
  - [ ] Sempre ativo (qualquer mensagem)

### Validações
- [ ] Validar nome único por tenant
- [ ] Validar fluxo antes de ativar
- [ ] Não permitir deletar se houver sessões ativas
- [ ] Não permitir desativar se for único fluxo ativo

---

## 🟡 Bot WhatsApp - Melhorias

### API de Envio de Mensagens
- [ ] Criar endpoint no bot Node.js para enviar mensagens
  - [ ] POST `/send-message`
  - [ ] Autenticação via bot_token
- [ ] Criar `WhatsappBotService` no Laravel
  - [ ] Método `sendMessage()`
  - [ ] Método `sendMedia()`
  - [ ] Retry automático em caso de falha

### Envio de Mídia
- [ ] Implementar envio de imagens
  - [ ] Upload via URL
  - [ ] Upload via base64
- [ ] Implementar envio de documentos (PDF, DOCX)
- [ ] Implementar envio de áudios
- [ ] Implementar envio de vídeos

### Logs de Mensagens
- [ ] Criar migration `create_whatsapp_messages_table`
  - [ ] Campos: tenant_id, phone, direction (sent/received)
  - [ ] Campos: type (text/image/document), content, status
  - [ ] Campos: sent_at, delivered_at, read_at
- [ ] Criar model `WhatsappMessage`
- [ ] Registrar todas as mensagens enviadas
- [ ] Registrar todas as mensagens recebidas
- [ ] Criar view para visualizar logs

### Deploy Automático
- [ ] Criar script de deploy no Fly.io
  - [ ] Criar app no Fly.io via API
  - [ ] Configurar variáveis de ambiente
  - [ ] Deploy do container Node.js
  - [ ] Obter URL pública
- [ ] Atualizar `whatsapp_instances` com dados do deploy
  - [ ] Campos: fly_app_name, public_url

### Health Check
- [ ] Implementar endpoint `/health` no bot
  - [ ] Retornar status da conexão WhatsApp
  - [ ] Retornar uptime
- [ ] Criar job `CheckBotHealth`
  - [ ] Verificar status periodicamente
  - [ ] Alertar se bot estiver offline
  - [ ] Atualizar status em `whatsapp_instances`

---

## 🟢 Integrações (12 novas)

### Email Marketing
- [ ] **Mailchimp**
  - [ ] Criar `MailchimpIntegration`
  - [ ] Implementar `testConnection()`
  - [ ] Implementar `addSubscriber()`
  - [ ] Implementar `updateSubscriber()`
  - [ ] Implementar `addTags()`
  - [ ] Criar view de conexão
  - [ ] Testes

### Gateways de Pagamento
- [ ] **Mercado Pago**
  - [ ] Criar `MercadoPagoIntegration`
  - [ ] Implementar `testConnection()`
  - [ ] Implementar `createPaymentLink()`
  - [ ] Implementar `getPaymentStatus()`
  - [ ] Implementar webhook handler
  - [ ] Criar view de conexão
  - [ ] Testes
- [ ] **Pagar.me**
  - [ ] Criar `PagarmeIntegration`
  - [ ] Implementar `testConnection()`
  - [ ] Implementar `createTransaction()`
  - [ ] Implementar `getTransactionStatus()`
  - [ ] Implementar webhook handler
  - [ ] Criar view de conexão
  - [ ] Testes

### E-commerce
- [ ] **Nuvemshop**
  - [ ] Criar `NuvemshopIntegration`
  - [ ] Implementar OAuth 2.0
  - [ ] Implementar `testConnection()`
  - [ ] Implementar `listProducts()`
  - [ ] Implementar `createOrder()`
  - [ ] Implementar `getOrderStatus()`
  - [ ] Criar view de conexão
  - [ ] Testes
- [ ] **WooCommerce**
  - [ ] Criar `WooCommerceIntegration`
  - [ ] Implementar autenticação (Consumer Key/Secret)
  - [ ] Implementar `testConnection()`
  - [ ] Implementar `listProducts()`
  - [ ] Implementar `createOrder()`
  - [ ] Implementar `getOrderStatus()`
  - [ ] Criar view de conexão
  - [ ] Testes

### Tráfego
- [ ] **Meta Business CAPI**
  - [ ] Criar `MetaBusinessCAPIIntegration`
  - [ ] Implementar `testConnection()`
  - [ ] Implementar `sendConversionEvent()`
  - [ ] Suportar eventos: Lead, Purchase, AddToCart
  - [ ] Implementar deduplicação (event_id)
  - [ ] Criar view de conexão
  - [ ] Testes
- [ ] **Google Ads API**
  - [ ] Criar `GoogleAdsIntegration`
  - [ ] Implementar OAuth 2.0
  - [ ] Implementar `testConnection()`
  - [ ] Implementar `uploadOfflineConversion()`
  - [ ] Associar a GCLID
  - [ ] Criar view de conexão
  - [ ] Testes
- [ ] **Google Analytics 4**
  - [ ] Criar `GoogleAnalytics4Integration`
  - [ ] Implementar `testConnection()`
  - [ ] Implementar `sendEvent()`
  - [ ] Suportar eventos: generate_lead, purchase
  - [ ] Criar view de conexão
  - [ ] Testes

### Suporte
- [ ] **Zendesk**
  - [ ] Criar `ZendeskIntegration`
  - [ ] Implementar autenticação (API Token ou OAuth)
  - [ ] Implementar `testConnection()`
  - [ ] Implementar `createTicket()`
  - [ ] Implementar `updateTicket()`
  - [ ] Implementar `addComment()`
  - [ ] Criar view de conexão
  - [ ] Testes

### Automação
- [ ] **Google Sheets**
  - [ ] Criar `GoogleSheetsIntegration`
  - [ ] Implementar OAuth 2.0 (Service Account)
  - [ ] Implementar `testConnection()`
  - [ ] Implementar `appendRow()`
  - [ ] Implementar `updateCell()`
  - [ ] Implementar `readData()`
  - [ ] Criar view de conexão
  - [ ] Testes
- [ ] **Pluga**
  - [ ] Criar `PlugaIntegration`
  - [ ] Implementar `sendWebhook()`
  - [ ] Configurar URL de webhook
  - [ ] Retry em caso de falha
  - [ ] Criar view de conexão
  - [ ] Testes
- [ ] **Webhook Genérico**
  - [ ] Criar `GenericWebhookIntegration`
  - [ ] Implementar configuração de URL
  - [ ] Implementar configuração de método (POST/PUT/PATCH)
  - [ ] Implementar configuração de headers
  - [ ] Implementar configuração de body (JSON/form-data)
  - [ ] Implementar mapeamento de variáveis
  - [ ] Retry automático
  - [ ] Logs de requisições
  - [ ] Criar view de conexão
  - [ ] Testes

### Geral para todas as integrações
- [ ] Adicionar ao `IntegrationRegistry`
- [ ] Criar contracts específicos se necessário
- [ ] Documentar endpoints e autenticação
- [ ] Criar seeders com exemplos
- [ ] Adicionar ícones das integrações

---

## 🟢 Analytics Básico

### Métricas do Dashboard Principal
- [ ] Implementar cálculo de "Contatos Iniciados"
  - [ ] Query: count de `conversation_sessions` (hoje, semana, mês)
  - [ ] Cache de 5 minutos
- [ ] Implementar cálculo de "Jornadas Interrompidas"
  - [ ] Query: count de sessões expiradas sem completar
- [ ] Implementar cálculo de "Leads Coletados"
  - [ ] Query: count de `leads` por período
- [ ] Implementar cálculo de "Taxa de Conversão"
  - [ ] Fórmula: (sessões completadas / sessões iniciadas) * 100

### Analytics por Fluxo
- [ ] Criar view `/dashboard/fluxes/{flux}/analytics`
  - [ ] Sessões iniciadas
  - [ ] Sessões completadas
  - [ ] Taxa de conclusão
  - [ ] Tempo médio de conclusão
  - [ ] Pontos de abandono (qual nó)
  - [ ] Leads gerados

### Gráficos
- [ ] Implementar gráfico de linha
  - [ ] Leads coletados por dia (últimos 30 dias)
  - [ ] Usar Chart.js ou similar
- [ ] Implementar gráfico de barra
  - [ ] Top 5 fluxos por conversão
- [ ] Implementar funil
  - [ ] Etapas do fluxo com drop-off
  - [ ] Visualizar onde usuários abandonam

### Otimizações
- [ ] Criar índices no banco para queries de analytics
- [ ] Implementar cache de métricas
  - [ ] Atualizar a cada 5 minutos
  - [ ] Usar Redis (futuro) ou cache de database
- [ ] Criar jobs assíncronos para cálculos pesados
  - [ ] `CalculateDailyMetrics`
  - [ ] Rodar diariamente via cron

---

## 🔵 Infraestrutura e DevOps

### Ambiente de Produção
- [ ] Configurar servidor de produção
  - [ ] Escolher provider (AWS, DigitalOcean, Fly.io)
  - [ ] Configurar domínio
  - [ ] Configurar SSL (Let's Encrypt)
- [ ] Configurar PostgreSQL em produção
  - [ ] Backup automático
  - [ ] Replicação (opcional)
- [ ] Configurar Redis (futuro)
  - [ ] Para cache
  - [ ] Para queue

### CI/CD
- [ ] Configurar GitHub Actions
  - [ ] Rodar testes automaticamente
  - [ ] Deploy automático em produção
  - [ ] Notificações de deploy

### Monitoring
- [ ] Configurar logs estruturados
  - [ ] Usar Laravel Log
  - [ ] Enviar para serviço externo (Papertrail, Logtail)
- [ ] Configurar error tracking
  - [ ] Sentry ou similar
  - [ ] Alertas de erros críticos
- [ ] Configurar uptime monitoring
  - [ ] UptimeRobot ou similar
  - [ ] Alertas se site cair

### Performance
- [ ] Otimizar queries do banco
  - [ ] Adicionar índices necessários
  - [ ] Usar eager loading
- [ ] Implementar cache
  - [ ] Cache de views
  - [ ] Cache de queries
- [ ] Configurar CDN para assets
  - [ ] Cloudflare ou similar

### Backup
- [ ] Configurar backup automático do PostgreSQL
  - [ ] Diário
  - [ ] Retenção de 30 dias
- [ ] Configurar backup de arquivos (se houver uploads)
- [ ] Testar restore de backup

### Segurança
- [ ] Implementar rate limiting
  - [ ] Em rotas de login
  - [ ] Em rotas de API
  - [ ] Em rotas de webhook
- [ ] Configurar CORS
- [ ] Implementar CSRF protection (já tem)
- [ ] Encriptar credenciais de integrações
  - [ ] Usar Laravel Encryption
- [ ] Configurar firewall
- [ ] Implementar 2FA (Pós-MVP)

---

## 📝 Documentação

### Para Desenvolvedores
- [ ] Documentar arquitetura do Flow Engine
- [ ] Documentar como adicionar novas integrações
- [ ] Documentar API de webhooks
- [ ] Criar guia de contribuição

### Para Usuários
- [ ] Criar documentação de uso
  - [ ] Como criar um fluxo
  - [ ] Como conectar integrações
  - [ ] Como gerenciar leads
- [ ] Criar vídeos tutoriais (opcional)
- [ ] Criar FAQ

---

## 🧪 Testes

### Unitários
- [ ] Testes de models
- [ ] Testes de services
- [ ] Testes de node processors
- [ ] Testes de integrações

### Integração
- [ ] Testes de fluxo completo
- [ ] Testes de webhooks
- [ ] Testes de pagamento

### End-to-End
- [ ] Teste: Cadastro → Pagamento → Ativação
- [ ] Teste: Criar fluxo → Ativar → Receber mensagem → Processar
- [ ] Teste: Lead → Sincronizar CRM
- [ ] Teste: Suspensão por inadimplência

### Carga
- [ ] Teste de 100 conversas simultâneas
- [ ] Teste de 1000 leads no banco
- [ ] Teste de múltiplos tenants

---

## 📊 Resumo por Prioridade

### 🔴 CRÍTICO (Bloqueadores do MVP)
1. Painel Admin e Sistema de Pagamentos
2. Flow Builder Visual
3. **Onboarding em 3 Etapas (Sprint 1.5)** — Detectado pós-Sprint 1
4. Engine de Execução de Fluxos

### 🟡 IMPORTANTE (Necessário para MVP funcional)
5. Gerenciamento de Fluxos
6. Bot WhatsApp - Melhorias

### 🟢 DESEJÁVEL (Adiciona valor ao MVP)
7. Integrações (12 novas)
8. Analytics Básico

### 🔵 INFRAESTRUTURA (Necessário para produção)
9. DevOps e Monitoring
10. Segurança
11. Backup

---

## 📅 Estimativa de Esforço

**Total estimado:** 13-16 semanas

- Sprint 0: Admin + Pagamentos (2 semanas) ✅
- Sprint 1: Flow Builder (2-3 semanas) ✅
- **Sprint 1.5: Onboarding em 3 Etapas (1-2 semanas)** ← Nova (detectada pós-Sprint 1)
- Sprint 2: Engine de Execução (2-3 semanas)
- Sprint 3: CRUD Fluxos + Bot (1-2 semanas)
- Sprint 4-5: Integrações (4 semanas)
- Sprint 6: Analytics + Polimento (1-2 semanas)
- Sprint 7: Testes e Homologação (1 semana)

> **Nota:** A Sprint 1.5 foi identificada como necessidade crítica após a conclusão da Sprint 1.
> O fluxo de cadastro/checkout atual (tela única) precisa ser dividido em 3 etapas para melhorar
> a UX de onboarding: (1) Dados do Dono, (2) Dados da Empresa (opcional), (3) Checkout.
> Inclui criação de tabela `companies`, validação de CPF/CNPJ, layout dedicado com stepper,
> busca de CEP via ViaCEP e máscaras de input.
> Ver especificação completa em `.sprints/1.5/01-spec.md`.

---

**Última atualização:** 06/02/2026  
**Próxima revisão:** Após conclusão de cada sprint
