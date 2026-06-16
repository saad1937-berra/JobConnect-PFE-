@extends('layouts.app')
@section('title', 'Connexion')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">Job<span>Connect</span></div>
        <p class="auth-subtitle">Connectez-vous à votre compte</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="vous@exemple.com" required autofocus>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label for="pass" style="display:flex;justify-content:space-between;">
                    Mot de passe
                    <a href="{{ route('password.request') }}" style="font-weight:400;color:var(--accent2);font-size:0.82rem;">Oublié ?</a>
                </label>
                <input type="password" id="pass" name="pass"
                       class="form-control @error('pass') is-invalid @enderror"
                       placeholder="••••••••" required>
                @error('pass')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.5rem;">
                <input type="checkbox" id="remember" name="remember" style="cursor:pointer;">
                <label for="remember" style="font-size:0.88rem;cursor:pointer;color:var(--muted);">Se souvenir de moi</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.75rem;">
                Se connecter
            </button>
        </form>

        <div class="auth-footer">
            Pas encore de compte ? <a href="{{ route('register') }}">S'inscrire</a>
        </div>
    </div>
</div>
@endsection