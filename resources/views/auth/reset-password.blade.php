@extends('layouts.app')
@section('title', 'Réinitialiser le mot de passe')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">Job<span>Connect</span></div>
        <p class="auth-subtitle">Choisissez un nouveau mot de passe sécurisé.</p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', request('email')) }}"
                       placeholder="vous@exemple.com" required>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Min. 8 caractères" required
                       oninput="checkStrength(this.value)">
                @error('password')<p class="form-error">{{ $message }}</p>@enderror

                <div class="password-strength">
                    <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                    <span class="strength-label" id="strength-label"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-control" placeholder="Répéter" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.75rem;">
                <i class="fas fa-lock"></i> Réinitialiser le mot de passe
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function checkStrength(val) {
        const fill  = document.getElementById('strength-fill');
        const label = document.getElementById('strength-label');
        let score = 0;
        if (val.length >= 8)           score++;
        if (/[A-Z]/.test(val))         score++;
        if (/[0-9]/.test(val))         score++;
        if (/[^A-Za-z0-9]/.test(val))  score++;

        const levels = [
            { pct: '25%',  color: '#dc3545', text: 'Très faible' },
            { pct: '50%',  color: '#fd7e14', text: 'Faible'      },
            { pct: '75%',  color: '#ffc107', text: 'Moyen'       },
            { pct: '100%', color: '#28a745', text: 'Fort'        },
        ];
        const lvl = levels[Math.max(0, score - 1)];
        fill.style.width      = val.length ? lvl.pct   : '0';
        fill.style.background = val.length ? lvl.color : '';
        label.textContent     = val.length ? lvl.text  : '';
    }
</script>
@endpush
@endsection