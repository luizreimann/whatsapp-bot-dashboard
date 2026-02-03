@extends('layouts.app')

@section('title', 'Cadastro | Zaptria')

@section('content')
<div class="row justify-content-center align-content-center mt-5">
    <div class="col-md-6 col-lg-5">
        <div class="card not-animate shadow-sm border-0">
            <div class="card-body p-4">
                <picture class="d-flex justify-content-center">
                    <img 
                        src="{{ asset('img/zaptria-dark.webp') }}" 
                        alt="Zaptria" 
                        class="logo-light d-none"
                        height="50"
                    >

                    <img 
                        src="{{ asset('img/zaptria-white.webp') }}" 
                        alt="Zaptria" 
                        class="logo-dark d-none"
                        height="50"
                    >
                </picture>
                
                <h5 class="text-center mt-3 mb-2">Crie sua conta</h5>
                <p class="text-muted small text-center mb-4">
                    Automatize seu WhatsApp em minutos
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

                <form action="{{ route('register.post') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="company_name" class="form-label">Nome da Empresa</label>
                        <input
                            type="text"
                            name="company_name"
                            id="company_name"
                            class="form-control @error('company_name') is-invalid @enderror"
                            value="{{ old('company_name') }}"
                            placeholder="Sua Empresa Ltda"
                            required
                            autofocus
                        >
                        @error('company_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Seu Nome</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="João Silva"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="seu@email.com"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            required
                        >
                        @error('password_confirmation')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            Criar Conta e Continuar
                        </button>
                    </div>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <p class="small mb-2">
                        Já tem uma conta?
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                        Fazer login
                    </a>
                </div>

                <div class="alert alert-info small mt-3 mb-0">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Apenas <strong>R$ 297/mês</strong> • Sem período de teste • Cancele quando quiser
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
