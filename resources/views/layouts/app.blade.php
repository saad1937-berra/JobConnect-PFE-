<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JobConnect') — Trouvez votre voie</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Styles globaux --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Styles spécifiques par rôle --}}
    @auth
        @if(auth()->user()->isParticulier())
            <link rel="stylesheet" href="{{ asset('css/particulier.css') }}">
        @elseif(auth()->user()->isEntreprise())
            <link rel="stylesheet" href="{{ asset('css/entreprise.css') }}">
        @elseif(auth()->user()->isAdmin())
            <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
        @endif
    @else
        <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    @endauth

    {{-- Styles spécifiques à une page (si besoin) --}}
    @stack('styles')
</head>

<body>

    <nav>
        <a href="{{ route('home') }}" class="nav-logo">Job<span>Connect</span></a>
        <ul class="nav-links">
            <li><a href="{{ route('offres.index') }}">Offres</a></li>

            @guest
                <li><a href="{{ route('login') }}">Connexion</a></li>
                <li><a href="{{ route('register') }}" class="btn-nav">S'inscrire</a></li>
            @endguest

            @auth
                @if (auth()->user()->isParticulier())
                    <li><a href="{{ route('particulier.profil') }}">Mon Profil</a></li>
                    <li><a href="{{ route('particulier.candidatures') }}">Mes Candidatures</a></li>
                @elseif(auth()->user()->isEntreprise())
                    <li><a href="{{ route('entreprise.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('entreprise.offres') }}">Mes Offres</a></li>
                @elseif(auth()->user()->isAdmin())
                    <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                @endif

                <li>
                    <a href="{{ route('messages.index') }}" style="position:relative;padding:0.5rem 0.75rem;">
                        <i class="fas fa-comments"></i>
                        @php $messagesNonLus = auth()->user()->unreadMessagesCount(); @endphp
                        @if ($messagesNonLus > 0)
                            <span class="nav-bell-badge">{{ $messagesNonLus > 99 ? '99+' : $messagesNonLus }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('notifications.index') }}" style="position:relative;padding:0.5rem 0.75rem;">
                        <i class="fas fa-bell"></i>
                        @php
                            $nonLues = auth()->user()->notifications()->whereNull('date_lecture')->count();
                        @endphp
                        @if ($nonLues > 0)
                            <span class="nav-bell-badge">{{ $nonLues > 99 ? '99+' : $nonLues }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="nav-logout-btn">
                            Déconnexion
                        </button>
                    </form>
                </li>
            @endauth
        </ul>
    </nav>

    <main>
        @if (session('success'))
            <div class="flash flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash flash-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div class="flash flash-info"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>
        @endif

        @yield('content')
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} <a href="#">JobConnect</a> — Tous droits réservés</p>
    </footer>

    @stack('scripts')
</body>

</html>
