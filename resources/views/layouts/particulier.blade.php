<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JobConnect') — Espace Candidat</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/particulier.css') }}">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">

    @stack('styles')
</head>

<body class="part-body">

    <!-- Topbar -->
    <header class="part-topbar">
        <a href="{{ route('home') }}" class="part-logo">
            Job<span>Connect</span>
        </a>

        <nav class="part-topnav">
            <a href="{{ route('home') }}" class="part-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                Accueil
            </a>
            <a href="{{ route('offres.index') }}" class="part-nav-link {{ request()->routeIs('offres.*') ? 'active' : '' }}">
                Offres
            </a>
            <a href="{{ route('particulier.suggestions') }}" class="part-nav-link {{ request()->routeIs('particulier.suggestions') ? 'active' : '' }}">
                Suggestions
            </a>
            <a href="{{ route('particulier.matching') }}" class="part-nav-link {{ request()->routeIs('particulier.matching*') ? 'active' : '' }}">
                Matching
            </a>
            <a href="{{ route('particulier.candidatures') }}" class="part-nav-link {{ request()->routeIs('particulier.candidatures') ? 'active' : '' }}">
                Mes candidatures
            </a>
        </nav>

        <div class="part-topbar-right">
            {{-- Notifications --}}
            <a href="{{ route('notifications.index') }}" class="part-icon-btn" style="position:relative;">
                <i class="fas fa-bell"></i>
                @php $nonLues = auth()->user()->notifications()->whereNull('date_lecture')->count(); @endphp
                @if($nonLues > 0)
                    <span class="part-notif-badge">{{ $nonLues > 99 ? '99+' : $nonLues }}</span>
                @endif
            </a>

            {{-- Avatar / profil --}}
            <a href="{{ route('particulier.profil') }}" class="part-avatar-btn">
                @if(auth()->user()->particulier?->photo)
                    <img src="{{ asset('storage/'.auth()->user()->particulier->photo) }}" alt="">
                @else
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                @endif
            </a>

            {{-- Déconnexion --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="part-logout-btn" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="part-main">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="part-flash part-flash-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;font-size:1rem;">×</button>
            </div>
        @endif
        @if(session('error'))
            <div class="part-flash part-flash-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;font-size:1rem;">×</button>
            </div>
        @endif

        @yield('part-content')
    </main>

    <footer class="part-footer">
        <span>© {{ date('Y') }} <strong>JobConnect</strong></span>
        <span style="color:#aaa;">Trouve ton job. Fais ta vie.</span>
    </footer>

    @stack('scripts')
</body>

</html>