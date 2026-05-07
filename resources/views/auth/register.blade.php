@extends('layouts.app')
@section('title', "S'inscrire")

@push('styles')
<style>
    .auth-page {
        min-height: calc(100vh - 144px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
    }

    .auth-card {
        width: 100%;
        max-width: 500px;
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 4px 40px rgba(0,0,0,0.06);
    }

    .auth-logo { font-family: var(--font-head); font-size: 1.6rem; font-weight: 800; text-align: center; margin-bottom: 0.5rem; }
    .auth-logo span { color: var(--accent); }
    .auth-subtitle { text-align: center; color: var(--muted); font-size: 0.9rem; margin-bottom: 2rem; }

    .role-selector { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem; }

    .role-option input[type="radio"] { display: none; }

    .role-option label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem;
        border: 2px solid var(--border);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }

    .role-option input:checked + label {
        border-color: var(--accent);
        background: rgba(232,76,30,0.04);
    }

    .role-option .role-icon { font-size: 1.8rem; }
    .role-option .role-name { font-family: var(--font-head); font-size: 0.9rem; font-weight: 700; }
    .role-option .role-desc { font-size: 0.75rem; color: var(--muted); }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    .auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.88rem; color: var(--muted); }
    .auth-footer a { color: var(--accent2); text-decoration: none; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">Job<span>Connect</span></div>
        <p class="auth-subtitle">Créez votre compte gratuitement</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Choix du rôle -->
            <div style="margin-bottom:1.2rem;">
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.75rem;">Je suis...</label>
                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" id="role_particulier" name="role" value="particulier"
                               {{ old('role', 'particulier') === 'particulier' ? 'checked' : '' }}>
                        <label for="role_particulier">
                            <span class="role-icon">👤</span>
                            <span class="role-name">Candidat</span>
                            <span class="role-desc">Je cherche un emploi</span>
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="role_entreprise" name="role" value="entreprise"
                               {{ old('role') === 'entreprise' ? 'checked' : '' }}>
                        <label for="role_entreprise">
                            <span class="role-icon">🏢</span>
                            <span class="role-name">Entreprise</span>
                            <span class="role-desc">Je recrute des talents</span>
                        </label>
                    </div>
                </div>
                @error('role')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <!-- Nom / Prénom -->
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" class="form-control @error('nom') is-invalid @enderror"
                           value="{{ old('nom') }}" placeholder="Alaoui" required>
                    @error('nom')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" class="form-control @error('prenom') is-invalid @enderror"
                           value="{{ old('prenom') }}" placeholder="Mohamed" required>
                    @error('prenom')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="vous@exemple.com" required>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="pass">Mot de passe</label>
                    <input type="password" id="pass" name="pass" class="form-control @error('pass') is-invalid @enderror"
                           placeholder="Min. 8 caractères" required>
                    @error('pass')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="pass_confirmation">Confirmer</label>
                    <input type="password" id="pass_confirmation" name="pass_confirmation" class="form-control"
                           placeholder="Répéter" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.75rem;margin-top:0.5rem;">
                Créer mon compte <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="auth-footer">
            Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a>
        </div>
    </div>
</div>
@endsection