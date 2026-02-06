# Sprint 1.5 - Onboarding em 3 Etapas: Especificação Técnica

**Data:** 06/02/2026  
**Sprint:** 1.5 (inserida entre Sprint 1 e Sprint 2)  
**Status:** Planejada  
**Objetivo:** Dividir o fluxo de cadastro/checkout em 3 etapas para melhorar a UX de onboarding, enriquecer dados de usuário e empresa, e criar layout dedicado com identidade visual consistente.

---

## 📋 Índice

1. [Contexto e Motivação](#contexto-e-motivação)
2. [Banco de Dados](#banco-de-dados)
3. [Models](#models)
4. [Validação de Documentos](#validação-de-documentos)
5. [Layout Onboarding](#layout-onboarding)
6. [Rotas](#rotas)
7. [Controller](#controller)
8. [Views](#views)
9. [JavaScript](#javascript)
10. [Lista de Arquivos](#lista-de-arquivos)
11. [Testes](#testes)

---

## 🎯 Contexto e Motivação

### Fluxo Atual (Sprint 0)
- **Tela única** de registro (`/register`) coleta tudo junto: nome do dono, email, senha, nome da empresa
- **Checkout** (`/checkout/{subscription}`) com Stripe Elements
- **Sucesso** (`/checkout/success`)

### Problemas Identificados
- UX sobrecarregada: formulário mistura dados pessoais e da empresa
- Sem dados ricos da empresa: tabela `tenants` só tem `name`, `slug`, `status`
- Sem dados ricos do dono: tabela `users` não tem telefone, documento
- Layout único: todas as telas usam `layouts.app` com navbar completa, inclusive no checkout
- Sem stepper visual: não há indicador de progresso entre etapas

### Novo Fluxo Proposto

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

---

## 🗄️ Banco de Dados

### Migration: `add_profile_fields_to_users_table`

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('phone', 20)->nullable()->after('email');
    $table->string('document', 20)->nullable()->after('phone');
    $table->string('document_type', 10)->nullable()->default('cpf')->after('document');
});
```

- `phone`: telefone pessoal do dono
- `document`: CPF no Brasil (genérico para expansão futura)
- `document_type`: tipo do documento (`cpf` padrão, expansível para `dni`, `ssn`, etc.)

### Migration: `create_companies_table`

```php
Schema::create('companies', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->string('document', 20)->nullable();
    $table->string('document_type', 10)->default('cnpj');
    $table->string('phone', 20)->nullable();
    $table->string('email')->nullable();
    $table->string('segment')->nullable();
    $table->json('address')->nullable();
    $table->timestamps();

    $table->unique('tenant_id');
});
```

### Estrutura do campo `address` (JSON)

```json
{
    "zip": "01001-000",
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

```php
class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'name', 'document', 'document_type',
        'phone', 'email', 'segment', 'address',
    ];

    protected function casts(): array
    {
        return ['address' => 'array'];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
```

### `Tenant` (editar)

Adicionar:
```php
public function company()
{
    return $this->hasOne(Company::class);
}
```

### `User` (editar)

Adicionar ao `$fillable`:
```php
'phone', 'document', 'document_type',
```

---

## ✅ Validação de Documentos

### `App\Rules\CpfRule`

- Valida formato e dígitos verificadores do CPF
- Aceita input com ou sem máscara (limpa pontuação antes de validar)
- Mensagem: `"O CPF informado não é válido."`

### `App\Rules\CnpjRule`

- Valida formato e dígitos verificadores do CNPJ
- Aceita input com ou sem máscara
- Mensagem: `"O CNPJ informado não é válido."`

Ambas implementadas como Custom Rules nativas do Laravel (sem dependência externa).

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
- Recebe variável `$currentStep` (1, 2 ou 3) para destacar passo ativo no stepper
- Sem navbar de navegação (Dashboard, Leads, Fluxos, etc.)
- Tema claro/escuro suportado (mesma lógica do `layouts.app`)
- Bootstrap 5, Google Fonts (Lato), Font Awesome
- Footer minimalista (copyright + link de suporte)

**Diferença-chave vs `layouts.app`:**
- Sem bloco `@auth` com navbar
- Com stepper visual (círculos + linhas conectoras + labels)
- Mantém identidade visual completa

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

// Checkout - rotas existentes, sem mudança (só trocar layout na view)
```

---

## 🎮 Controller

### `RegisterController` (refatorado)

| Método | Ação |
|--------|------|
| `showStep1()` | Exibe `auth.register.step1` (layout onboarding, step=1) |
| `processStep1()` | Valida: name*, email*, password*, password_confirmation*, phone, document (CpfRule). Salva em `session('onboarding')`. Redirect → `register.company` |
| `showStep2()` | Verifica `session('onboarding')`, senão redirect → `register`. Exibe `auth.register.step2` (layout onboarding, step=2) |
| `processStep2()` | Valida campos empresa (todos opcionais). `DB::transaction`: cria Tenant (name = user name), User, Company (se dados preenchidos), Subscription (pending). Login automático. Limpa session. Redirect → `checkout.index` |

### Session entre etapas

```php
session('onboarding') = [
    'name' => '...',
    'email' => '...',
    'password' => '...', // hash
    'phone' => '...',
    'document' => '...',
];
```

### Lógica do Tenant.name

- Sempre usa o nome do usuário (`$validated['name']` da etapa 1)

### Botão "Pular" no Step 2

- Faz POST sem preencher campos — controller cria Tenant + User + Subscription sem Company

---

## 🖥️ Views

### `auth/register/step1.blade.php`

| Campo | Tipo | Obrigatório | Máscara |
|-------|------|-------------|---------|
| Nome | text | ✅ | — |
| Email | email | ✅ | — |
| Senha | password | ✅ | — |
| Confirmar Senha | password | ✅ | — |
| Telefone | tel | ❌ | `(00) 00000-0000` |
| CPF | text | ❌ | `000.000.000-00` |

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

**Botões:** "Pular etapa" (outline) + "Continuar" (primary)

### Segmentos disponíveis (select)

- E-commerce
- Saúde
- Educação
- Consultoria
- Marketing Digital
- Tecnologia
- Serviços Financeiros
- Alimentação
- Imobiliário
- Varejo
- Outro

### `checkout/index.blade.php` (editar)

- Trocar `@extends('layouts.app')` → `@extends('layouts.onboarding', ['currentStep' => 3])`
- Resto do conteúdo sem mudança

### `checkout/success.blade.php`

- Sem mudança — continua com `@extends('layouts.app')`

---

## ⚡ JavaScript (Vanilla)

### `resources/js/utils/input-masks.js`

Módulo reutilizável com funções de máscara:
- `maskCpf(input)` — `000.000.000-00`
- `maskCnpj(input)` — `00.000.000/0000-00`
- `maskPhone(input)` — `(00) 00000-0000` / `(00) 0000-0000`
- `maskCep(input)` — `00000-000`

Inicialização automática via atributo `data-mask="cpf"` nos inputs.

### `resources/js/utils/cep-lookup.js`

Módulo reutilizável para busca de CEP:
- API: **ViaCEP** (`https://viacep.com.br/ws/{cep}/json/`) — gratuita, sem autenticação
- Função `initCepLookup(cepInput, fieldMap)`:
  - Escuta `blur` ou quando CEP atinge 8 dígitos
  - Faz `fetch` na ViaCEP
  - Preenche automaticamente: rua, bairro, cidade, estado
  - Marca cidade/estado como `readonly` quando preenchidos
  - Feedback visual (loading spinner, erro se CEP inválido)

**Uso reutilizável:**
```js
import { initCepLookup } from '../utils/cep-lookup';

initCepLookup('#cep', {
    street: '#street',
    neighborhood: '#neighborhood',
    city: '#city',
    state: '#state',
});
```

### `resources/js/pages/onboarding.js`

Inicializa máscaras + CEP lookup nas páginas de onboarding.

---

## 📁 Lista de Arquivos

| # | Tipo | Caminho | Ação |
|---|------|---------|------|
| 1 | Migration | `database/migrations/xxxx_add_profile_fields_to_users_table.php` | Criar |
| 2 | Migration | `database/migrations/xxxx_create_companies_table.php` | Criar |
| 3 | Model | `app/Models/Company.php` | Criar |
| 4 | Model | `app/Models/User.php` | Editar ($fillable) |
| 5 | Model | `app/Models/Tenant.php` | Editar (add company()) |
| 6 | Rule | `app/Rules/CpfRule.php` | Criar |
| 7 | Rule | `app/Rules/CnpjRule.php` | Criar |
| 8 | Layout | `resources/views/layouts/onboarding.blade.php` | Criar |
| 9 | View | `resources/views/auth/register/step1.blade.php` | Criar |
| 10 | View | `resources/views/auth/register/step2.blade.php` | Criar |
| 11 | Controller | `app/Http/Controllers/Auth/RegisterController.php` | Refatorar |
| 12 | View | `resources/views/checkout/index.blade.php` | Editar (layout) |
| 13 | Routes | `routes/web.php` | Editar |
| 14 | JS | `resources/js/utils/input-masks.js` | Criar |
| 15 | JS | `resources/js/utils/cep-lookup.js` | Criar |
| 16 | JS | `resources/js/pages/onboarding.js` | Criar |
| 17 | Factory | `database/factories/CompanyFactory.php` | Criar |
| 18 | Tests | `tests/Feature/RegisterControllerTest.php` | Criar/Refatorar |
| 19 | Tests | `tests/Unit/CpfRuleTest.php` | Criar |
| 20 | Tests | `tests/Unit/CnpjRuleTest.php` | Criar |
| 21 | Tests | `tests/Unit/CompanyTest.php` | Criar |
| 22 | View | `resources/views/auth/register.blade.php` | Remover |

**Total: 22 arquivos** (13 novos, 5 editados, 1 refatorado, 1 removido, 2 testes novos)

---

## 🧪 Testes

### Unitários
- [ ] `CpfRuleTest` — CPFs válidos, inválidos, com/sem máscara, sequências repetidas
- [ ] `CnpjRuleTest` — CNPJs válidos, inválidos, com/sem máscara
- [ ] `CompanyTest` — criação, relacionamento com tenant, cast de address

### Feature
- [ ] `RegisterControllerTest`
  - [ ] Step 1: exibe formulário
  - [ ] Step 1: valida campos obrigatórios
  - [ ] Step 1: valida CPF quando preenchido
  - [ ] Step 1: salva dados na session
  - [ ] Step 2: redireciona se session vazia
  - [ ] Step 2: exibe formulário com session válida
  - [ ] Step 2: cria tenant + user + subscription sem company (pular)
  - [ ] Step 2: cria tenant + user + company + subscription (preenchido)
  - [ ] Step 2: valida CNPJ quando preenchido
  - [ ] Step 2: faz login automático e redireciona para checkout

---

**Última atualização:** 06/02/2026  
**Próxima revisão:** Após início da implementação
