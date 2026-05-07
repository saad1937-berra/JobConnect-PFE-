<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JobConnect') — Trouvez votre voie</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:       #0d0d0d;
            --paper:     #f5f2eb;
            --accent:    #e84c1e;
            --accent2:   #1e6fe8;
            --muted:     #6b6b6b;
            --border:    #e0dbd0;
            --card:      #ffffff;
            --radius:    12px;
            --font-head: 'Syne', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background: var(--paper);
            color: var(--ink);
            min-height: 100vh;
        }

        /* ── Navbar ── */
        nav {
            background: var(--ink);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-logo {
            font-family: var(--font-head);
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--paper);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .nav-logo span { color: var(--accent); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
        }

        .nav-links a {
            color: #aaa;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.5rem 0.9rem;
            border-radius: 6px;
            transition: color 0.2s, background 0.2s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--paper);
            background: rgba(255,255,255,0.08);
        }

        .btn-nav {
            background: var(--accent) !important;
            color: white !important;
            padding: 0.45rem 1.1rem !important;
            border-radius: 6px !important;
        }

        .btn-nav:hover { opacity: 0.9; background: var(--accent) !important; }

        /* ── Flash messages ── */
        .flash {
            padding: 0.85rem 1.5rem;
            border-radius: var(--radius);
            margin: 1rem 2rem;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .flash-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .flash-error   { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .flash-info    { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }

        /* ── Main content ── */
        main { min-height: calc(100vh - 64px - 80px); }

        /* ── Footer ── */
        footer {
            background: var(--ink);
            color: #888;
            text-align: center;
            padding: 1.5rem;
            font-size: 0.85rem;
        }

        footer a { color: var(--accent); text-decoration: none; }

        /* ── Utilities ── */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
        .container-sm { max-width: 680px; margin: 0 auto; padding: 0 2rem; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.4rem;
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary   { background: var(--accent); color: white; }
        .btn-primary:hover { opacity: 0.88; transform: translateY(-1px); }
        .btn-secondary { background: var(--ink); color: white; }
        .btn-secondary:hover { opacity: 0.85; }
        .btn-outline   { background: transparent; color: var(--ink); border: 1.5px solid var(--border); }
        .btn-outline:hover { border-color: var(--ink); }
        .btn-sm        { padding: 0.4rem 0.9rem; font-size: 0.82rem; }
        .btn-danger    { background: #dc3545; color: white; }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.7rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .badge-green  { background: #d4edda; color: #155724; }
        .badge-red    { background: #f8d7da; color: #721c24; }
        .badge-blue   { background: #d1ecf1; color: #0c5460; }
        .badge-yellow { background: #fff3cd; color: #856404; }
        .badge-gray   { background: #e9ecef; color: #495057; }

        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem; color: var(--ink); }
        .form-control {
            width: 100%;
            padding: 0.65rem 0.9rem;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 0.9rem;
            background: white;
            transition: border-color 0.2s;
            color: var(--ink);
        }
        .form-control:focus { outline: none; border-color: var(--accent2); }
        .form-error { font-size: 0.8rem; color: #dc3545; margin-top: 0.3rem; }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .container { padding: 0 1rem; }
        }
    </style>

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
            @if(auth()->user()->isParticulier())
                <li><a href="{{ route('particulier.profil') }}">Mon Profil</a></li>
                <li><a href="{{ route('particulier.candidatures') }}">Mes Candidatures</a></li>
            @elseif(auth()->user()->isEntreprise())
                <li><a href="{{ route('entreprise.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('entreprise.offres') }}">Mes Offres</a></li>
            @elseif(auth()->user()->isAdmin())
                <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            @endif

            <li>
                <a href="{{ route('notifications.index') }}">
                    <i class="fas fa-bell"></i>
                </a>
            </li>

            <li>
                <form action="{{ route('logout') }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:#aaa;font-family:var(--font-body);font-size:0.9rem;padding:0.5rem 0.9rem;">
                        Déconnexion
                    </button>
                </form>
            </li>
        @endauth
    </ul>
</nav>

<main>
    @if(session('success'))
        <div class="flash flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if(session('info'))
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