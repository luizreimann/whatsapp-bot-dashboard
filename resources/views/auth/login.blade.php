@extends('layouts.app')

@section('title', 'Login | Zaptria')

@section('content')
<div class="row justify-content-center align-content-center mt-5">
    <div class="col-md-5 col-lg-4">
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
                <p class="text-muted small text-center my-4">
                    Acesse o painel para gerenciar seus robôs de WhatsApp.
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

                <form action="{{ route('login.post') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            required
                            autofocus
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

                    <div class="mb-3 form-check">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="form-check-input"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="remember">
                            Manter-me conectado
                        </label>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            Entrar
                        </button>
                    </div>
                </form>
                
                <hr>
                <div class="text-center">
                    <p class="small mb-1">
                        Não tem uma conta?
                    </p>
                    <a href="#" class="btn btn-outline-primary w-100">
                        Cadastre-se
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection