@extends('layouts.onboarding', ['currentStep' => 2])

@section('title', 'Cadastro - Sua Empresa | Zaptria')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card not-animate shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="text-center mb-1">Dados da Empresa</h5>
                <p class="text-muted small text-center mb-4">
                    Opcional — preencha se desejar ou pule esta etapa
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.step2') }}" method="POST" id="step2-form" novalidate>
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label">Nome da Empresa</label>
                            <input
                                type="text"
                                name="company_name"
                                id="company_name"
                                class="form-control @error('company_name') is-invalid @enderror"
                                value="{{ old('company_name') }}"
                                placeholder="Sua Empresa Ltda"
                            >
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cnpj" class="form-label">CNPJ</label>
                            <input
                                type="text"
                                name="cnpj"
                                id="cnpj"
                                class="form-control @error('cnpj') is-invalid @enderror"
                                value="{{ old('cnpj') }}"
                                placeholder="00.000.000/0000-00"
                                data-mask="cnpj"
                            >
                            @error('cnpj')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_phone" class="form-label">Telefone Comercial</label>
                            <input
                                type="tel"
                                name="company_phone"
                                id="company_phone"
                                class="form-control @error('company_phone') is-invalid @enderror"
                                value="{{ old('company_phone') }}"
                                placeholder="(00) 00000-0000"
                                data-mask="phone"
                            >
                            @error('company_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company_email" class="form-label">Email Comercial</label>
                            <input
                                type="email"
                                name="company_email"
                                id="company_email"
                                class="form-control @error('company_email') is-invalid @enderror"
                                value="{{ old('company_email') }}"
                                placeholder="contato@empresa.com"
                            >
                            @error('company_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="segment" class="form-label">Segmento</label>
                        <select
                            name="segment"
                            id="segment"
                            class="form-select @error('segment') is-invalid @enderror"
                        >
                            <option value="">Selecione o segmento...</option>
                            <option value="ecommerce" {{ old('segment') == 'ecommerce' ? 'selected' : '' }}>E-commerce</option>
                            <option value="saude" {{ old('segment') == 'saude' ? 'selected' : '' }}>Saúde</option>
                            <option value="educacao" {{ old('segment') == 'educacao' ? 'selected' : '' }}>Educação</option>
                            <option value="consultoria" {{ old('segment') == 'consultoria' ? 'selected' : '' }}>Consultoria</option>
                            <option value="marketing_digital" {{ old('segment') == 'marketing_digital' ? 'selected' : '' }}>Marketing Digital</option>
                            <option value="tecnologia" {{ old('segment') == 'tecnologia' ? 'selected' : '' }}>Tecnologia</option>
                            <option value="servicos_financeiros" {{ old('segment') == 'servicos_financeiros' ? 'selected' : '' }}>Serviços Financeiros</option>
                            <option value="alimentacao" {{ old('segment') == 'alimentacao' ? 'selected' : '' }}>Alimentação</option>
                            <option value="imobiliario" {{ old('segment') == 'imobiliario' ? 'selected' : '' }}>Imobiliário</option>
                            <option value="varejo" {{ old('segment') == 'varejo' ? 'selected' : '' }}>Varejo</option>
                            <option value="outro" {{ old('segment') == 'outro' ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('segment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-3">
                    <p class="small text-muted mb-3">
                        <i class="fa-solid fa-location-dot me-1"></i> Endereço
                    </p>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="zip" class="form-label">CEP</label>
                            <div class="position-relative">
                                <input
                                    type="text"
                                    name="zip"
                                    id="zip"
                                    class="form-control @error('zip') is-invalid @enderror"
                                    value="{{ old('zip') }}"
                                    placeholder="00000-000"
                                    data-mask="cep"
                                >
                                <div id="cep-spinner" class="position-absolute top-50 end-0 translate-middle-y me-3 d-none">
                                    <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                                </div>
                            </div>
                            <div id="cep-error" class="text-danger small mt-1 d-none"></div>
                            @error('zip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="street" class="form-label">Rua</label>
                            <input
                                type="text"
                                name="street"
                                id="street"
                                class="form-control @error('street') is-invalid @enderror"
                                value="{{ old('street') }}"
                                placeholder="Rua das Flores"
                            >
                            @error('street')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="number" class="form-label">Nº</label>
                            <input
                                type="text"
                                name="number"
                                id="number"
                                class="form-control @error('number') is-invalid @enderror"
                                value="{{ old('number') }}"
                                placeholder="123"
                            >
                            @error('number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="complement" class="form-label">Complemento</label>
                            <input
                                type="text"
                                name="complement"
                                id="complement"
                                class="form-control @error('complement') is-invalid @enderror"
                                value="{{ old('complement') }}"
                                placeholder="Sala 4"
                            >
                            @error('complement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="neighborhood" class="form-label">Bairro</label>
                            <input
                                type="text"
                                name="neighborhood"
                                id="neighborhood"
                                class="form-control @error('neighborhood') is-invalid @enderror"
                                value="{{ old('neighborhood') }}"
                                placeholder="Centro"
                            >
                            @error('neighborhood')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="city" class="form-label">Cidade</label>
                            <input
                                type="text"
                                name="city"
                                id="city"
                                class="form-control @error('city') is-invalid @enderror"
                                value="{{ old('city') }}"
                                placeholder="São Paulo"
                                readonly
                            >
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="state" class="form-label">UF</label>
                            <input
                                type="text"
                                name="state"
                                id="state"
                                class="form-control @error('state') is-invalid @enderror"
                                value="{{ old('state') }}"
                                placeholder="SP"
                                maxlength="2"
                                readonly
                            >
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" name="_skip" value="1" class="btn btn-outline-secondary flex-fill" formnovalidate>
                            Pular etapa
                        </button>
                        <button type="submit" class="btn btn-primary flex-fill">
                            Continuar <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/onboarding.js'])
@endpush
