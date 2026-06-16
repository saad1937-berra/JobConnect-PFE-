@extends('layouts.app')
@section('title', 'Mot de passe oublié')

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