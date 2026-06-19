<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JobConnect') — Espace Recruteur</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/entreprise.css') }}">

    @stack('styles')
</head>

<body class="ent-body">

    <!-- Topbar -->
    <header class="ent-topbar">
        <a href="{{ route('home') }}" class="ent-logo">
            Job<span>Connect</span>
            <span class="ent-logo-tag">Recruteur</span>
        </a>

        <div class="ent-topbar-right">
            <a href="{{ route('messages.index') }}" class="ent-icon-btn" style="position:relative;">
                <i class="fas fa-comments"></i>
                @php $messagesNonLus = auth()->user()->unreadMessagesCount(); @endphp
                @if($messagesNonLus > 0)
                    <span class="ent-notif-badge">{{ $messagesNonLus > 99 ? '99+' : $messagesNonLus }}</span>
                @endif
            </a>

            {{-- Notifications --}}
            <a href="{{ route('notifications.index') }}" class="ent-icon-btn" style="position:relative;">
                <i class="fas fa-bell"></i>
                @php $nonLues = auth()->user()->notifications()->whereNull('date_lecture')->count(); @endphp
                @if($nonLues > 0)
                    <span class="ent-notif-badge">{{ $nonLues > 99 ? '99+' : $nonLues }}</span>
                @endif
            </a>

            {{-- User info --}}
            <div class="ent-user-chip">
                <div class="ent-user-avatar">
                    @if(auth()->user()->entreprise?->logo)
                        <img src="{{ asset('storage/'.auth()->user()->entreprise->logo) }}" alt="">
                    @else
                        {{ strtoupper(substr(auth()->user()->entreprise?->nom ?? 'E', 0, 2)) }}
                    @endif
                </div>
                <div class="ent-user-info">
                    <span class="ent-user-name">{{ auth()->user()->entreprise?->nom ?? auth()->user()->prenom }}</span>
                    <span class="ent-user-role">Recruteur</span>
                </div>
            </div>

            {{-- Déconnexion --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="ent-logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </header>

    <div class="ent-wrapper">

        <!-- Sidebar -->
        <aside class="ent-sidebar">
            <nav class="ent-nav">
                <div class="ent-nav-section">
                    <span class="ent-nav-label">Principal</span>
                    <a href="{{ route('entreprise.dashboard') }}" class="ent-nav-link {{ request()->routeIs('entreprise.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> Dashboard
                    </a>
                    <a href="{{ route('entreprise.profil') }}" class="ent-nav-link {{ request()->routeIs('entreprise.profil') ? 'active' : '' }}">
                        <i class="fas fa-building"></i> Profil entreprise
                    </a>
                </div>

                <div class="ent-nav-section">
                    <span class="ent-nav-label">Offres</span>
                    <a href="{{ route('entreprise.offres') }}" class="ent-nav-link {{ request()->routeIs('entreprise.offres') ? 'active' : '' }}">
                        <i class="fas fa-briefcase"></i> Mes offres
                    </a>
                    <a href="{{ route('entreprise.offres.creer') }}" class="ent-nav-link {{ request()->routeIs('entreprise.offres.creer') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> Publier une offre
                    </a>
                </div>

                <div class="ent-nav-section">
                    <span class="ent-nav-label">Candidats</span>
                    <a href="{{ route('entreprise.candidatures') }}" class="ent-nav-link {{ request()->routeIs('entreprise.candidatures') || request()->routeIs('entreprise.candidature.show') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Candidatures
                    </a>
                </div>

                <div class="ent-nav-section" style="margin-top:auto;padding-top:1rem;border-top:1px solid var(--ent-border);">
                    @php $adminContact = \App\Models\Utilisateur::where('role', 'admin')->first(); @endphp
                    @if($adminContact)
                        <form method="POST" action="{{ route('messages.start') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $adminContact->id }}">
                            <button type="submit" class="ent-nav-link" style="width:100%;border:0;background:transparent;cursor:pointer;text-align:left;">
                                <i class="fas fa-headset"></i> Contacter admin
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('messages.index') }}" class="ent-nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                        <i class="fas fa-comments"></i> Messages
                        @if($messagesNonLus > 0)
                            <span class="ent-nav-badge">{{ $messagesNonLus }}</span>
                        @endif
                    </a>
                    <a href="{{ route('notifications.index') }}" class="ent-nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                        <i class="fas fa-bell"></i> Notifications
                        @if($nonLues > 0)
                            <span class="ent-nav-badge">{{ $nonLues }}</span>
                        @endif
                    </a>
                    <a href="{{ route('privacy') }}" class="ent-nav-link {{ request()->routeIs('privacy') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt"></i> Confidentialite
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <main class="ent-main">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="ent-flash ent-flash-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="ent-flash ent-flash-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @yield('ent-content')
        </main>

    </div>

    @stack('scripts')
</body>

</html>
