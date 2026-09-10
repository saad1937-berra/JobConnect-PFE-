@extends('layouts.app')
@section('title', 'Connexion')

@push('styles')
<style>
    .login-password { position:relative; }
    .login-password .form-control { padding-right:3.25rem; }
    .login-password-toggle { position:absolute; right:.3rem; top:50%; transform:translateY(-50%); display:grid; place-items:center; width:2.5rem; height:2.5rem; border:0; border-radius:8px; background:transparent; color:var(--muted, #68776d); cursor:pointer; }
    .login-password-toggle:hover { color:var(--accent, #22543d); background:#22543d0d; }
    .login-password-toggle:focus-visible { outline:2px solid var(--accent, #22543d); outline-offset:1px; }
</style>
@endpush

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
                <div class="login-password">
                    <input type="password" id="pass" name="pass"
                       class="form-control @error('pass') is-invalid @enderror"
                       placeholder="••••••••" autocomplete="current-password" required>
                    <button type="button" class="login-password-toggle" id="toggle-password"
                            aria-label="Afficher le mot de passe" aria-controls="pass" aria-pressed="false"
                            title="Afficher le mot de passe">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
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

@push('scripts')
<script>
    document.getElementById('toggle-password').addEventListener('click', function () {
        const password = document.getElementById('pass');
        const visible = password.type === 'password';
        password.type = visible ? 'text' : 'password';
        const label = visible ? 'Masquer le mot de passe' : 'Afficher le mot de passe';
        this.setAttribute('aria-label', label);
        this.setAttribute('aria-pressed', String(visible));
        this.title = label;
        this.querySelector('i').className = visible ? 'fas fa-eye-slash' : 'fas fa-eye';
    });
</script>
@endpush
