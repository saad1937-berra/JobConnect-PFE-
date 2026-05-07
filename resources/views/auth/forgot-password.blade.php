@extends('layouts.app')
@section('title', 'Mot de passe oublié')

@push('styles')
<style>
    .auth-page {
        min-height: calc(100vh - 144px);
        display: flex; align-items: center; justify-content: center;
        padding: 3rem 1rem;
    }
    .auth-card {
        width: 100%; max-width: 440px;
        background: white; border: 1px solid var(--border);
        border-radius: 16px; padding: 2.5rem;
        box-shadow: 0 4px 40px rgba(0,0,0,0.06);
    }
    .auth-logo { font-family: var(--font-head); font-size: 1.6rem; font-weight: 800; text-align: center; margin-bottom: 0.5rem; }
    .auth-logo span { color: var(--accent); }
    .auth-subtitle { text-align: center; color: var(--muted); font-size: 0.9rem; margin-bottom: 2rem; line-height: 1.6; }
    .auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.88rem; color: var(--muted); }
    .auth-footer a { color: var(--accent2); text-decoration: none; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">Job<span>Connect</span></div>
        <p class="auth-subtitle">
            Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
        </p>

        @if(session('status'))
            <div class="flash flash-success" style="margin-bottom:1.5rem;">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="vous@exemple.com" required autofocus>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.75rem;">
                <i class="fas fa-paper-plane"></i> Envoyer le lien
            </button>
        </form>

        <div class="auth-footer">
            <a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Retour à la connexion</a>
        </div>
    </div>
</div>
@endsection