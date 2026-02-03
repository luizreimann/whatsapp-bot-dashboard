# 01 - Mapeamento do Projeto: WhatsApp Bot Dashboard (Zaptria)

**Data de criação:** 03/02/2026  
**Versão:** 1.0  
**Status do Projeto:** Em desenvolvimento - MVP funcional

---

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Arquitetura do Sistema](#arquitetura-do-sistema)
3. [Stack Tecnológica](#stack-tecnológica)
4. [Estrutura do Banco de Dados](#estrutura-do-banco-de-dados)
5. [Módulos Implementados](#módulos-implementados)
6. [Integrações](#integrações)
7. [Rotas e Endpoints](#rotas-e-endpoints)
8. [Frontend](#frontend)
9. [Próximos Passos](#próximos-passos)

---

## 🎯 Visão Geral

**Zaptria** é um SaaS multi-tenant de automação para WhatsApp com criação de fluxos conversacionais. O sistema permite que empresas (tenants) criem bots de WhatsApp, gerenciem leads, criem fluxos de conversação e integrem com CRMs e outras ferramentas.

### Conceito Principal

- **Multi-tenant:** Cada empresa (tenant) tem seus próprios dados isolados
- **WhatsApp Bot:** Integração com WhatsApp via container Node.js separado
- **Flow Builder:** Sistema de criação de fluxos conversacionais (em desenvolvimento)
- **Lead Management:** Captura e gestão de leads via conversas
- **Integrações:** Conexão com CRMs (RD Station, Pipedrive) e outras ferramentas

---

## 🏗️ Arquitetura do Sistema

### Componentes Principais

```
┌─────────────────────────────────────────────────────────────┐
│                    USUÁRIO (Browser)                         │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              DASHBOARD (Laravel 12 + Blade)                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ Autenticação │  │ Gerenciamento│  │  Integrações │      │
│  │              │  │  de Leads    │  │   (CRMs)     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│  ┌──────────────┐  ┌──────────────┐                        │
│  │ Gerenciamento│  │ Flow Builder │                        │
│  │  de Fluxos   │  │ (planejado)  │                        │
│  └──────────────┘  └──────────────┘                        │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                  BANCO DE DADOS (SQLite)                     │
│  • Tenants  • Users  • WhatsappInstances  • Leads          │
│  • Fluxes   • IntegrationAccounts                           │
└─────────────────────────────────────────────────────────────┘
                     ▲
                     │
                     │ API Webhooks
                     │
┌────────────────────┴────────────────────────────────────────┐
│           WHATSAPP BOT (Container Node.js)                   │
│  • Gerencia conexão WhatsApp (whatsapp-web.js)             │
│  • Envia QR Code, Status, Mensagens para Dashboard         │
│  • Recebe ações do Dashboard                                │
└─────────────────────────────────────────────────────────────┘
                     ▲
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                      WHATSAPP                                │
└─────────────────────────────────────────────────────────────┘
```

### Fluxo de Comunicação

1. **Dashboard → Bot:** Comandos e configurações
2. **Bot → Dashboard:** Webhooks (QR Code, Status, Mensagens recebidas)
3. **Dashboard → CRMs:** Sincronização de leads
4. **Usuário → Dashboard:** Interface web para gerenciamento

---

## 🛠️ Stack Tecnológica

### Backend
- **Framework:** Laravel 12 (PHP 8.2+)
- **Database:** PostgreSQL 16
- **Queue:** Database driver (jobs assíncronos)
- **Cache:** Database driver
- **Session:** Database driver

### Frontend
- **Template Engine:** Blade (Laravel)
- **CSS Framework:** TailwindCSS 4.0
- **JavaScript:** Vanilla JS + Vite
- **Icons:** Font Awesome
- **Build Tool:** Vite 7.0

### DevOps
- **Containerização:** Docker + Docker Compose
  - **App Container:** PHP 8.2-FPM com extensões PostgreSQL
  - **Web Server:** Nginx Alpine
  - **Database:** PostgreSQL 16
  - **Network:** Bridge network isolada (app-net)
- **Deployment:** Fly.io (planejado para bot containers)
- **Development:** Concurrently (servidor + queue + logs + vite)

### Integrações Externas
- **WhatsApp:** whatsapp-web.js (via container Node.js separado)
- **CRMs:** RD Station CRM, Pipedrive
- **Planejado:** Mailchimp, Mercado Pago, Nuvemshop, Google Sheets, etc.

---

## � Infraestrutura Docker

### Arquitetura de Containers

O projeto utiliza Docker Compose com 3 serviços principais:

#### 1. **app** (PHP-FPM)
- **Imagem Base:** `php:8.2-fpm`
- **Container:** `whatsapp-dashboard-app`
- **Função:** Processa requisições PHP via FastCGI
- **Extensões PHP:**
  - `pdo`, `pdo_pgsql`, `pgsql` (PostgreSQL)
  - `zip` (manipulação de arquivos)
- **Volumes:**
  - `./:/var/www/html` (código fonte montado)
- **Dependências:** `db` (PostgreSQL)
- **Working Directory:** `/var/www/html`

#### 2. **nginx** (Web Server)
- **Imagem:** `nginx:alpine`
- **Container:** `whatsapp-dashboard-nginx`
- **Porta:** `8080:80` (host:container)
- **Função:** Servidor web que encaminha requisições PHP para o container app
- **Configuração:** `docker/nginx/default.conf`
  - Root: `/var/www/html/public`
  - FastCGI Pass: `app:9000`
  - Try files: Suporte para rotas do Laravel
- **Volumes:**
  - Código fonte
  - Configuração customizada do Nginx
- **Dependências:** `app`

#### 3. **db** (PostgreSQL)
- **Imagem:** `postgres:16`
- **Container:** `whatsapp-dashboard-db`
- **Porta:** `5432:5432`
- **Credenciais:**
  - Database: `laravel`
  - User: `laravel`
  - Password: `laravel`
- **Volume Persistente:** `pgdata` (dados do PostgreSQL)
- **Função:** Banco de dados relacional

### Network
- **Nome:** `app-net`
- **Driver:** Bridge
- **Função:** Isolamento e comunicação entre containers

### Volumes
- **pgdata:** Persistência dos dados do PostgreSQL

### Como Usar

```bash
# Subir todos os containers
docker-compose up -d

# Ver logs
docker-compose logs -f

# Acessar container app
docker-compose exec app bash

# Rodar migrations
docker-compose exec app php artisan migrate

# Parar containers
docker-compose down

# Parar e remover volumes (CUIDADO: apaga dados)
docker-compose down -v
```

### Acesso
- **Dashboard:** http://localhost:8080
- **PostgreSQL:** localhost:5432

---

## �️ Estrutura do Banco de Dados

### Tabelas Principais

#### 1. **tenants**
Representa cada empresa/cliente do SaaS.

```sql
- id
- name (nome da empresa)
- slug (identificador único)
- status (active, inactive)
- created_at, updated_at
```

#### 2. **users**
Usuários do sistema, vinculados a um tenant.

```sql
- id
- tenant_id (FK)
- name
- email (único)
- password
- role (admin, user - planejado)
- remember_token
- created_at, updated_at
```

#### 3. **whatsapp_instances**
Instância do bot WhatsApp de cada tenant (1:1 com tenant).

```sql
- id
- tenant_id (FK, único)
- status (inactive, pending_local, starting, qr_ready, connected, disconnected, error)
- bot_token (token de autenticação único)
- number (número WhatsApp conectado)
- fly_app_name (nome do app no Fly.io)
- public_url (URL pública do container)
- last_status_payload (JSON com último status)
- last_connected_at
- created_at, updated_at
```

#### 4. **fluxes**
Fluxos conversacionais criados pelo tenant.

```sql
- id
- tenant_id (FK)
- name
- status (draft, active, inactive)
- data (JSON: nodes, edges, version, description)
- conversion_goal (meta de conversão)
- created_at, updated_at
```

#### 5. **leads**
Leads capturados via conversas no WhatsApp.

```sql
- id
- tenant_id (FK)
- flux_id (FK, nullable)
- name
- phone (obrigatório)
- email
- source (origem do lead)
- status (new, qualified, in_progress, lost)
- data (JSON: notas, UTMs, dados customizados)
- created_at, updated_at
- INDEX: (tenant_id, phone)
```

#### 6. **integration_accounts**
Contas de integração conectadas (CRMs, etc).

```sql
- id
- tenant_id (FK)
- category (crm, email_marketing, payment, ecommerce, traffic, support, automation)
- provider (rd_station_crm, pipedrive, mailchimp, etc)
- name (nome amigável da conexão)
- config (JSON: tokens, keys - será encriptado)
- metadata (JSON: dados adicionais)
- status (connected, disconnected, error, pending_auth)
- connected_at
- last_synced_at
- created_at, updated_at
- UNIQUE: (tenant_id, provider)
```

#### 7. **Tabelas do Sistema**
- **cache:** Cache do Laravel
- **cache_locks:** Locks de cache
- **jobs:** Fila de jobs
- **job_batches:** Batches de jobs
- **failed_jobs:** Jobs que falharam
- **sessions:** Sessões de usuário

---

## 📦 Módulos Implementados

### ✅ 1. Autenticação
**Status:** Implementado

**Arquivos:**
- `app/Http/Controllers/Auth/AuthController.php`
- `resources/views/auth/login.blade.php`

**Funcionalidades:**
- Login com email/senha
- Logout com proteção CSRF
- Sessão persistente
- Middleware de autenticação

---

### ✅ 2. Multi-tenancy
**Status:** Implementado (básico)

**Modelo:**
- Cada usuário pertence a um tenant
- Isolamento de dados por `tenant_id`
- Relacionamentos configurados nos models

**Escopo:**
- Todos os queries filtram por `tenant_id` do usuário autenticado
- Proteção contra acesso cross-tenant

---

### ✅ 3. Dashboard Principal
**Status:** Implementado

**Arquivos:**
- `app/Http/Controllers/Dashboard/DashboardController.php`
- `resources/views/dashboard/index.blade.php`

**Funcionalidades:**
- Visão geral do status do robô WhatsApp
- Métricas rápidas (contatos iniciados, jornadas interrompidas, leads coletados)
- Atalhos para módulos principais
- Theme toggle (dark/light mode)

---

### ✅ 4. Gerenciamento de Leads
**Status:** Implementado

**Arquivos:**
- `app/Http/Controllers/Dashboard/LeadController.php`
- `resources/views/dashboard/leads/`
  - `index.blade.php` (listagem)
  - `show.blade.php` (detalhes)
  - `partials/table.blade.php` (tabela AJAX)

**Funcionalidades:**
- ✅ Listagem de leads com paginação
- ✅ Filtros por fluxo, status, data
- ✅ Ordenação por colunas
- ✅ Visualização de detalhes do lead
- ✅ Edição inline de notas
- ✅ AJAX para atualização sem reload
- ✅ Badges de status coloridos

**Enums:**
- `LeadStatus`: NEW, QUALIFIED, IN_PROGRESS, LOST

---

### ✅ 5. Gerenciamento do Bot WhatsApp
**Status:** Implementado (básico)

**Arquivos:**
- `app/Http/Controllers/Dashboard/BotController.php`
- `resources/views/dashboard/bot/index.blade.php`

**Funcionalidades:**
- Visualização do status da instância WhatsApp
- Exibição de QR Code (quando disponível)
- Informações de conexão

---

### ✅ 6. API de Webhooks (Bot → Dashboard)
**Status:** Implementado

**Arquivos:**
- `app/Http/Controllers/Api/WhatsappWebhookController.php`
- `app/Services/Whatsapp/WhatsappWebhookService.php`
- `routes/api.php`

**Endpoints:**
```
POST /api/tenants/{tenant}/whatsapp/qr
POST /api/tenants/{tenant}/whatsapp/status
POST /api/tenants/{tenant}/whatsapp/incoming
```

**Autenticação:**
- Header `X-Bot-Token` validado contra `whatsapp_instances.bot_token`

**Funcionalidades:**
- ✅ Recebimento de QR Code do bot
- ✅ Atualização de status (connected, disconnected, etc)
- ✅ Recebimento de mensagens (estrutura pronta, processamento pendente)

---

### ✅ 7. Sistema de Integrações
**Status:** Implementado (arquitetura + 2 CRMs)

**Arquitetura:**
```
app/Integrations/
├── Contracts/
│   ├── IntegrationInterface.php (interface base)
│   └── CrmIntegrationContract.php (contrato para CRMs)
├── Crm/
│   ├── RdStationCrmIntegration.php (✅ implementado)
│   └── PipedriveCrmIntegration.php (✅ implementado)
└── IntegrationRegistry.php (registro central)
```

**Enums:**
- `IntegrationCategory`: CRM, EMAIL_MARKETING, PAYMENT, ECOMMERCE, TRAFFIC, SUPPORT, AUTOMATION
- `IntegrationProvider`: RD_STATION_CRM, PIPEDRIVE, MAILCHIMP, MERCADO_PAGO, etc. (14 providers planejados)

**CRMs Implementados:**

#### RD Station CRM
- ✅ Autenticação via API Token
- ✅ Teste de conexão
- ✅ Sincronização de leads (criar contatos)
- ✅ Logging estruturado
- ✅ Tratamento de erros

#### Pipedrive
- ✅ Autenticação via API Token
- ✅ Teste de conexão
- ✅ Sincronização de leads (criar pessoas + deals)
- ✅ Mapeamento de campos customizados
- ✅ Logging estruturado

**Controllers:**
- `app/Http/Controllers/Dashboard/Integrations/IntegrationController.php`

**Views:**
- `resources/views/dashboard/integrations/index.blade.php` (lista de integrações)
- `resources/views/dashboard/integrations/connect.blade.php` (formulário de conexão)

**Funcionalidades:**
- ✅ Listagem de integrações disponíveis
- ✅ Conexão de novas integrações
- ✅ Desconexão de integrações
- ✅ Teste de conexão
- ✅ Armazenamento seguro de credenciais (JSON)

---

### 🔄 8. Fluxos Conversacionais
**Status:** Estrutura criada, builder pendente

**Arquivos:**
- `app/Models/Flux.php`
- Migration: `create_fluxes_table.php`

**Estrutura de Dados:**
```json
{
  "nodes": [],
  "edges": [],
  "version": 1,
  "description": "Descrição do fluxo"
}
```

**Pendente:**
- Flow builder visual (drag & drop)
- Engine de execução de fluxos
- Tipos de nós (mensagem, pergunta, condição, ação, integração)
- Validação de fluxos

---

## 🔌 Integrações

### Implementadas

#### 1. RD Station CRM
- **Categoria:** CRM
- **Auth Type:** API Token
- **Funcionalidades:**
  - Criar/atualizar contatos
  - Adicionar notas com origem do lead
  - Incluir UTMs nos dados do contato

#### 2. Pipedrive
- **Categoria:** CRM
- **Auth Type:** API Token
- **Funcionalidades:**
  - Criar pessoas (contacts)
  - Criar negócios (deals)
  - Vincular pessoa ao negócio
  - Campos customizados

### Planejadas (estrutura pronta)

**Email Marketing:**
- Mailchimp

**Pagamentos:**
- Mercado Pago
- Pagar.me

**E-commerce:**
- Nuvemshop
- WooCommerce

**Tráfego:**
- Meta Business CAPI
- Google Ads
- Google Analytics 4

**Suporte:**
- Zendesk

**Automação:**
- Google Sheets
- Pluga
- Webhook genérico

---

## 🛣️ Rotas e Endpoints

### Rotas Web (`routes/web.php`)

#### Autenticação
```
GET  /login           → AuthController@showLoginForm
POST /login           → AuthController@login
POST /logout          → AuthController@logout
```

#### Dashboard (protegido por auth)
```
GET  /dashboard                           → DashboardController@index
GET  /dashboard/bot                       → BotController@index
GET  /dashboard/leads                     → LeadController@index
GET  /dashboard/leads/data                → LeadController@data (AJAX)
GET  /dashboard/leads/lead/{lead}         → LeadController@show
PATCH /dashboard/leads/{lead}/notes       → LeadController@updateNotes
GET  /dashboard/logout                    → DashboardController@logout
```

#### Integrações
```
GET    /dashboard/integrations                    → IntegrationController@index
GET    /dashboard/integrations/connect/{provider} → IntegrationController@showConnectForm
POST   /dashboard/integrations/connect/{provider} → IntegrationController@connect
DELETE /dashboard/integrations/{account}          → IntegrationController@disconnect
```

### Rotas API (`routes/api.php`)

#### Webhooks do Bot
```
POST /api/tenants/{tenant}/whatsapp/qr       → WhatsappWebhookController@qr
POST /api/tenants/{tenant}/whatsapp/status   → WhatsappWebhookController@status
POST /api/tenants/{tenant}/whatsapp/incoming → WhatsappWebhookController@incoming
```

**Autenticação:** Header `X-Bot-Token`

---

## 🎨 Frontend

### Layout
- **Base:** `resources/views/layouts/app.blade.php`
- **Sidebar:** Navegação principal
- **Topbar:** Informações do usuário, theme toggle
- **Responsivo:** Mobile-friendly

### Componentes JavaScript

#### 1. Theme Toggle (`app.js`)
- Alternância entre dark/light mode
- Persistência em localStorage
- Ícone dinâmico

#### 2. Inline Edit (`components/inline-edit.js`)
- Edição inline de campos
- Salvamento via AJAX
- Feedback visual

#### 3. Copy to Clipboard (`components/copy.js`)
- Copiar textos com um clique
- Feedback de sucesso

### Estilos
- **TailwindCSS 4.0:** Utility-first CSS
- **Bootstrap Icons:** Via Font Awesome
- **Custom CSS:** `resources/css/`

### Assets
- **Build:** Vite
- **Hot Reload:** Disponível em desenvolvimento

---

## 🚀 Próximos Passos

### Prioridade Alta

#### 1. Flow Builder Visual
- [ ] Interface drag & drop para criar fluxos
- [ ] Tipos de nós:
  - Mensagem de texto
  - Pergunta (captura de resposta)
  - Condição (if/else)
  - Ação (salvar lead, enviar para CRM)
  - Integração (chamar API externa)
- [ ] Validação de fluxos
- [ ] Preview do fluxo

#### 2. Engine de Execução de Fluxos
- [ ] Máquina de estados para conversas
- [ ] Sessões de conversa (armazenar contexto)
- [ ] Processamento de mensagens recebidas
- [ ] Geração de respostas baseadas no fluxo
- [ ] Transições entre nós
- [ ] Timeout de sessão

#### 3. Gerenciamento de Fluxos
- [ ] CRUD completo de fluxos
- [ ] Ativar/desativar fluxos
- [ ] Duplicar fluxos
- [ ] Versionamento de fluxos
- [ ] Analytics por fluxo

### Prioridade Média

#### 4. Melhorias no Bot
- [ ] Gerenciamento de múltiplas instâncias por tenant
- [ ] Deploy automático de containers no Fly.io
- [ ] Logs de mensagens enviadas/recebidas
- [ ] Retry de mensagens falhadas
- [ ] Rate limiting

#### 5. Analytics e Relatórios
- [ ] Dashboard com métricas reais
- [ ] Funil de conversão
- [ ] Taxa de resposta
- [ ] Tempo médio de conversação
- [ ] Exportação de relatórios

#### 6. Mais Integrações
- [ ] Implementar integrações planejadas
- [ ] Webhook genérico
- [ ] Google Sheets
- [ ] Mailchimp
- [ ] Mercado Pago

### Prioridade Baixa

#### 7. Recursos Avançados
- [ ] Sistema de templates de mensagem
- [ ] Agendamento de mensagens
- [ ] Broadcast de mensagens
- [ ] Segmentação de leads
- [ ] Tags e categorias
- [ ] Campos customizados

#### 8. Administração
- [ ] Painel de admin multi-tenant
- [ ] Gerenciamento de usuários por tenant
- [ ] Roles e permissões
- [ ] Auditoria de ações
- [ ] Configurações por tenant

#### 9. Infraestrutura
- [ ] Redis para cache e queue (atualmente usando database driver)
- [ ] CDN para assets
- [ ] Backup automatizado do PostgreSQL
- [ ] Monitoring e alertas (Sentry, New Relic)
- [ ] Horizontal scaling (múltiplos workers)

---

## 📝 Notas Técnicas

### Seeders Disponíveis
- `InitialSetupSeeder`: Cria tenant, usuário, instância WhatsApp e 2 fluxos de exemplo
- `LeadsSeeder`: Cria leads de exemplo para testes

**Credenciais padrão:**
- Email: `admin@example.com`
- Senha: `password`

### Comandos Úteis
```bash
# Setup inicial
composer setup

# Desenvolvimento (servidor + queue + logs + vite)
composer dev

# Testes
composer test

# Seed inicial
php artisan db:seed --class=InitialSetupSeeder
```

### Variáveis de Ambiente Importantes
```
APP_NAME=Zaptria
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

---

## 🔒 Segurança

### Implementado
- ✅ Autenticação Laravel
- ✅ CSRF Protection
- ✅ Isolamento multi-tenant
- ✅ Validação de bot token
- ✅ Password hashing (bcrypt)

### Pendente
- [ ] Encriptação de credenciais de integração
- [ ] Rate limiting em APIs
- [ ] 2FA (autenticação de dois fatores)
- [ ] Logs de auditoria
- [ ] Política de senhas fortes

---

## 📚 Documentação Adicional

Esta é a primeira documentação do projeto. Documentos adicionais serão criados conforme necessário:

- `02-flow-engine.md` - Arquitetura do motor de fluxos
- `03-integrations-guide.md` - Guia para adicionar novas integrações
- `04-api-reference.md` - Referência completa da API
- `05-deployment.md` - Guia de deploy em produção

---

**Última atualização:** 03/02/2026  
**Mantido por:** Equipe de Desenvolvimento Zaptria
