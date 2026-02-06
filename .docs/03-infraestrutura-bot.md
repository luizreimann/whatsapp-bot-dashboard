# 03 - Infraestrutura do Bot WhatsApp (Multi-tenant)

**Data de criação:** 05/02/2026  
**Versão:** 1.0  
**Status:** Proposta Técnica  
**Objetivo:** Definir arquitetura de infraestrutura para containers Node.js do whatsapp-web.js

---

## 📋 Índice

1. [Contexto e Requisitos](#contexto-e-requisitos)
2. [Análise de Soluções](#análise-de-soluções)
3. [Arquitetura Recomendada](#arquitetura-recomendada)
4. [Especificações Técnicas](#especificações-técnicas)
5. [Implementação](#implementação)
6. [Custos e Escalabilidade](#custos-e-escalabilidade)
7. [Monitoramento e Manutenção](#monitoramento-e-manutenção)
8. [Roadmap de Implementação](#roadmap-de-implementação)

---

## 🎯 Contexto e Requisitos

### Visão Geral

O **Zaptria** é um SaaS multi-tenant onde cada cliente (tenant) precisa de sua própria instância do bot WhatsApp rodando em um container Node.js isolado. A infraestrutura precisa suportar:

- Provisionamento dinâmico de containers
- Isolamento completo entre tenants
- Persistência de sessões WhatsApp
- Comunicação via webhooks com o dashboard Laravel
- Escalabilidade horizontal conforme crescimento

### Requisitos Funcionais

#### RF01: Isolamento por Tenant
- Cada tenant deve ter **1 container Node.js dedicado**
- Container executa `whatsapp-web.js` + Puppeteer
- Sessão WhatsApp isolada (QR Code único)
- Autenticação via `bot_token` único

#### RF02: Persistência de Sessão
- Sessões WhatsApp devem persistir entre restarts
- Volumes persistentes para diretório `.wwebjs_auth`
- Backup automático de sessões
- Recuperação em caso de falha

#### RF03: Provisionamento Dinâmico
- Criar container automaticamente após pagamento confirmado
- Destruir container ao cancelar assinatura
- API para gerenciar lifecycle dos containers
- Deploy sem downtime

#### RF04: Comunicação
- Webhooks do bot → dashboard Laravel
- Endpoints: `/qr`, `/status`, `/incoming`
- Autenticação via header `X-Bot-Token`
- Retry automático em falhas

#### RF05: Escalabilidade
- Suportar 50+ tenants no MVP (3 meses)
- Escalar para centenas de tenants (6-12 meses)
- Adicionar servidores horizontalmente
- Load balancing entre servidores

### Requisitos Não-Funcionais

#### RNF01: Performance
- Tempo de provisionamento: <2 minutos
- Latência de webhook: <500ms
- Uptime por container: >99.5%
- Consumo de RAM por container: 200-500MB

#### RNF02: Custo
- Modelo de negócio: R$ 297/mês por tenant
- Margem alvo: >60% (custo infra <R$ 120/tenant)
- Custo ideal por container: <R$ 10/mês
- Otimização de recursos

#### RNF03: Segurança
- Isolamento de rede entre containers
- Credenciais encriptadas
- SSL/TLS obrigatório
- Rate limiting

#### RNF04: Confiabilidade
- Health checks periódicos (1 min)
- Auto-restart em caso de falha
- Logs centralizados
- Alertas em tempo real

---

## 🔍 Análise de Soluções

### Opção 1: Fly.io (Proposta Original)

**Descrição:** PaaS especializado em edge computing com deploy global.

#### Prós
- ✅ Deploy extremamente simples (`flyctl deploy`)
- ✅ Escala automática e global
- ✅ Volumes persistentes nativos
- ✅ HTTPS automático
- ✅ Excelente DX (Developer Experience)
- ✅ Free tier (3 VMs shared-cpu)

#### Contras
- ❌ **Custo elevado:** ~$10-15/mês por container
- ❌ **Inviável para multi-tenant:** 50 tenants = $500-750/mês (~R$ 2.500-3.750)
- ❌ **Margem comprometida:** 50-60% do MRR apenas em infra
- ❌ Não é a solução mais econômica

#### Custos Detalhados
```
Container shared-cpu-1x (256MB RAM): $1.94/mês
Volume 1GB: $0.15/mês
Custo por tenant: ~$2.09/mês (~R$ 10,50)

50 tenants: $104.50/mês (~R$ 525)
100 tenants: $209/mês (~R$ 1.050)
```

**Veredicto:** ❌ **Não recomendado** - Custo por container inviabiliza escala

---

### Opção 2: VPS com Docker (Recomendação Principal)

**Descrição:** VPS dedicado com Docker Compose/Swarm para orquestrar múltiplos containers.

#### Prós
- ✅ **Custo 10-20x menor** que Fly.io
- ✅ Recursos dedicados (CPU, RAM, Storage)
- ✅ Controle total da infraestrutura
- ✅ Fácil escalar horizontalmente (adicionar VPS)
- ✅ Volumes locais para persistência
- ✅ Sem vendor lock-in

#### Contras
- ⚠️ Requer gerenciamento manual
- ⚠️ Responsabilidade por backups e segurança
- ⚠️ Monitoramento precisa ser configurado
- ⚠️ Sem escala automática nativa

#### Providers Recomendados

##### 1. Hetzner Cloud (Alemanha) 🏆
**Melhor custo/benefício**

```
CPX31: 4 vCPU, 8GB RAM, 160GB SSD
- Preço: €8.46/mês (~R$ 50)
- Capacidade: 20-30 containers
- Custo por tenant: R$ 1,67-2,50/mês

CPX41: 8 vCPU, 16GB RAM, 240GB SSD
- Preço: €15.30/mês (~R$ 90)
- Capacidade: 40-50 containers
- Custo por tenant: R$ 1,80-2,25/mês

CCX33: 8 vCPU, 32GB RAM, 240GB SSD
- Preço: €30/mês (~R$ 180)
- Capacidade: 80-100 containers
- Custo por tenant: R$ 1,80-2,25/mês
```

##### 2. Contabo (Alemanha)
**Mais barato**

```
Cloud VPS M: 6 vCPU, 16GB RAM, 400GB SSD
- Preço: €6.99/mês (~R$ 42)
- Capacidade: 40-50 containers
- Custo por tenant: R$ 0,84-1,05/mês
```

##### 3. DigitalOcean (EUA)
**Mais confiável, porém mais caro**

```
Droplet 8GB: 4 vCPU, 8GB RAM, 160GB SSD
- Preço: $48/mês (~R$ 240)
- Capacidade: 20-30 containers
- Custo por tenant: R$ 8-12/mês
```

**Veredicto:** ✅ **RECOMENDADO** - Melhor custo/benefício para MVP

---

### Opção 3: Dokploy (PaaS Self-hosted) 🏆

**Descrição:** PaaS open-source que você instala em seu próprio VPS, similar ao Heroku/Fly.io.

#### O que é Dokploy?

Dokploy é uma plataforma de deploy self-hosted que:
- Gerencia containers Docker via interface web
- Fornece deploy via Git ou Docker Registry
- Inclui proxy reverso automático (Traefik)
- Gera SSL automático (Let's Encrypt)
- Monitora containers em tempo real
- Centraliza logs

#### Prós
- ✅ **Mesma economia do VPS** (roda em cima de VPS)
- ✅ **Interface visual** para gerenciar containers
- ✅ **Deploy simplificado** (Git push ou API REST)
- ✅ **SSL automático** com Traefik
- ✅ **Monitoramento integrado**
- ✅ **Logs centralizados**
- ✅ **API REST completa** para automação
- ✅ **Melhor DX** que Docker puro
- ✅ **Open-source** (sem vendor lock-in)

#### Contras
- ⚠️ Camada adicional de abstração
- ⚠️ Menos maduro que soluções enterprise
- ⚠️ Comunidade menor que Docker/K8s
- ⚠️ Documentação ainda em desenvolvimento

#### Arquitetura Dokploy

```
VPS (Hetzner/Contabo)
├── Dokploy Dashboard (UI Web)
├── Traefik (Reverse Proxy + SSL)
├── PostgreSQL (Metadados do Dokploy)
└── Containers de Aplicação
    ├── Bot Tenant 1
    ├── Bot Tenant 2
    └── Bot Tenant N
```

#### API REST do Dokploy

```bash
# Criar aplicação
POST /api/application.create
{
  "name": "bot-tenant-123",
  "appName": "bot-tenant-123",
  "description": "Bot WhatsApp - Tenant 123",
  "env": "BOT_TOKEN=abc123\nDASHBOARD_URL=https://app.zaptria.com",
  "memoryLimit": 512,
  "cpuLimit": 0.5,
  "dockerImage": "registry.zaptria.com/whatsapp-bot:latest"
}

# Deletar aplicação
POST /api/application.remove
{
  "appName": "bot-tenant-123"
}

# Obter status
POST /api/application.one
{
  "appName": "bot-tenant-123"
}
```

**Veredicto:** ✅ **ALTAMENTE RECOMENDADO** - Combina economia do VPS com DX do PaaS

---

### Opção 4: Coolify (Alternativa ao Dokploy)

**Descrição:** PaaS self-hosted open-source, mais maduro que Dokploy.

#### Prós
- ✅ Mesmas vantagens do Dokploy
- ✅ Mais maduro e estável
- ✅ Comunidade maior (20k+ stars GitHub)
- ✅ Suporte a múltiplos servidores
- ✅ Melhor documentação

#### Contras
- ⚠️ Pode ser mais pesado (overhead)
- ⚠️ Interface mais complexa
- ⚠️ Curva de aprendizado inicial

**Veredicto:** ✅ **RECOMENDADO** - Alternativa sólida ao Dokploy

---

### Opção 5: Kubernetes (K3s/MicroK8s)

**Descrição:** Orquestração profissional de containers.

#### Prós
- ✅ Orquestração enterprise-grade
- ✅ Auto-scaling nativo
- ✅ Health checks e self-healing
- ✅ Preparado para escala massiva

#### Contras
- ❌ **Complexidade excessiva para MVP**
- ❌ Overhead de recursos (etcd, control plane)
- ❌ Curva de aprendizado íngreme
- ❌ Overkill para 50-100 tenants
- ❌ Requer DevOps especializado

**Veredicto:** ❌ **Não recomendado para MVP** - Guardar para Fase 3 (Escala Enterprise)

---

## 🏗️ Arquitetura Recomendada

### Solução Escolhida: VPS + Dokploy

**Justificativa:**
- ✅ Melhor custo/benefício (R$ 1,80-2,50 por tenant)
- ✅ DX excelente (interface + API)
- ✅ Escalabilidade horizontal simples
- ✅ Manutenção reduzida
- ✅ Open-source (sem lock-in)

### Diagrama de Arquitetura

```
┌─────────────────────────────────────────────────────────────┐
│                  USUÁRIO (WhatsApp)                         │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              DASHBOARD LARAVEL (Servidor 1)                 │
│  ┌────────────────────────────────────────────────────┐     │
│  │  - Gerencia tenants e assinaturas                  │     │
│  │  - Cria/destrói containers via API Dokploy         │     │
│  │  - Recebe webhooks dos bots                        │     │
│  │  - PostgreSQL (dados dos tenants)                  │     │
│  └────────────────────────────────────────────────────┘     │
└────────────────────┬────────────────────────────────────────┘
                     │ API REST
                     │ Webhooks
                     ▼
┌─────────────────────────────────────────────────────────────┐
│         VPS DOKPLOY (Hetzner CPX41 - Servidor 2)            │
│  ┌────────────────────────────────────────────────────┐     │
│  │              Dokploy Dashboard                      │     │
│  │  - Interface Web (porta 3000)                      │     │
│  │  - API REST para automação                         │     │
│  │  - PostgreSQL (metadados Dokploy)                  │     │
│  └────────────────────────────────────────────────────┘     │
│  ┌────────────────────────────────────────────────────┐     │
│  │  Traefik (Reverse Proxy + SSL)                     │     │
│  │  - bot-tenant-1.bots.zaptria.com → Container 1    │     │
│  │  - bot-tenant-2.bots.zaptria.com → Container 2    │     │
│  │  - bot-tenant-N.bots.zaptria.com → Container N    │     │
│  └────────────────────────────────────────────────────┘     │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐    │
│  │   Bot    │  │   Bot    │  │   Bot    │  │   Bot    │    │
│  │ Tenant 1 │  │ Tenant 2 │  │ Tenant 3 │  │ Tenant N │    │
│  │ Node.js  │  │ Node.js  │  │ Node.js  │  │ Node.js  │    │
│  │ 256MB    │  │ 256MB    │  │ 256MB    │  │ 256MB    │    │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘    │
│  ┌────────────────────────────────────────────────────┐     │
│  │  Volume Storage (Sessões WhatsApp)                 │     │
│  │  /var/lib/dokploy/volumes/bot-tenant-*/session     │     │
│  └────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────┘
```

### Fluxo de Provisionamento

```
1. Tenant paga assinatura
   ↓
2. Webhook Stripe → Dashboard Laravel
   ↓
3. TenantProvisioningService::provision()
   ↓
4. API POST /api/application.create → Dokploy
   ↓
5. Dokploy cria container Docker
   ↓
6. Traefik configura SSL + domínio
   ↓
7. Bot inicia e envia webhook /qr
   ↓
8. Dashboard recebe QR Code
   ↓
9. Tenant escaneia QR Code
   ↓
10. Bot conecta e envia webhook /status
    ↓
11. WhatsappInstance.status = 'connected'
```

### Fluxo de Comunicação

```
WhatsApp → Bot Container → Webhook → Dashboard Laravel
                ↓
          Processa mensagem
                ↓
          FlowEngine executa
                ↓
          Gera resposta
                ↓
          Webhook → Bot Container → WhatsApp
```

---

## 🔧 Especificações Técnicas

### Servidor 1: Dashboard Laravel

**Função:** Aplicação principal (já existente)

**Especificações:**
- **Provider:** Qualquer (DigitalOcean, AWS, Hetzner)
- **Recursos:** 2 vCPU, 4GB RAM, 80GB SSD
- **Stack:** Laravel 12, PostgreSQL 16, Nginx
- **Custo:** ~R$ 100-200/mês

**Não requer alterações na infraestrutura atual.**

---

### Servidor 2: VPS Dokploy (Bots)

**Função:** Hospedar containers dos bots WhatsApp

#### Especificações Recomendadas

##### Fase 1: MVP (0-50 tenants)
```
Provider: Hetzner Cloud
Plano: CPX41
- vCPU: 8 cores
- RAM: 16GB
- Storage: 240GB SSD
- Tráfego: 20TB/mês
- Preço: €15.30/mês (~R$ 90)
- Capacidade: 40-50 containers
```

##### Fase 2: Crescimento (50-100 tenants)
```
Adicionar 1 VPS idêntico
- Total: 2 VPS CPX41
- Capacidade: 80-100 containers
- Custo: €30.60/mês (~R$ 180)
```

##### Fase 3: Escala (100-200 tenants)
```
Migrar para CCX33 ou adicionar mais CPX41
- CCX33: 8 vCPU, 32GB RAM, 240GB SSD
- Preço: €30/mês (~R$ 180)
- Capacidade: 80-100 containers
```

#### Configuração do VPS

**Sistema Operacional:** Ubuntu 22.04 LTS

**Software Instalado:**
```bash
- Docker 24+
- Dokploy (última versão)
- Traefik 2.10+
- Fail2ban (segurança)
- UFW (firewall)
```

**Portas Abertas:**
```
22   - SSH (apenas IP do dashboard)
80   - HTTP (redirect para HTTPS)
443  - HTTPS (Traefik)
3000 - Dokploy Dashboard (apenas IP do dashboard)
```

---

### Container do Bot WhatsApp

#### Especificações por Container

```yaml
Image: registry.zaptria.com/whatsapp-bot:latest
Base: node:18-alpine

Resources:
  Memory: 256MB (limit: 512MB)
  CPU: 0.25 cores (limit: 0.5)
  Storage: 1GB (volume persistente)

Environment Variables:
  BOT_TOKEN: <token único do tenant>
  DASHBOARD_URL: https://app.zaptria.com
  TENANT_ID: <id do tenant>
  NODE_ENV: production
  
Volumes:
  - /app/.wwebjs_auth (sessão WhatsApp)
  - /app/.wwebjs_cache (cache)

Ports:
  - 3000 (interno, exposto via Traefik)

Health Check:
  Endpoint: GET /health
  Interval: 60s
  Timeout: 10s
  Retries: 3

Restart Policy: always
```

#### Dockerfile do Bot

```dockerfile
FROM node:18-alpine

# Instalar dependências do Puppeteer
RUN apk add --no-cache \
    chromium \
    nss \
    freetype \
    harfbuzz \
    ca-certificates \
    ttf-freefont \
    font-noto-emoji

# Configurar Puppeteer para usar Chromium instalado
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser \
    NODE_ENV=production

WORKDIR /app

# Copiar package files
COPY package*.json ./

# Instalar dependências
RUN npm ci --only=production && \
    npm cache clean --force

# Copiar código fonte
COPY . .

# Criar diretórios para volumes
RUN mkdir -p .wwebjs_auth .wwebjs_cache && \
    chown -R node:node /app

# Usar usuário não-root
USER node

# Expor porta
EXPOSE 3000

# Health check
HEALTHCHECK --interval=60s --timeout=10s --retries=3 \
  CMD node healthcheck.js || exit 1

# Comando de inicialização
CMD ["node", "index.js"]
```

---

### Configuração DNS

#### Domínio Principal
```
app.zaptria.com → IP do Dashboard Laravel
```

#### Subdomínio Wildcard para Bots
```
*.bots.zaptria.com → IP do VPS Dokploy

Exemplos:
- bot-tenant-1.bots.zaptria.com → Container Tenant 1
- bot-tenant-2.bots.zaptria.com → Container Tenant 2
- bot-tenant-123.bots.zaptria.com → Container Tenant 123
```

#### Configuração no Cloudflare (Recomendado)

```
Tipo: A
Nome: bots
Conteúdo: <IP_VPS_DOKPLOY>
Proxy: Desabilitado (DNS only)
TTL: Auto

Tipo: A
Nome: *.bots
Conteúdo: <IP_VPS_DOKPLOY>
Proxy: Desabilitado (DNS only)
TTL: Auto
```

**Nota:** Proxy desabilitado para permitir SSL via Let's Encrypt no Traefik.

---

## 💻 Implementação

### Fase 1: Setup Inicial do VPS

#### 1.1. Provisionar VPS na Hetzner

```bash
# Via Hetzner Cloud Console ou CLI
hcloud server create \
  --name dokploy-bots-1 \
  --type cpx41 \
  --image ubuntu-22.04 \
  --ssh-key <sua-chave-ssh>
```

#### 1.2. Configurar Firewall

```bash
# Conectar via SSH
ssh root@<IP_VPS>

# Atualizar sistema
apt update && apt upgrade -y

# Instalar UFW
apt install -y ufw

# Configurar regras
ufw default deny incoming
ufw default allow outgoing
ufw allow from <IP_DASHBOARD> to any port 22
ufw allow from <IP_DASHBOARD> to any port 3000
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
```

#### 1.3. Instalar Docker

```bash
# Instalar Docker
curl -fsSL https://get.docker.com | sh

# Adicionar usuário ao grupo docker
usermod -aG docker $USER

# Habilitar Docker no boot
systemctl enable docker
systemctl start docker
```

#### 1.4. Instalar Dokploy

```bash
# Instalar Dokploy (1 comando)
curl -sSL https://dokploy.com/install.sh | sh

# Aguardar instalação (2-3 minutos)
# Dokploy estará disponível em: http://<IP_VPS>:3000
```

#### 1.5. Configurar Dokploy

1. Acessar `http://<IP_VPS>:3000`
2. Criar conta admin
3. Configurar domínio wildcard: `*.bots.zaptria.com`
4. Habilitar SSL automático (Let's Encrypt)
5. Gerar API Token para integração

---

### Fase 2: Criar Imagem Docker do Bot

#### 2.1. Estrutura do Projeto Bot

```
whatsapp-bot/
├── Dockerfile
├── .dockerignore
├── package.json
├── index.js
├── healthcheck.js
├── src/
│   ├── bot.js
│   ├── webhooks.js
│   └── utils/
└── README.md
```

#### 2.2. Código do Bot (index.js)

```javascript
const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const axios = require('axios');

const app = express();
app.use(express.json());

const BOT_TOKEN = process.env.BOT_TOKEN;
const DASHBOARD_URL = process.env.DASHBOARD_URL;
const TENANT_ID = process.env.TENANT_ID;
const PORT = process.env.PORT || 3000;

// Cliente WhatsApp
const client = new Client({
  authStrategy: new LocalAuth({
    dataPath: './.wwebjs_auth'
  }),
  puppeteer: {
    executablePath: process.env.PUPPETEER_EXECUTABLE_PATH,
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-dev-shm-usage',
      '--disable-accelerated-2d-canvas',
      '--no-first-run',
      '--no-zygote',
      '--disable-gpu'
    ]
  }
});

// Evento: QR Code gerado
client.on('qr', async (qr) => {
  console.log('QR Code recebido');
  
  try {
    await axios.post(`${DASHBOARD_URL}/api/tenants/${TENANT_ID}/whatsapp/qr`, {
      qr_code: qr,
      timestamp: new Date().toISOString()
    }, {
      headers: { 'X-Bot-Token': BOT_TOKEN }
    });
  } catch (error) {
    console.error('Erro ao enviar QR Code:', error.message);
  }
});

// Evento: Cliente pronto
client.on('ready', async () => {
  console.log('Bot conectado!');
  
  const info = client.info;
  
  try {
    await axios.post(`${DASHBOARD_URL}/api/tenants/${TENANT_ID}/whatsapp/status`, {
      status: 'connected',
      number: info.wid.user,
      name: info.pushname,
      timestamp: new Date().toISOString()
    }, {
      headers: { 'X-Bot-Token': BOT_TOKEN }
    });
  } catch (error) {
    console.error('Erro ao enviar status:', error.message);
  }
});

// Evento: Mensagem recebida
client.on('message', async (message) => {
  console.log('Mensagem recebida:', message.from);
  
  try {
    await axios.post(`${DASHBOARD_URL}/api/tenants/${TENANT_ID}/whatsapp/incoming`, {
      from: message.from,
      body: message.body,
      timestamp: message.timestamp,
      type: message.type
    }, {
      headers: { 'X-Bot-Token': BOT_TOKEN }
    });
  } catch (error) {
    console.error('Erro ao enviar mensagem:', error.message);
  }
});

// Evento: Desconectado
client.on('disconnected', async (reason) => {
  console.log('Bot desconectado:', reason);
  
  try {
    await axios.post(`${DASHBOARD_URL}/api/tenants/${TENANT_ID}/whatsapp/status`, {
      status: 'disconnected',
      reason: reason,
      timestamp: new Date().toISOString()
    }, {
      headers: { 'X-Bot-Token': BOT_TOKEN }
    });
  } catch (error) {
    console.error('Erro ao enviar status:', error.message);
  }
});

// Inicializar cliente
client.initialize();

// Health check endpoint
app.get('/health', (req, res) => {
  const isReady = client.info !== null;
  res.status(isReady ? 200 : 503).json({
    status: isReady ? 'healthy' : 'starting',
    uptime: process.uptime(),
    timestamp: new Date().toISOString()
  });
});

// Endpoint para enviar mensagem (chamado pelo dashboard)
app.post('/send', async (req, res) => {
  const { token, to, message } = req.body;
  
  if (token !== BOT_TOKEN) {
    return res.status(401).json({ error: 'Unauthorized' });
  }
  
  try {
    await client.sendMessage(to, message);
    res.json({ success: true });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.listen(PORT, () => {
  console.log(`Bot rodando na porta ${PORT}`);
});
```

#### 2.3. Health Check (healthcheck.js)

```javascript
const http = require('http');

const options = {
  host: 'localhost',
  port: 3000,
  path: '/health',
  timeout: 5000
};

const request = http.request(options, (res) => {
  if (res.statusCode === 200) {
    process.exit(0);
  } else {
    process.exit(1);
  }
});

request.on('error', () => {
  process.exit(1);
});

request.end();
```

#### 2.4. Build e Push da Imagem

```bash
# Build
docker build -t registry.zaptria.com/whatsapp-bot:latest .

# Push para registry (Docker Hub, GitHub Registry, ou privado)
docker push registry.zaptria.com/whatsapp-bot:latest
```

---

### Fase 3: Integração Laravel ↔ Dokploy

#### 3.1. Service: BotProvisioningService

```php
<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\WhatsappInstance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BotProvisioningService
{
    private string $dokployUrl;
    private string $dokployToken;

    public function __construct()
    {
        $this->dokployUrl = config('services.dokploy.url');
        $this->dokployToken = config('services.dokploy.token');
    }

    /**
     * Provisionar container do bot para um tenant
     */
    public function provision(Tenant $tenant): WhatsappInstance
    {
        $botToken = Str::random(40);
        $appName = "bot-tenant-{$tenant->id}";
        $domain = "{$appName}.bots.zaptria.com";

        Log::info("Provisionando bot para tenant", [
            'tenant_id' => $tenant->id,
            'app_name' => $appName,
        ]);

        try {
            // Criar aplicação no Dokploy via API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->dokployToken}",
                'Content-Type' => 'application/json',
            ])->post("{$this->dokployUrl}/api/application.create", [
                'name' => $appName,
                'appName' => $appName,
                'description' => "Bot WhatsApp - Tenant {$tenant->name}",
                'env' => implode("\n", [
                    "BOT_TOKEN={$botToken}",
                    "DASHBOARD_URL=" . config('app.url'),
                    "TENANT_ID={$tenant->id}",
                    "NODE_ENV=production",
                ]),
                'memoryLimit' => 512,
                'cpuLimit' => 0.5,
                'dockerImage' => 'registry.zaptria.com/whatsapp-bot:latest',
                'domains' => [$domain],
            ]);

            if (!$response->successful()) {
                throw new \Exception("Erro ao criar aplicação no Dokploy: " . $response->body());
            }

            // Criar registro no banco
            $instance = WhatsappInstance::create([
                'tenant_id' => $tenant->id,
                'status' => 'starting',
                'bot_token' => $botToken,
                'public_url' => "https://{$domain}",
                'container_name' => $appName,
                'server' => 'dokploy-1',
            ]);

            Log::info("Bot provisionado com sucesso", [
                'instance_id' => $instance->id,
                'url' => $instance->public_url,
            ]);

            return $instance;

        } catch (\Exception $e) {
            Log::error("Erro ao provisionar bot", [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Destruir container do bot
     */
    public function destroy(WhatsappInstance $instance): void
    {
        Log::info("Destruindo bot", [
            'instance_id' => $instance->id,
            'container_name' => $instance->container_name,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->dokployToken}",
                'Content-Type' => 'application/json',
            ])->post("{$this->dokployUrl}/api/application.remove", [
                'appName' => $instance->container_name,
            ]);

            if (!$response->successful()) {
                Log::warning("Erro ao remover aplicação do Dokploy", [
                    'response' => $response->body(),
                ]);
            }

            $instance->delete();

            Log::info("Bot destruído com sucesso");

        } catch (\Exception $e) {
            Log::error("Erro ao destruir bot", [
                'instance_id' => $instance->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Obter status do container
     */
    public function getStatus(WhatsappInstance $instance): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->dokployToken}",
                'Content-Type' => 'application/json',
            ])->post("{$this->dokployUrl}/api/application.one", [
                'appName' => $instance->container_name,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['status' => 'unknown'];

        } catch (\Exception $e) {
            Log::error("Erro ao obter status do bot", [
                'instance_id' => $instance->id,
                'error' => $e->getMessage(),
            ]);

            return ['status' => 'error'];
        }
    }

    /**
     * Reiniciar container
     */
    public function restart(WhatsappInstance $instance): void
    {
        Log::info("Reiniciando bot", [
            'instance_id' => $instance->id,
        ]);

        try {
            Http::withHeaders([
                'Authorization' => "Bearer {$this->dokployToken}",
                'Content-Type' => 'application/json',
            ])->post("{$this->dokployUrl}/api/application.restart", [
                'appName' => $instance->container_name,
            ]);

            Log::info("Bot reiniciado com sucesso");

        } catch (\Exception $e) {
            Log::error("Erro ao reiniciar bot", [
                'instance_id' => $instance->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
```

#### 3.2. Configuração (config/services.php)

```php
'dokploy' => [
    'url' => env('DOKPLOY_URL', 'https://dokploy.zaptria.com'),
    'token' => env('DOKPLOY_TOKEN'),
],
```

#### 3.3. Variáveis de Ambiente (.env)

```env
DOKPLOY_URL=https://<IP_VPS>:3000
DOKPLOY_TOKEN=<token_gerado_no_dokploy>
```

#### 3.4. Migration: Adicionar Campos

```php
Schema::table('whatsapp_instances', function (Blueprint $table) {
    $table->string('container_name')->nullable()->after('bot_token');
    $table->string('server')->default('dokploy-1')->after('container_name');
});
```

#### 3.5. Uso no TenantProvisioningService

```php
public function provision(Tenant $tenant): void
{
    // ... código existente ...

    // Provisionar bot WhatsApp
    $botService = app(BotProvisioningService::class);
    $botService->provision($tenant);

    Log::info("Tenant provisionado completamente", [
        'tenant_id' => $tenant->id,
    ]);
}
```

---

### Fase 4: Monitoramento e Health Checks

#### 4.1. Job: CheckBotsHealth

```php
<?php

namespace App\Jobs;

use App\Models\WhatsappInstance;
use App\Services\BotProvisioningService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckBotsHealth implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(BotProvisioningService $botService): void
    {
        $instances = WhatsappInstance::whereIn('status', ['connected', 'qr_ready'])
            ->get();

        foreach ($instances as $instance) {
            try {
                $status = $botService->getStatus($instance);

                // Se container não está rodando, tentar reiniciar
                if ($status['status'] !== 'running') {
                    Log::warning("Container do bot não está rodando", [
                        'instance_id' => $instance->id,
                        'status' => $status['status'],
                    ]);

                    $botService->restart($instance);
                }

            } catch (\Exception $e) {
                Log::error("Erro ao verificar health do bot", [
                    'instance_id' => $instance->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
```

#### 4.2. Schedule (app/Console/Kernel.php)

```php
protected function schedule(Schedule $schedule): void
{
    // Verificar health dos bots a cada 5 minutos
    $schedule->job(new CheckBotsHealth)->everyFiveMinutes();
}
```

---

## 💰 Custos e Escalabilidade

### Análise de Custos por Fase

#### Fase 1: MVP (0-50 tenants)

```
Servidor 1: Dashboard Laravel
- Provider: DigitalOcean Droplet 4GB
- Custo: $24/mês (~R$ 120)

Servidor 2: VPS Dokploy (Bots)
- Provider: Hetzner CPX41
- Custo: €15.30/mês (~R$ 90)
- Capacidade: 40-50 containers

Backup Hetzner:
- Custo: €3/mês (~R$ 18)

Total Mensal: R$ 228
Receita (50 tenants × R$ 297): R$ 14.850
Custo por tenant: R$ 4,56
Margem: 98,5% 🎉
```

#### Fase 2: Crescimento (50-100 tenants)

```
Servidor 1: Dashboard Laravel (mesmo)
- Custo: R$ 120

Servidor 2: VPS Dokploy 1 (Bots)
- Hetzner CPX41: R$ 90
- Capacidade: 50 containers

Servidor 3: VPS Dokploy 2 (Bots)
- Hetzner CPX41: R$ 90
- Capacidade: 50 containers

Backups:
- Custo: R$ 36

Total Mensal: R$ 336
Receita (100 tenants × R$ 297): R$ 29.700
Custo por tenant: R$ 3,36
Margem: 98,9% 🎉
```

#### Fase 3: Escala (100-200 tenants)

```
Opção A: Adicionar mais CPX41
- 4 VPS CPX41: R$ 360
- Capacidade: 200 containers
- Custo por tenant: R$ 1,80

Opção B: Migrar para CCX33
- 2 VPS CCX33 (32GB): R$ 360
- Capacidade: 200 containers
- Custo por tenant: R$ 1,80

Total Mensal: R$ 480 (infra bots) + R$ 120 (dashboard) = R$ 600
Receita (200 tenants × R$ 297): R$ 59.400
Custo por tenant: R$ 3,00
Margem: 99% 🎉
```

### Comparação com Fly.io

```
Fly.io (50 tenants):
- Custo: $104.50/mês (~R$ 525)
- Margem: 96,5%

VPS + Dokploy (50 tenants):
- Custo: R$ 228/mês
- Margem: 98,5%

Economia: R$ 297/mês (56% mais barato)
```

### Escalabilidade Horizontal

```
Capacidade por VPS CPX41: 40-50 containers
Tempo para provisionar novo VPS: 5 minutos
Custo adicional por VPS: R$ 90/mês

Estratégia:
- 0-50 tenants: 1 VPS
- 50-100 tenants: 2 VPS
- 100-150 tenants: 3 VPS
- 150-200 tenants: 4 VPS
- 200+ tenants: Migrar para CCX33 ou K8s
```

---

## 📊 Monitoramento e Manutenção

### Métricas a Monitorar

#### Nível de Servidor
- CPU usage (alerta >80%)
- RAM usage (alerta >85%)
- Disk usage (alerta >80%)
- Network I/O
- Uptime

#### Nível de Container
- Status (running, stopped, error)
- CPU por container
- RAM por container
- Restart count
- Health check failures

#### Nível de Aplicação
- Bots conectados vs desconectados
- Mensagens processadas/min
- Latência de webhooks
- Taxa de erro de webhooks
- QR Codes gerados vs conectados

### Ferramentas de Monitoramento

#### 1. Dokploy Dashboard (Nativo)
- Visualização de todos os containers
- Logs em tempo real
- Métricas de CPU/RAM
- Status de health checks

#### 2. Uptime Kuma (Recomendado)
```bash
# Instalar via Docker no VPS
docker run -d \
  --name uptime-kuma \
  -p 3001:3001 \
  -v uptime-kuma:/app/data \
  --restart=always \
  louislam/uptime-kuma:1
```

**Funcionalidades:**
- Monitorar uptime de cada bot
- Alertas via Telegram/Discord/Email
- Dashboard público de status
- Histórico de uptime

#### 3. Prometheus + Grafana (Opcional - Fase 3)
- Métricas detalhadas
- Dashboards customizados
- Alertas avançados

### Alertas Configurados

```yaml
Alertas Críticos (Telegram):
  - VPS com CPU >90% por 5 minutos
  - VPS com RAM >95% por 5 minutos
  - VPS com Disk >90%
  - Bot com 3+ falhas de health check
  - 5+ bots desconectados simultaneamente

Alertas de Aviso (Email):
  - VPS com CPU >80% por 15 minutos
  - VPS com RAM >85% por 15 minutos
  - Bot reiniciado automaticamente
  - Webhook com latência >2s
```

### Backup e Recuperação

#### Backup de Sessões WhatsApp

```bash
# Script de backup diário (cron)
#!/bin/bash

BACKUP_DIR="/backups/whatsapp-sessions"
DATE=$(date +%Y%m%d)

# Criar diretório de backup
mkdir -p $BACKUP_DIR/$DATE

# Backup de todos os volumes
for volume in /var/lib/dokploy/volumes/bot-tenant-*/session; do
  tenant=$(basename $(dirname $volume))
  tar -czf $BACKUP_DIR/$DATE/$tenant.tar.gz $volume
done

# Manter apenas últimos 7 dias
find $BACKUP_DIR -type d -mtime +7 -exec rm -rf {} \;

# Upload para S3/Backblaze (opcional)
# aws s3 sync $BACKUP_DIR s3://zaptria-backups/sessions/
```

**Cron:**
```cron
0 3 * * * /root/scripts/backup-sessions.sh
```

#### Recuperação de Desastre

**Cenário 1: Container falhou**
- Dokploy reinicia automaticamente (restart: always)
- Sessão WhatsApp preservada no volume

**Cenário 2: VPS falhou**
1. Provisionar novo VPS
2. Instalar Dokploy
3. Restaurar backups de volumes
4. Recriar containers via API

**Cenário 3: Perda de sessão WhatsApp**
1. Tenant precisa escanear QR Code novamente
2. Processo automático (webhook /qr)

---

## 🗺️ Roadmap de Implementação

### Sprint 2: Engine de Execução + Setup Inicial Infra (2-3 semanas)

**Semana 1: Setup VPS**
- [ ] Provisionar VPS Hetzner CPX41
- [ ] Configurar firewall e segurança
- [ ] Instalar Docker + Dokploy
- [ ] Configurar DNS wildcard
- [ ] Gerar API token do Dokploy

**Semana 2: Desenvolvimento Bot**
- [ ] Criar projeto Node.js do bot
- [ ] Implementar whatsapp-web.js
- [ ] Implementar webhooks para dashboard
- [ ] Criar Dockerfile otimizado
- [ ] Build e push para registry
- [ ] Testar deploy manual no Dokploy

**Semana 3: Integração Laravel**
- [ ] Criar BotProvisioningService
- [ ] Implementar API Dokploy no Laravel
- [ ] Adicionar campos na migration
- [ ] Integrar com TenantProvisioningService
- [ ] Criar job CheckBotsHealth
- [ ] Testes end-to-end

**Entregável:** Infraestrutura funcional com provisionamento automático

---

### Sprint 3: Melhorias e Automação (1-2 semanas)

**Semana 1: Automação**
- [ ] Script de backup automático
- [ ] Configurar Uptime Kuma
- [ ] Implementar alertas (Telegram)
- [ ] Dashboard de monitoramento
- [ ] Documentação operacional

**Semana 2: Otimização**
- [ ] Otimizar consumo de RAM dos containers
- [ ] Implementar rate limiting
- [ ] Melhorar logs estruturados
- [ ] Testes de carga (50 containers)
- [ ] Plano de disaster recovery

**Entregável:** Infraestrutura robusta e monitorada

---

### Fase 2: Crescimento (3-6 meses)

**Quando atingir 40 tenants:**
- [ ] Provisionar segundo VPS (CPX41)
- [ ] Implementar load balancing simples
- [ ] Distribuir novos tenants entre VPS
- [ ] Monitorar capacidade

**Quando atingir 80 tenants:**
- [ ] Avaliar migração para CCX33
- [ ] Ou adicionar terceiro VPS CPX41
- [ ] Implementar orquestração multi-servidor

---

### Fase 3: Escala Enterprise (6-12 meses)

**Quando atingir 200+ tenants:**
- [ ] Migrar para Kubernetes (K3s)
- [ ] Implementar auto-scaling
- [ ] Multi-região (latência)
- [ ] CDN para assets
- [ ] Redis para cache distribuído

---

## 📝 Checklist de Implementação

### Pré-requisitos
- [ ] Conta Hetzner Cloud criada
- [ ] Domínio configurado (zaptria.com)
- [ ] Cloudflare configurado
- [ ] Docker Registry (Docker Hub ou GitHub)
- [ ] Telegram Bot para alertas (opcional)

### Setup Inicial
- [ ] VPS provisionado
- [ ] Firewall configurado
- [ ] Docker instalado
- [ ] Dokploy instalado e configurado
- [ ] DNS wildcard apontando para VPS
- [ ] SSL configurado (Let's Encrypt)

### Desenvolvimento
- [ ] Projeto bot Node.js criado
- [ ] Dockerfile otimizado
- [ ] Imagem buildada e pushed
- [ ] BotProvisioningService implementado
- [ ] Migrations aplicadas
- [ ] Testes unitários passando

### Testes
- [ ] Deploy manual de 1 bot funcionando
- [ ] QR Code recebido no dashboard
- [ ] Conexão WhatsApp estabelecida
- [ ] Webhooks funcionando
- [ ] Provisionamento automático testado
- [ ] Destruição de container testada

### Produção
- [ ] Backup automático configurado
- [ ] Monitoramento ativo
- [ ] Alertas configurados
- [ ] Documentação completa
- [ ] Runbook de operações
- [ ] Plano de disaster recovery

---

## 🎯 Conclusão

### Decisão Final: VPS + Dokploy

**Justificativa:**
1. **Custo:** 56% mais barato que Fly.io
2. **Escalabilidade:** Simples adicionar VPS conforme crescimento
3. **DX:** Interface + API facilitam operação
4. **Controle:** Total controle da infraestrutura
5. **Margem:** 98,5% de margem no MVP

### Próximos Passos Imediatos

1. **Aprovar proposta** de infraestrutura
2. **Provisionar VPS** Hetzner CPX41
3. **Iniciar Sprint 2** (Engine + Infra)
4. **Desenvolver bot** Node.js
5. **Integrar** com dashboard Laravel

### Riscos e Mitigações

| Risco | Probabilidade | Impacto | Mitigação |
|-------|---------------|---------|-----------|
| VPS ficar offline | Baixa | Alto | Backup automático + VPS reserva |
| Container crashar | Média | Médio | Auto-restart + health checks |
| Perda de sessão WhatsApp | Baixa | Médio | Backup diário de volumes |
| Capacidade insuficiente | Média | Baixo | Monitoramento + escala horizontal |
| Dokploy bug crítico | Baixa | Médio | Fallback para Docker Compose |

---

**Última atualização:** 05/02/2026  
**Próxima revisão:** Após implementação (Sprint 2)  
**Mantido por:** Equipe de Desenvolvimento Zaptria
