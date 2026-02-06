# Sprint 1.5 - Desenvolvimento: Onboarding em 3 Etapas

**Data de Início:** 06/02/2026  
**Sprint:** 1.5 (inserida entre Sprint 1 e Sprint 2)  
**Status:** ✅ CONCLUÍDA  
**Objetivo:** Dividir o fluxo de cadastro/checkout em 3 etapas para melhorar a UX de onboarding, enriquecer dados de usuário e empresa, e criar layout dedicado com identidade visual consistente.

---

## 📋 Índice

1. [Resumo das Entregas](#resumo-das-entregas)
2. [Banco de Dados](#banco-de-dados)
3. [Models](#models)
4. [Validação de Documentos](#validação-de-documentos)
5. [Layout Onboarding](#layout-onboarding)
6. [Controller](#controller)
7. [Rotas](#rotas)
8. [Views](#views)
9. [JavaScript](#javascript)
10. [Testes](#testes)
11. [Arquivos Criados/Modificados](#arquivos-criadosmodificados)
12. [Decisões Técnicas](#decisões-técnicas)
13. [Limitações Conhecidas](#limitações-conhecidas)

---

## 🎯 Resumo das Entregas

### Fluxo Implementado

```
  /register (guest)              /register/company (guest)         /checkout/{sub} (auth)
┌─────────────────┐          ┌─────────────────────┐          ┌──────────────────┐
│  ETAPA 1        │  POST    │  ETAPA 2            │  POST    │  ETAPA 3         │
│  Seus Dados     │ ───────→ │  Sua Empresa        │ ───────→ │  Pagamento       │
│                 │          │  (opcional)          │          │  Stripe Elements │
│  Layout:        │          │  Layout:             │          │  Layout:         │
│  onboarding(1)  │          │  onboarding(2)       │          │  onboarding(3)   │
└─────────────────┘          │                     │          └────────┬─────────┘
                             │  [Pular] [Continuar]│                   │
                             └─────────────────────┘                   ▼
                                                              /checkout/success
                                                           ┌──────────────────┐
                                                           │  Layout: app     │
                                                           │  (com navbar)    │
                                                           └──────────────────┘
```

### Checklist de Requisitos

| Requisito | Status | Observações |
|-----------|--------|-------------|
| Migration: campos perfil em `users` | ✅ | `phone`, `document`, `document_type` |
| Migration: tabela `companies` | ✅ | Dados jurídicos/comerciais opcionais |
| Model `Company` | ✅ | Com cast `address` → array |
| Relacionamento `Tenant → Company` | ✅ | hasOne |
| Validação CPF (CpfRule) | ✅ | Dígitos verificadores, sequências repetidas |
| Validação CNPJ (CnpjRule) | ✅ | Dígitos verificadores, sequências repetidas |
| Layout `onboarding.blade.php` | ✅ | Stepper visual 3 etapas, sem navbar |
| RegisterController refatorado | ✅ | 4 métodos: showStep1, processStep1, showStep2, processStep2 |
| View Step 1 (dados pessoais) | ✅ | Nome, email, senha, telefone, CPF |
| View Step 2 (dados empresa) | ✅ | Nome empresa, CNPJ, telefone, email, segmento, endereço |
| Checkout usa layout onboarding | ✅ | Step 3 no stepper |
| Rotas atualizadas | ✅ | 4 rotas de onboarding |
| Máscaras de input (JS) | ✅ | CPF, CNPJ, telefone, CEP |
| Busca de CEP (ViaCEP) | ✅ | Auto-preenchimento de endereço |
| CompanyFactory | ✅ | Para testes |
| Testes unitários (CPF/CNPJ) | ✅ | 20 testes |
| Testes feature (RegisterController) | ✅ | 12 testes |
| Testes unitários (Company) | ✅ | 5 testes |
| View antiga depreciada | ✅ | `auth/register.blade.php` marcada para remoção |

---

## 🗄️ Banco de Dados

### Migration 1: `2026_02_06_000001_add_profile_fields_to_users_table`

Adiciona campos de perfil à tabela `users`:

```php
$table->string('phone', 20)->nullable()->after('email');
$table->string('document', 20)->nullable()->after('phone');
$table->string('document_type', 10)->nullable()->default('cpf')->after('document');
```

### Migration 2: `2026_02_06_000002_create_companies_table`

Cria tabela `companies` para dados jurídicos/comerciais:

```php
Schema::create('companies', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->string('document', 20)->nullable();       // CNPJ
    $table->string('document_type', 10)->default('cnpj');
    $table->string('phone', 20)->nullable();
    $table->string('email')->nullable();
    $table->string('segment')->nullable();
    $table->json('address')->nullable();               // JSON com endereço completo
    $table->timestamps();
    $table->unique('tenant_id');                       // 1 company por tenant
});
```

### Estrutura do campo `address` (JSON)

```json
{
    "zip": "01001000",
    "street": "Rua das Flores",
    "number": "123",
    "complement": "Sala 4",
    "neighborhood": "Centro",
    "city": "São Paulo",
    "state": "SP"
}
```

### Diagrama de Relacionamentos (atualizado)

```
Tenant (1) → (N) Users
Tenant (1) → (1) Company (opcional)
Tenant (1) → (1) Subscription
Tenant (1) → (N) Leads, Fluxes, etc.
```

---

## 📦 Models

### `Company` (novo)

- **Arquivo:** `app/Models/Company.php`
- **Fillable:** `tenant_id`, `name`, `document`, `document_type`, `phone`, `email`, `segment`, `address`
- **Casts:** `address` → `array`
- **Relacionamentos:** `belongsTo(Tenant)`

### `Tenant` (editado)

- **Adicionado:** `company()` → `hasOne(Company::class)`

### `User` (editado)

- **Adicionado ao $fillable:** `phone`, `document`, `document_type`

---

## ✅ Validação de Documentos

### `App\Rules\CpfRule`

- Valida formato (exatamente 11 dígitos após limpar pontuação)
- Rejeita sequências repetidas (ex: `111.111.111-11`)
- Calcula e valida ambos os dígitos verificadores
- Aceita input com ou sem máscara
- Mensagem: `"O CPF informado não é válido."`

### `App\Rules\CnpjRule`

- Valida formato (exatamente 14 dígitos após limpar pontuação)
- Rejeita sequências repetidas (ex: `11.111.111/1111-11`)
- Calcula e valida ambos os dígitos verificadores com pesos corretos
- Aceita input com ou sem máscara
- Mensagem: `"O CNPJ informado não é válido."`

Ambas implementadas como Custom Rules nativas do Laravel (`ValidationRule` interface), sem dependências externas.

---

## 🎨 Layout Onboarding

### `layouts/onboarding.blade.php`

```
┌──────────────────────────────────────────────────┐
│                 [Logo Zaptria]                    │
│                                                  │
│   ● Seus Dados ── ○ Sua Empresa ── ○ Pagamento  │
│                                                  │
│   ┌──────────────────────────────────────────┐   │
│   │           @yield('content')               │   │
│   └──────────────────────────────────────────┘   │
│                                                  │
│          © 2026 Zaptria · Suporte                │
└──────────────────────────────────────────────────┘
```

**Características:**
- Recebe variável `$currentStep` (1, 2 ou 3) para destacar passo ativo
- Stepper visual com círculos + linhas conectoras + labels
- Steps completados mostram ícone de check verde
- Step ativo tem destaque com cor primária + sombra
- Sem navbar de navegação (Dashboard, Leads, etc.)
- Tema claro/escuro suportado (mesma lógica do `layouts.app`)
- Bootstrap 5, Google Fonts (Lato), Font Awesome
- Footer minimalista (copyright + link de suporte)
- Responsivo (linhas do stepper adaptam em mobile)

---

## 🎮 Controller

### `RegisterController` (refatorado)

| Método | Rota | Ação |
|--------|------|------|
| `showStep1()` | `GET /register` | Exibe formulário de dados pessoais |
| `processStep1()` | `POST /register` | Valida, limpa máscaras, hash da senha, salva em `session('onboarding')`, redirect → step 2 |
| `showStep2()` | `GET /register/company` | Verifica session, exibe formulário de empresa |
| `processStep2()` | `POST /register/company` | Valida, `DB::transaction`: cria Tenant + User + Company (se dados) + Subscription, login automático, redirect → checkout |

### Session entre etapas

```php
session('onboarding') = [
    'name'     => 'João Silva',
    'email'    => 'joao@email.com',
    'password' => '$2y$12$...', // hash bcrypt
    'phone'    => '11999999999', // sem máscara
    'document' => '52998224725', // sem máscara
];
```

### Lógica de criação

- **Tenant.name** = nome do usuário (etapa 1)
- **Company** = criada somente se algum campo da etapa 2 foi preenchido
- **Botão "Pular"** = faz POST sem dados → cria tudo sem Company
- **Senha** = hash feito na etapa 1, guardado na session, usado direto no `User::create`
- **Máscaras** = removidas (regex `/\D/`) antes de salvar na session e no banco

---

## 🛤️ Rotas

```php
// Onboarding - Step 1 (guest)
Route::get('/register', [RegisterController::class, 'showStep1'])
    ->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'processStep1'])
    ->name('register.step1')->middleware('guest');

// Onboarding - Step 2 (guest, requer session de step1)
Route::get('/register/company', [RegisterController::class, 'showStep2'])
    ->name('register.company')->middleware('guest');
Route::post('/register/company', [RegisterController::class, 'processStep2'])
    ->name('register.step2')->middleware('guest');

// Checkout - sem mudança nas rotas, apenas layout da view alterado
```

---

## 🖥️ Views

### `auth/register/step1.blade.php`

| Campo | Tipo | Obrigatório | Máscara |
|-------|------|-------------|---------|
| Nome completo | text | ✅ | — |
| Email | email | ✅ | — |
| Senha | password | ✅ | — |
| Confirmar Senha | password | ✅ | — |
| Telefone | tel | ❌ | `(00) 00000-0000` |
| CPF | text | ❌ | `000.000.000-00` |

- Layout: `onboarding` (step=1)
- Botão: "Continuar →"
- Link para login existente
- Info de preço (R$ 297/mês)

### `auth/register/step2.blade.php`

| Campo | Tipo | Obrigatório | Máscara |
|-------|------|-------------|---------|
| Nome da Empresa | text | ❌ | — |
| CNPJ | text | ❌ | `00.000.000/0000-00` |
| Telefone Comercial | tel | ❌ | `(00) 00000-0000` |
| Email Comercial | email | ❌ | — |
| Segmento | select | ❌ | — |
| CEP | text | ❌ | `00000-000` (auto-preenche via ViaCEP) |
| Rua | text | ❌ | (auto-preenchido) |
| Número | text | ❌ | — |
| Complemento | text | ❌ | — |
| Bairro | text | ❌ | (auto-preenchido) |
| Cidade | text | ❌ | (auto-preenchido, readonly) |
| Estado | text | ❌ | (auto-preenchido, readonly) |

- Layout: `onboarding` (step=2)
- Botões: "Pular etapa" (outline) + "Continuar →" (primary)
- Seção de endereço separada visualmente

### Segmentos disponíveis

- E-commerce, Saúde, Educação, Consultoria, Marketing Digital, Tecnologia, Serviços Financeiros, Alimentação, Imobiliário, Varejo, Outro

### `checkout/index.blade.php` (editado)

- Trocado `@extends('layouts.app')` → `@extends('layouts.onboarding', ['currentStep' => 3])`
- Conteúdo sem mudança

### `checkout/success.blade.php`

- Sem mudança — continua com `@extends('layouts.app')`

### `auth/register.blade.php` (depreciado)

- Conteúdo substituído por comentário de depreciação
- Pode ser removido com segurança

---

## ⚡ JavaScript

### `resources/js/utils/input-masks.js`

Módulo reutilizável com funções de máscara vanilla JS:

| Função | Padrão | Uso |
|--------|--------|-----|
| `maskCpf(input)` | `000.000.000-00` | `data-mask="cpf"` |
| `maskCnpj(input)` | `00.000.000/0000-00` | `data-mask="cnpj"` |
| `maskPhone(input)` | `(00) 00000-0000` / `(00) 0000-0000` | `data-mask="phone"` |
| `maskCep(input)` | `00000-000` | `data-mask="cep"` |
| `initMasks(container)` | — | Inicializa todas via `data-mask` |

- Máscaras progressivas (formatam conforme o usuário digita)
- Limita quantidade de dígitos automaticamente
- Suporta telefone fixo (10 dígitos) e celular (11 dígitos)

### `resources/js/utils/cep-lookup.js`

Módulo reutilizável para busca de CEP:

- **API:** ViaCEP (`https://viacep.com.br/ws/{cep}/json/`) — gratuita, sem autenticação
- **Trigger:** `blur` no input ou quando CEP atinge 8 dígitos
- **Auto-preenchimento:** rua, bairro, cidade, estado
- **Feedback visual:** spinner durante busca, mensagem de erro se CEP inválido
- **Cidade/Estado:** marcados como `readonly` quando preenchidos automaticamente

### `resources/js/pages/onboarding.js`

Entry point que inicializa máscaras + CEP lookup nas páginas de onboarding.

### `vite.config.js` (editado)

Adicionado `resources/js/pages/onboarding.js` ao array de inputs.

---

## 🧪 Testes

### Testes Unitários

| Arquivo | Testes | Descrição |
|---------|--------|-----------|
| `tests/Unit/Rules/CpfRuleTest.php` | 10 | CPFs válidos/inválidos, com/sem máscara, sequências repetidas, mensagem de erro |
| `tests/Unit/Rules/CnpjRuleTest.php` | 10 | CNPJs válidos/inválidos, com/sem máscara, sequências repetidas, mensagem de erro |
| `tests/Unit/Models/CompanyTest.php` | 5 | Criação, relacionamentos, cast de address, campos opcionais |

### Testes Feature

| Arquivo | Testes | Descrição |
|---------|--------|-----------|
| `tests/Feature/Controllers/RegisterControllerTest.php` | 12 | Step 1: form, validações, session. Step 2: redirect sem session, criação sem/com company, validação CNPJ, login, slug |

### Resumo

| Categoria | Quantidade |
|-----------|------------|
| Unitários (CPF) | 10 |
| Unitários (CNPJ) | 10 |
| Unitários (Company) | 5 |
| Feature (Register) | 12 |
| **Total** | **37** |

### Comandos para rodar

```bash
# Todos os testes
php artisan test

# Testes da Sprint 1.5
php artisan test --filter=CpfRule
php artisan test --filter=CnpjRule
php artisan test --filter=CompanyTest
php artisan test --filter=RegisterController

# Testes unitários apenas
php artisan test --testsuite=Unit

# Testes feature apenas
php artisan test --testsuite=Feature
```

---

## 📁 Arquivos Criados/Modificados

### Novos (16 arquivos)

```
database/migrations/
├── 2026_02_06_000001_add_profile_fields_to_users_table.php
└── 2026_02_06_000002_create_companies_table.php

app/
├── Models/Company.php
├── Rules/CpfRule.php
└── Rules/CnpjRule.php

resources/views/
├── layouts/onboarding.blade.php
└── auth/register/
    ├── step1.blade.php
    └── step2.blade.php

resources/js/
├── utils/input-masks.js
├── utils/cep-lookup.js
└── pages/onboarding.js

database/factories/
└── CompanyFactory.php

tests/
├── Unit/Rules/CpfRuleTest.php
├── Unit/Rules/CnpjRuleTest.php
└── Unit/Models/CompanyTest.php
```

### Editados (6 arquivos)

```
app/Models/User.php                    → +3 campos no $fillable
app/Models/Tenant.php                  → +company() relationship
app/Http/Controllers/Auth/RegisterController.php → Refatorado (4 métodos)
routes/web.php                         → 4 rotas de onboarding
resources/views/checkout/index.blade.php → Layout trocado para onboarding
vite.config.js                         → +onboarding.js no input
```

### Depreciados (1 arquivo)

```
resources/views/auth/register.blade.php → Marcado para remoção
```

### Refatorados (1 arquivo)

```
tests/Feature/Controllers/RegisterControllerTest.php → 12 testes para novo fluxo
```

**Total: 24 arquivos** (16 novos, 6 editados, 1 depreciado, 1 refatorado)

---

## 🔧 Decisões Técnicas

### 1. Senha na Session
- A senha é hasheada com `Hash::make()` **antes** de ser salva na session
- Nunca armazenamos texto puro na session
- O hash é usado diretamente no `User::create()` sem re-hash (campo `password` tem cast `hashed`, mas como já é hash, o Laravel detecta e não re-hasheia)

### 2. Limpeza de Máscaras
- Todas as máscaras são removidas (`preg_replace('/\D/', '')`) antes de salvar na session e no banco
- Isso garante dados limpos para consultas e integrações

### 3. Company Opcional
- A tabela `companies` é separada de `tenants` para isolamento de dados
- Company só é criada se algum campo relevante foi preenchido na etapa 2
- Botão "Pular" faz POST sem dados → nenhuma Company é criada

### 4. Tenant.name = User.name
- No novo fluxo, o nome do tenant vem do nome do usuário (etapa 1)
- Isso simplifica o cadastro e evita campo obrigatório de empresa

### 5. Layout Onboarding vs App
- `onboarding.blade.php`: sem navbar, com stepper visual — usado em register e checkout
- `app.blade.php`: com navbar completa — usado no dashboard e checkout/success
- Ambos compartilham mesma identidade visual (Bootstrap 5, Lato, cores)

### 6. JavaScript Vanilla
- Sem dependências externas para máscaras e CEP
- Módulos ES6 reutilizáveis (`import`/`export`)
- Inicialização via atributo `data-mask` nos inputs
- ViaCEP como API de CEP (gratuita, sem auth)

---

## ⚠️ Limitações Conhecidas

1. **Validação de CPF/CNPJ** — Apenas validação matemática, não verifica se o documento existe na Receita Federal
2. **ViaCEP** — API gratuita sem SLA; em caso de indisponibilidade, o usuário pode preencher manualmente
3. **Session de onboarding** — Expira com a session do Laravel (padrão 120 min); se expirar entre etapas, o usuário precisa recomeçar
4. **Intelephense** — Mostra falsos positivos para `auth()->login()` e `auth()->user()` — funciona corretamente em runtime

---

## 📝 Comandos Úteis

```bash
# Rodar migrations
php artisan migrate

# Rodar testes da sprint
php artisan test --filter=CpfRule
php artisan test --filter=CnpjRule
php artisan test --filter=CompanyTest
php artisan test --filter=RegisterController

# Build assets (inclui onboarding.js)
npm run build

# Dev mode
npm run dev
```

---

## 🚀 Próximos Passos (Sprint 2)

### Engine de Execução de Fluxos
1. Criar model `ConversationSession`
2. Implementar `SessionManager`
3. Implementar `FlowEngine`
4. Criar `NodeProcessors` para cada tipo de nó
5. Integrar com `WhatsappWebhookService`

---

**Autor:** Cascade AI  
**Data:** 06/02/2026  
**Versão:** 1.0
