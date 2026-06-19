@extends('layouts.app')
@section('title', 'Accueil — JobConnect')

@push('styles')
    <style>
        .hero {
            background: var(--ink);
            color: var(--paper);
            padding: 6rem 2rem 5rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 70% 50%, rgba(232, 76, 30, 0.15) 0%, transparent 60%);
            pointer-events: none;
        }

        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero-tag {
            display: inline-block;
            background: rgba(232, 76, 30, 0.15);
            color: var(--accent);
            border: 1px solid rgba(232, 76, 30, 0.3);
            padding: 0.3rem 0.9rem;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 1.5rem;
        }

        .hero h1 {
            font-family: var(--font-head);
            font-size: clamp(2.8rem, 6vw, 5rem);
            font-weight: 800;
            line-height: 1.05;
            margin-bottom: 1.5rem;
            letter-spacing: -2px;
        }

        .hero h1 em {
            font-style: normal;
            color: var(--accent);
        }

        .hero p {
            font-size: 1.15rem;
            color: #aaa;
            max-width: 520px;
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .hero-stat {
            display: flex;
            gap: 3rem;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .hero-stat strong {
            display: block;
            font-family: var(--font-head);
            font-size: 2rem;
            font-weight: 800;
            color: var(--paper);
        }

        .hero-stat span {
            font-size: 0.85rem;
            color: #777;
        }

        .search-section {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 1.5rem 2rem;
        }

        .search-bar {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            gap: 0.75rem;
            background: var(--paper);
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 0.5rem;
        }

        .search-bar input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 0.5rem 0.75rem;
            font-family: var(--font-body);
            font-size: 0.95rem;
            color: var(--ink);
            outline: none;
        }

        .search-bar select {
            border: none;
            background: white;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            font-family: var(--font-body);
            font-size: 0.9rem;
            color: var(--ink);
            cursor: pointer;
            outline: none;
        }

        .section {
            padding: 4rem 0;
        }

        .section-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .section-header h2 {
            font-family: var(--font-head);
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .offres-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.25rem;
        }

        .offre-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .offre-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border-color: var(--accent);
        }

        .offre-card-header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .company-logo {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: var(--paper);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-head);
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--accent);
            flex-shrink: 0;
            overflow: hidden;
        }

        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .offre-card h3 {
            font-family: var(--font-head);
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .offre-card .company-name {
            font-size: 0.85rem;
            color: var(--muted);
        }

        .offre-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.78rem;
            color: var(--muted);
            padding: 0.2rem 0.5rem;
            background: var(--paper);
            border-radius: 5px;
        }

        .offre-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        .offre-date {
            font-size: 0.78rem;
            color: var(--muted);
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
        }

        .categorie-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem 1.25rem;
            text-align: center;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }

        .categorie-card:hover {
            border-color: var(--accent);
            background: var(--ink);
            color: var(--paper);
        }

        .categorie-card .icon {
            font-size: 1.8rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .categorie-card h4 {
            font-family: var(--font-head);
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .categorie-card span {
            font-size: 0.8rem;
            color: var(--muted);
        }

        .categorie-card:hover span {
            color: #aaa;
        }

        .cta-banner {
            background: var(--accent);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: var(--radius);
            margin: 2rem 0;
        }

        .cta-banner h2 {
            font-family: var(--font-head);
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .cta-banner p {
            font-size: 1rem;
            opacity: 0.85;
            margin-bottom: 2rem;
        }

        .btn-white {
            background: white;
            color: var(--accent) !important;
        }

        .stat-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .stat-card strong {
            display: block;
            font-family: var(--font-head);
            font-size: 1.7rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.2rem;
        }

        .stat-card span {
            font-size: 0.8rem;
            color: var(--muted);
        }

        .table-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .table-header h3 {
            font-family: var(--font-head);
            font-size: 1rem;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: .75rem 1.5rem;
            text-align: left;
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .4px;
            background: var(--paper);
            border-bottom: 1px solid var(--border);
        }

        tbody td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-size: .9rem;
            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #fafafa;
        }

        .home-page-entreprise .hero {
            background: var(--ent-green, #1a4d3a);
        }

        .home-page-entreprise .hero::before {
            background: radial-gradient(ellipse at 70% 50%, rgba(22, 160, 133, 0.25) 0%, transparent 60%);
        }

        .home-page-entreprise .hero-tag,
        .home-page-entreprise .hero h1 em {
            color: var(--ent-accent, #16a085);
        }

        .home-page-entreprise .btn-primary {
            background: var(--ent-green, #1a4d3a);
        }

        .home-page-admin .hero {
            background: #111827;
        }

        .home-page-admin .hero::before {
            background: radial-gradient(ellipse at 70% 50%, rgba(30, 111, 232, 0.22) 0%, transparent 60%);
        }

        .home-page-admin .hero-tag,
        .home-page-admin .hero h1 em {
            color: #60a5fa;
        }

        .home-page-admin .btn-primary {
            background: #1e40af;
        }

        .home-page-particulier .hero {
            background: #151515;
        }

        .home-page-particulier .hero-tag,
        .home-page-particulier .hero h1 em {
            color: var(--accent, #e84c1e);
        }
    </style>
@endpush

@section('content')
    <div class="home-page home-page-{{ auth()->check() ? auth()->user()->role : 'guest' }}">

    @auth

        {{-- CANDIDAT --}}
        @if (auth()->user()->isParticulier())
            @php $particulier = auth()->user()->particulier; @endphp

            <section class="hero">
                <div class="hero-inner">
                    <div class="hero-tag">👋 Bienvenue sur JobConnect</div>
                    <h1>Bonjour, <em>{{ auth()->user()->prenom }}</em> !</h1>
                    <p>Découvrez les dernières offres disponibles et suivez vos candidatures en temps réel.</p>
                    <div class="hero-actions">
                        <a href="{{ route('offres.index') }}" class="btn btn-primary"
                            style="font-size:1rem;padding:0.8rem 1.8rem;"><i class="fas fa-search"></i> Chercher un emploi</a>
                        <a href="{{ route('particulier.candidatures') }}" class="btn btn-outline"
                            style="color:var(--paper);border-color:rgba(255,255,255,0.2);font-size:1rem;padding:0.8rem 1.8rem;"><i
                                class="fas fa-paper-plane"></i> Mes candidatures</a>
                    </div>
                    <div class="hero-stat">
                        <div><strong>{{ $stats['candidatures'] ?? 0 }}</strong><span>Candidatures</span></div>
                        <div><strong>{{ $stats['acceptees'] ?? 0 }}</strong><span>Acceptées</span></div>
                        <div><strong>{{ $stats['en_attente'] ?? 0 }}</strong><span>En attente</span></div>
                        <div><strong>{{ $stats['competances'] ?? 0 }}</strong><span>Compétences</span></div>
                    </div>
                </div>
            </section>

            <div class="search-section">
                <form action="{{ route('offres.index') }}" method="GET">
                    <div class="search-bar">
                        <i class="fas fa-search"
                            style="padding:0.5rem 0 0.5rem 0.75rem;color:var(--muted);align-self:center;"></i>
                        <input type="text" name="search" placeholder="Titre du poste, mot-clé..."
                            value="{{ request('search') }}">
                        <select name="localisation">
                            <option value="">Toutes les villes</option>
                            <option value="Casablanca">Casablanca</option>
                            <option value="Rabat">Rabat</option>
                            <option value="Marrakech">Marrakech</option>
                            <option value="Remote">Remote</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Rechercher</button>
                    </div>
                </form>
            </div>

            <div class="container" style="padding-top:2.5rem;">
                <div style="display:grid;grid-template-columns:1fr 320px;gap:2rem;align-items:start;">

                    <div>
                        {{-- Offres récentes --}}
                        <div class="section-header">
                            <h2>Offres récentes</h2>
                            <a href="{{ route('offres.index') }}" class="btn btn-outline btn-sm">Voir tout <i
                                    class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="offres-grid" style="margin-bottom:3rem;">
                            @forelse($offres ?? [] as $offre)
                                <a href="{{ route('offres.show', $offre->id) }}" class="offre-card">
                                    <div class="offre-card-header">
                                        <div class="company-logo">
                                            @if ($offre->entreprise->logo)
                                                <img src="{{ asset('storage/' . $offre->entreprise->logo) }}" alt="">
                                            @else
                                                {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <h3>{{ $offre->titre }}</h3>
                                            <div class="company-name">{{ $offre->entreprise->nom }}</div>
                                        </div>
                                    </div>
                                    <div class="offre-meta">
                                        @if ($offre->localisation)
                                            <span class="meta-chip"><i class="fas fa-map-marker-alt"></i>
                                                {{ $offre->localisation }}</span>
                                        @endif
                                        @if ($offre->contrat)
                                            <span class="meta-chip"><i class="fas fa-briefcase"></i>
                                                {{ $offre->contrat }}</span>
                                        @endif
                                    </div>
                                    <div class="offre-card-footer">
                                        <span class="offre-date">{{ $offre->date_publication->diffForHumans() }}</span>
                                        <span class="badge badge-green">{{ $offre->categorie->nom ?? 'Autre' }}</span>
                                    </div>
                                </a>
                            @empty
                                <p style="color:var(--muted);grid-column:1/-1;">Aucune offre disponible.</p>
                            @endforelse
                        </div>

                        {{-- Suggestions --}}
                        @if (isset($suggestions) && $suggestions->count() > 0)
                            <div class="section-header">
                                <h2><i class="fas fa-magic" style="color:var(--accent);margin-right:0.5rem;"></i> Suggérées pour
                                    vous</h2>
                                <a href="{{ route('particulier.suggestions') }}" class="btn btn-outline btn-sm">Voir tout</a>
                            </div>
                            <div class="offres-grid">
                                @foreach ($suggestions->take(3) as $item)
                                    <a href="{{ route('offres.show', $item['offre']->id) }}" class="offre-card"
                                        style="position:relative;border-color:rgba(232,76,30,0.3);">
                                        <span
                                            style="position:absolute;top:1rem;right:1rem;background:var(--accent);color:white;font-size:0.72rem;font-weight:700;padding:0.15rem 0.55rem;border-radius:20px;">
                                            <i class="fas fa-star"></i> {{ $item['score'] }}
                                            match{{ $item['score'] > 1 ? 's' : '' }}
                                        </span>
                                        <div class="offre-card-header">
                                            <div class="company-logo">
                                                @if ($item['offre']->entreprise->logo)
                                                    <img src="{{ asset('storage/' . $item['offre']->entreprise->logo) }}"
                                                        alt="">
                                                @else
                                                    {{ strtoupper(substr($item['offre']->entreprise->nom, 0, 2)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <h3>{{ $item['offre']->titre }}</h3>
                                                <div class="company-name">{{ $item['offre']->entreprise->nom }}</div>
                                            </div>
                                        </div>
                                        @if (count($item['matched']) > 0)
                                            <div style="display:flex;flex-wrap:wrap;gap:0.3rem;margin-bottom:0.5rem;">
                                                @foreach ($item['matched'] as $comp)
                                                    <span
                                                        style="background:#d4edda;color:#155724;padding:0.15rem 0.55rem;border-radius:5px;font-size:0.72rem;font-weight:600;">
                                                        <i class="fas fa-check"></i> {{ $comp }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="offre-meta">
                                            @if ($item['offre']->localisation)
                                                <span class="meta-chip"><i class="fas fa-map-marker-alt"></i>
                                                    {{ $item['offre']->localisation }}</span>
                                            @endif
                                            @if ($item['offre']->contrat)
                                                <span class="badge badge-blue">{{ $item['offre']->contrat }}</span>
                                            @endif
                                        </div>
                                        <div class="offre-card-footer">
                                            <span
                                                class="offre-date">{{ $item['offre']->date_publication->diffForHumans() }}</span>
                                            <span
                                                class="badge badge-green">{{ $item['offre']->categorie->nom ?? 'Autre' }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @elseif($particulier->competances->count() === 0)
                            <div
                                style="background:#fff3cd;border:1px solid #ffc107;border-radius:var(--radius);padding:1rem 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                                <span style="font-size:0.88rem;color:#856404;"><i class="fas fa-lightbulb"></i> Ajoutez des
                                    compétences pour recevoir des suggestions personnalisées.</span>
                                <a href="{{ route('particulier.profil') }}" class="btn btn-primary btn-sm">Ajouter</a>
                            </div>
                        @endif
                        {{-- Matching rapide --}}
                    <div style="margin-top:2rem;">
                        <div class="section-header">
                            <h2><i class="fas fa-percentage" style="color:var(--accent);margin-right:0.5rem;"></i> Matching</h2>
                            <a href="{{ route('particulier.matching') }}" class="btn btn-outline btn-sm">Voir tout</a>
                        </div>
                        <div class="offres-grid">
                            @forelse($offresMatchees ?? [] as $offre)
                                <a href="{{ route('particulier.matching.score', $offre->id) }}" class="offre-card"
                                    style="position:relative;">
                                    {{-- Jauge --}}
                                    <div style="position:absolute;top:1rem;right:1rem;text-align:center;">
                                        <div
                                            style="font-family:var(--font-head);font-size:1.1rem;font-weight:800;color:{{ $offre->matching['couleur'] }};">
                                            {{ $offre->matching['score'] }}%
                                        </div>
                                        <div
                                            style="width:50px;height:5px;background:var(--paper);border-radius:3px;margin-top:3px;">
                                            <div
                                                style="width:{{ $offre->matching['score'] }}%;height:5px;border-radius:3px;background:{{ $offre->matching['couleur'] }};">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="offre-card-header" style="padding-right:4rem;">
                                        <div class="company-logo">
                                            @if ($offre->entreprise->logo)
                                                <img src="{{ asset('storage/' . $offre->entreprise->logo) }}" alt="">
                                            @else
                                                {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <h3>{{ $offre->titre }}</h3>
                                            <div class="company-name">{{ $offre->entreprise->nom }}</div>
                                        </div>
                                    </div>

                                    {{-- Compétences matchées --}}
                                    @if (count($offre->matching['criteres']['competences']['detail']) > 0)
                                        <div style="display:flex;flex-wrap:wrap;gap:0.3rem;margin-bottom:0.5rem;">
                                            @foreach (array_slice($offre->matching['criteres']['competences']['detail'], 0, 3) as $comp)
                                                <span
                                                    style="background:#d4edda;color:#155724;padding:0.15rem 0.55rem;border-radius:5px;font-size:0.72rem;font-weight:600;">
                                                    <i class="fas fa-check"></i> {{ $comp }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="offre-meta">
                                        @if ($offre->localisation)
                                            <span class="meta-chip"><i class="fas fa-map-marker-alt"></i>
                                                {{ $offre->localisation }}</span>
                                        @endif
                                        @if ($offre->contrat)
                                            <span class="badge badge-blue">{{ $offre->contrat }}</span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div
                                    style="grid-column:1/-1;padding:1.5rem;background:white;border:1px solid var(--border);border-radius:var(--radius);text-align:center;color:var(--muted);">
                                    <i class="fas fa-info-circle"></i>
                                    Ajoutez des compétences pour voir vos offres matchées.
                                    <a href="{{ route('particulier.profil') }}"
                                        style="color:var(--accent2);margin-left:0.5rem;">Compléter le profil</a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    </div>
                    

                    {{-- Sidebar --}}
                    <div>
                        @php
                            $checks = [
                                'Bio renseignée' => !empty($particulier->bio),
                                'Téléphone' => !empty($particulier->tel),
                                'Adresse' => !empty($particulier->adresse),
                                'CV uploadé' => $particulier->cv->count() > 0,
                                'Compétences ajoutées' => $particulier->competances->count() > 0,
                            ];
                            $score = round((collect($checks)->filter()->count() / count($checks)) * 100);
                        @endphp
                        <div
                            style="background:white;border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.25rem;">
                            <h3 style="font-family:var(--font-head);font-size:1rem;font-weight:700;margin-bottom:1rem;"><i
                                    class="fas fa-user-check" style="color:var(--accent);margin-right:0.5rem;"></i> Profil
                                complété</h3>
                            <div style="display:flex;justify-content:space-between;font-size:0.82rem;margin-bottom:0.4rem;">
                                <span>Progression</span><strong>{{ $score }}%</strong></div>
                            <div style="background:var(--paper);border-radius:6px;height:8px;margin-bottom:1rem;">
                                <div
                                    style="width:{{ $score }}%;height:8px;border-radius:6px;background:{{ $score >= 80 ? '#28a745' : ($score >= 50 ? '#ffc107' : 'var(--accent)') }};">
                                </div>
                            </div>
                            @foreach ($checks as $label => $done)
                                <div
                                    style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;font-size:0.85rem;">
                                    <i class="fas {{ $done ? 'fa-check-circle' : 'fa-times-circle' }}"
                                        style="color:{{ $done ? '#28a745' : '#dc3545' }};"></i> {{ $label }}
                                </div>
                            @endforeach
                            @if ($score < 100)
                                <a href="{{ route('particulier.profil') }}" class="btn btn-primary btn-sm"
                                    style="width:100%;justify-content:center;margin-top:0.75rem;">Compléter mon profil</a>
                            @endif
                        </div>
                        <div class="table-card">
                            <div class="table-header">
                                <h3>Mes candidatures</h3>
                                <a href="{{ route('particulier.candidatures') }}" class="btn btn-outline btn-sm">Voir
                                    tout</a>
                            </div>
                            @forelse($dernieresCandidatures ?? [] as $cand)
                                @php
                                    $bc = match ($cand->statut) {
                                        'acceptee' => 'badge-green',
                                        'refusee' => 'badge-red',
                                        'en_cours' => 'badge-blue',
                                        default => 'badge-yellow',
                                    };
                                    $bl = match ($cand->statut) {
                                        'acceptee' => 'Acceptée',
                                        'refusee' => 'Refusée',
                                        'en_cours' => 'En cours',
                                        default => 'En attente',
                                    };
                                @endphp
                                <div
                                    style="padding:0.85rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:0.75rem;">
                                    <div>
                                        <div style="font-weight:600;font-size:0.88rem;">
                                            {{ Str::limit($cand->offre->titre, 28) }}</div>
                                        <div style="font-size:0.75rem;color:var(--muted);">{{ $cand->offre->entreprise->nom }}
                                        </div>
                                    </div>
                                    <span class="badge {{ $bc }}">{{ $bl }}</span>
                                </div>
                            @empty
                                <p style="padding:1.5rem;color:var(--muted);text-align:center;font-size:0.88rem;">Aucune
                                    candidature.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- ENTREPRISE --}}
        @elseif(auth()->user()->isEntreprise())
            @php $entreprise = auth()->user()->entreprise; @endphp

            <section class="hero">
                <div class="hero-inner">
                    <div class="hero-tag">🏢 Espace Recruteur</div>
                    <h1>Bienvenue, <em>{{ $entreprise->nom }}</em></h1>
                    <p>Gérez vos offres d'emploi, consultez les candidatures et trouvez les meilleurs talents.</p>
                    <div class="hero-actions">
                        <a href="{{ route('entreprise.offres.creer') }}" class="btn btn-primary"
                            style="font-size:1rem;padding:0.8rem 1.8rem;"><i class="fas fa-plus"></i> Publier une offre</a>
                        <a href="{{ route('entreprise.candidatures') }}" class="btn btn-outline"
                            style="color:var(--paper);border-color:rgba(255,255,255,0.2);font-size:1rem;padding:0.8rem 1.8rem;"><i
                                class="fas fa-users"></i> Voir les candidatures</a>
                    </div>
                    <div class="hero-stat">
                        <div><strong>{{ $stats['total_offres'] ?? 0 }}</strong><span>Offres publiées</span></div>
                        <div><strong>{{ $stats['offres_actives'] ?? 0 }}</strong><span>Offres actives</span></div>
                        <div><strong>{{ $stats['total_candidatures'] ?? 0 }}</strong><span>Candidatures reçues</span></div>
                    </div>
                </div>
            </section>

            <div class="container" style="padding-top:2.5rem;">
                <div class="table-card" style="margin-bottom:1.25rem;">
                    <div class="table-header">
                        <h3><i class="fas fa-briefcase" style="color:var(--accent);margin-right:0.5rem;"></i> Mes offres
                            récentes</h3>
                        <a href="{{ route('entreprise.offres.creer') }}" class="btn btn-primary btn-sm"><i
                                class="fas fa-plus"></i> Nouvelle offre</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Poste</th>
                                <th>Contrat</th>
                                <th>Candidatures</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($offres ?? [] as $offre)
                                <tr>
                                    <td>
                                        <div style="font-weight:600;">{{ $offre->titre }}</div>
                                        <div style="font-size:0.78rem;color:var(--muted);">{{ $offre->localisation }}</div>
                                    </td>
                                    <td><span class="badge badge-blue">{{ $offre->contrat ?? '—' }}</span></td>
                                    <td><strong>{{ $offre->candidatures_count }}</strong></td>
                                    <td>
                                        @if ($offre->statut === 'active')
                                            <span class="badge badge-green">Active</span>
                                        @elseif($offre->statut === 'expiree')
                                            <span class="badge badge-red">Expirée</span>
                                        @else
                                            <span class="badge badge-gray">Brouillon</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:0.4rem;">
                                            <a href="{{ route('entreprise.offres.edit', $offre->id) }}"
                                                class="btn btn-outline btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="{{ route('entreprise.offres.suggestions', $offre->id) }}"
                                                class="btn btn-outline btn-sm" title="Candidats suggérés"><i
                                                    class="fas fa-magic"></i></a>
                                            <a href="{{ route('entreprise.offres.matching', $offre->id) }}"
                                                class="btn btn-outline btn-sm" title="Matching"><i
                                                    class="fas fa-chart-line"></i></a>
                                            <form method="POST"
                                                action="{{ route('entreprise.offres.supprimer', $offre->id) }}"
                                                onsubmit="return confirm('Supprimer ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;padding:2rem;color:var(--muted);">Aucune offre
                                        publiée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-bottom:2rem;">
                    <div class="section-header">
                        <h2><i class="fas fa-percentage" style="color:var(--accent);margin-right:0.5rem;"></i> Matching</h2>
                        <a href="{{ route('entreprise.offres') }}" class="btn btn-outline btn-sm">Voir mes offres</a>
                    </div>

                    <div class="offres-grid">
                        @forelse($candidatsMatches ?? [] as $item)
                            @php
                                $particulier = $item['particulier'];
                                $offre = $item['offre'];
                                $matching = $item['matching'];
                                $score = $matching['score'];
                                $couleur = $matching['couleur'];
                                $matched = $matching['criteres']['competences']['detail'] ?? [];
                                $initiales = strtoupper(substr($particulier->utilisateur->prenom, 0, 1) . substr($particulier->utilisateur->nom, 0, 1));
                            @endphp

                            <a href="{{ route('entreprise.offres.matching', $offre->id) }}" class="offre-card"
                                style="display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:start;">
                                <div class="offre-card-header" style="margin-bottom:0;">
                                    <div class="company-logo">{{ $initiales }}</div>
                                    <div>
                                        <h3>{{ $particulier->utilisateur->prenom }} {{ $particulier->utilisateur->nom }}</h3>
                                        <div class="company-name">{{ $offre->titre }}</div>
                                    </div>
                                </div>

                                <div style="text-align:right;min-width:56px;">
                                    <div style="font-family:var(--font-head);font-size:1.25rem;font-weight:800;color:{{ $couleur }};">
                                        {{ $score }}%
                                    </div>
                                    <div style="height:5px;background:var(--border);border-radius:3px;overflow:hidden;">
                                        <div style="width:{{ $score }}%;height:5px;border-radius:3px;background:{{ $couleur }};"></div>
                                    </div>
                                </div>

                                <div style="grid-column:1/-1;">
                                    @if(count($matched) > 0)
                                        <div style="display:flex;gap:0.35rem;flex-wrap:wrap;margin-bottom:0.75rem;">
                                            @foreach(array_slice($matched, 0, 3) as $comp)
                                                <span style="background:#d4edda;color:#155724;padding:0.18rem 0.55rem;border-radius:5px;font-size:0.72rem;font-weight:600;">
                                                    <i class="fas fa-check"></i> {{ $comp }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="offre-meta">
                                        @if($particulier->adresse)
                                            <span class="meta-chip"><i class="fas fa-map-marker-alt"></i> {{ $particulier->adresse }}</span>
                                        @endif
                                        @if($particulier->niveau_etude)
                                            <span class="badge badge-blue">{{ $particulier->niveau_etude }}</span>
                                        @endif
                                        @if($particulier->cv->count() > 0)
                                            <span class="meta-chip"><i class="fas fa-file-pdf"></i> CV disponible</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="table-card" style="grid-column:1/-1;padding:2rem;text-align:center;color:var(--muted);">
                                <i class="fas fa-users" style="font-size:2rem;margin-bottom:0.75rem;display:block;"></i>
                                <strong style="display:block;color:var(--ink);margin-bottom:0.35rem;">Aucun matching disponible</strong>
                                <p>Ajoutez des competences aux offres et aux profils candidats pour calculer les meilleurs matchs.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div style="margin-bottom:2rem;">
                    <div class="section-header">
                        <h2><i class="fas fa-magic" style="color:var(--accent);margin-right:0.5rem;"></i> Suggestions</h2>
                        <a href="{{ route('entreprise.offres') }}" class="btn btn-outline btn-sm">Voir mes offres</a>
                    </div>

                    <div class="offres-grid">
                        @forelse($candidatsSuggestions ?? [] as $item)
                            @php
                                $particulier = $item['particulier'];
                                $offre = $item['offre'];
                                $matched = $item['matched'];
                                $initiales = strtoupper(substr($particulier->utilisateur->prenom, 0, 1) . substr($particulier->utilisateur->nom, 0, 1));
                            @endphp

                            <a href="{{ route('entreprise.offres.suggestions', $offre->id) }}" class="offre-card"
                                style="display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:start;border-color:rgba(232,76,30,0.25);">
                                <div class="offre-card-header" style="margin-bottom:0;">
                                    <div class="company-logo">{{ $initiales }}</div>
                                    <div>
                                        <h3>{{ $particulier->utilisateur->prenom }} {{ $particulier->utilisateur->nom }}</h3>
                                        <div class="company-name">{{ $offre->titre }}</div>
                                    </div>
                                </div>

                                <div style="text-align:right;min-width:72px;">
                                    <div style="font-family:var(--font-head);font-size:1.25rem;font-weight:800;color:var(--accent);">
                                        {{ $item['score'] }}
                                    </div>
                                    <div style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;font-weight:700;">
                                        match{{ $item['score'] > 1 ? 's' : '' }}
                                    </div>
                                </div>

                                <div style="grid-column:1/-1;">
                                    @if(count($matched) > 0)
                                        <div style="display:flex;gap:0.35rem;flex-wrap:wrap;margin-bottom:0.75rem;">
                                            @foreach(array_slice($matched, 0, 3) as $comp)
                                                <span style="background:#d4edda;color:#155724;padding:0.18rem 0.55rem;border-radius:5px;font-size:0.72rem;font-weight:600;">
                                                    <i class="fas fa-check"></i> {{ $comp }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="offre-meta">
                                        @if($particulier->adresse)
                                            <span class="meta-chip"><i class="fas fa-map-marker-alt"></i> {{ $particulier->adresse }}</span>
                                        @endif
                                        @if($particulier->niveau_etude)
                                            <span class="badge badge-blue">{{ $particulier->niveau_etude }}</span>
                                        @endif
                                        @if($particulier->cv->count() > 0)
                                            <span class="meta-chip"><i class="fas fa-file-pdf"></i> CV disponible</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="table-card" style="grid-column:1/-1;padding:2rem;text-align:center;color:var(--muted);">
                                <i class="fas fa-magic" style="font-size:2rem;margin-bottom:0.75rem;display:block;"></i>
                                <strong style="display:block;color:var(--ink);margin-bottom:0.35rem;">Aucune suggestion disponible</strong>
                                <p>Ajoutez des competences aux offres pour recevoir des candidats suggérés.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-header">
                        <h3><i class="fas fa-users" style="color:var(--accent);margin-right:0.5rem;"></i> Candidatures
                            récentes</h3>
                        <a href="{{ route('entreprise.candidatures') }}" class="btn btn-outline btn-sm">Voir tout</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Candidat</th>
                                <th>Poste</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dernieresCandidatures ?? [] as $cand)
                                @php
                                    $bc = match ($cand->statut) {
                                        'acceptee' => 'badge-green',
                                        'refusee' => 'badge-red',
                                        'en_cours' => 'badge-blue',
                                        default => 'badge-yellow',
                                    };
                                    $bl = match ($cand->statut) {
                                        'acceptee' => 'Acceptée',
                                        'refusee' => 'Refusée',
                                        'en_cours' => 'En cours',
                                        default => 'En attente',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight:600;">{{ $cand->particulier->utilisateur->prenom }}
                                            {{ $cand->particulier->utilisateur->nom }}</div>
                                        <div style="font-size:0.78rem;color:var(--muted);">
                                            {{ $cand->particulier->utilisateur->email }}</div>
                                    </td>
                                    <td>{{ $cand->offre->titre }}</td>
                                    <td>{{ $cand->date->format('d/m/Y') }}</td>
                                    <td><span class="badge {{ $bc }}">{{ $bl }}</span></td>
                                    <td><a href="{{ route('entreprise.candidature.show', $cand->id) }}"
                                            class="btn btn-outline btn-sm">Voir</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;padding:2rem;color:var(--muted);">Aucune
                                        candidature.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ADMIN --}}
        @elseif(auth()->user()->isAdmin())
            <section class="hero">
                <div class="hero-inner">
                    <div class="hero-tag">🛡 Panneau d'administration</div>
                    <h1>Tableau de bord <em>Admin</em></h1>
                    <p>Gérez les utilisateurs, les entreprises, les offres et consultez les statistiques globales.</p>
                    <div class="hero-actions">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary"
                            style="font-size:1rem;padding:0.8rem 1.8rem;"><i class="fas fa-chart-pie"></i> Statistiques
                            détaillées</a>
                        <a href="{{ route('admin.entreprises') }}" class="btn btn-outline"
                            style="color:var(--paper);border-color:rgba(255,255,255,0.2);font-size:1rem;padding:0.8rem 1.8rem;"><i
                                class="fas fa-building"></i> Gérer les entreprises</a>
                    </div>
                    <div class="hero-stat">
                        <div><strong>{{ $stats['total_utilisateurs'] ?? 0 }}</strong><span>Utilisateurs</span></div>
                        <div><strong>{{ $stats['total_entreprises'] ?? 0 }}</strong><span>Entreprises</span></div>
                        <div><strong>{{ $stats['offres_actives'] ?? 0 }}</strong><span>Offres actives</span></div>
                        <div><strong>{{ $stats['total_candidatures'] ?? 0 }}</strong><span>Candidatures</span></div>
                    </div>
                </div>
            </section>

            <div class="container" style="padding-top:2.5rem;">
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
                    <a href="{{ route('admin.utilisateurs') }}" style="text-decoration:none;">
                        <div class="stat-card" style="cursor:pointer;">
                            <div class="stat-icon" style="background:#d1ecf1;color:#0c5460;"><i class="fas fa-users"></i>
                            </div>
                            <div><strong>{{ $stats['total_utilisateurs'] ?? 0 }}</strong><span>Utilisateurs</span></div>
                        </div>
                    </a>
                    <a href="{{ route('admin.entreprises') }}" style="text-decoration:none;">
                        <div class="stat-card" style="cursor:pointer;">
                            <div class="stat-icon" style="background:#e2d9f3;color:#6f42c1;"><i class="fas fa-building"></i>
                            </div>
                            <div><strong>{{ $stats['total_entreprises'] ?? 0 }}</strong><span>Entreprises</span></div>
                        </div>
                    </a>
                    <a href="{{ route('admin.offres') }}" style="text-decoration:none;">
                        <div class="stat-card" style="cursor:pointer;">
                            <div class="stat-icon" style="background:#d4edda;color:#155724;"><i class="fas fa-briefcase"></i>
                            </div>
                            <div><strong>{{ $stats['offres_actives'] ?? 0 }}</strong><span>Offres actives</span></div>
                        </div>
                    </a>
                </div>
            </div>

        @endif
    @endauth

    {{-- VISITEUR --}}
    @guest
        <section class="hero">
            <div class="hero-inner">
                <div class="hero-tag">🚀 Plateforme d'emploi #1 au Maroc</div>
                <h1>Trouvez le job<br>qui vous <em>ressemble</em></h1>
                <p>Des milliers d'offres d'emploi, de stage et d'alternance. Connectez-vous avec les meilleures entreprises du
                    pays.</p>
                <div class="hero-actions">
                    <a href="{{ route('offres.index') }}" class="btn btn-primary"
                        style="font-size:1rem;padding:0.8rem 1.8rem;"><i class="fas fa-search"></i> Parcourir les offres</a>
                    <a href="{{ route('register') }}" class="btn btn-outline"
                        style="color:var(--paper);border-color:rgba(255,255,255,0.2);font-size:1rem;padding:0.8rem 1.8rem;">Créer
                        un compte</a>
                </div>
                <div class="hero-stat">
                    <div><strong>{{ $stats['offres'] ?? '0' }}+</strong><span>Offres actives</span></div>
                    <div><strong>{{ $stats['entreprises'] ?? '0' }}+</strong><span>Entreprises</span></div>
                    <div><strong>{{ $stats['particuliers'] ?? '0' }}+</strong><span>Candidats</span></div>
                </div>
            </div>
        </section>

        <div class="search-section">
            <form action="{{ route('offres.index') }}" method="GET">
                <div class="search-bar">
                    <i class="fas fa-search"
                        style="padding:0.5rem 0 0.5rem 0.75rem;color:var(--muted);align-self:center;"></i>
                    <input type="text" name="search" placeholder="Titre du poste, mot-clé..."
                        value="{{ request('search') }}">
                    <select name="localisation">
                        <option value="">Toutes les villes</option>
                        <option value="Casablanca">Casablanca</option>
                        <option value="Rabat">Rabat</option>
                        <option value="Marrakech">Marrakech</option>
                        <option value="Remote">Remote</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </form>
        </div>

        <div class="container">
            <section class="section">
                <div class="section-header">
                    <h2>Offres récentes</h2>
                    <a href="{{ route('offres.index') }}" class="btn btn-outline btn-sm">Voir tout <i
                            class="fas fa-arrow-right"></i></a>
                </div>
                <div class="offres-grid">
                    @forelse($offres ?? [] as $offre)
                        <a href="{{ route('offres.show', $offre->id) }}" class="offre-card">
                            <div class="offre-card-header">
                                <div class="company-logo">
                                    @if ($offre->entreprise->logo)
                                        <img src="{{ asset('storage/' . $offre->entreprise->logo) }}" alt="">
                                    @else
                                        {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                                    @endif
                                </div>
                                <div>
                                    <h3>{{ $offre->titre }}</h3>
                                    <div class="company-name">{{ $offre->entreprise->nom }}</div>
                                </div>
                            </div>
                            <p style="font-size:0.88rem;color:var(--muted);line-height:1.5;">
                                {{ Str::limit($offre->description, 100) }}</p>
                            <div class="offre-meta">
                                @if ($offre->localisation)
                                    <span class="meta-chip"><i class="fas fa-map-marker-alt"></i>
                                        {{ $offre->localisation }}</span>
                                @endif
                                @if ($offre->contrat)
                                    <span class="meta-chip"><i class="fas fa-briefcase"></i> {{ $offre->contrat }}</span>
                                @endif
                                @if ($offre->salaire)
                                    <span class="meta-chip"><i class="fas fa-money-bill-wave"></i>
                                        {{ $offre->salaire }}</span>
                                @endif
                            </div>
                            <div class="offre-card-footer">
                                <span class="offre-date">{{ $offre->date_publication->diffForHumans() }}</span>
                                <span class="badge badge-green">{{ $offre->categorie->nom ?? 'Autre' }}</span>
                            </div>
                        </a>
                    @empty
                        <p style="color:var(--muted);grid-column:1/-1;">Aucune offre disponible.</p>
                    @endforelse
                </div>
            </section>

            <section class="section" style="padding-top:0;">
                <div class="section-header">
                    <h2>Parcourir par catégorie</h2>
                </div>
                <div class="categories-grid">
                    @php $icons = ['💻','🏗️','📊','🎨','⚕️','📚','🔬','🏦','✈️','🛒']; @endphp
                    @forelse($categories ?? [] as $i => $cat)
                        <a href="{{ route('offres.index', ['categorie_id' => $cat->id]) }}" class="categorie-card">
                            <span class="icon">{{ $icons[$i % count($icons)] }}</span>
                            <h4>{{ $cat->nom }}</h4>
                            <span>{{ $cat->offres_count ?? 0 }} offres</span>
                        </a>
                    @empty
                        <p style="color:var(--muted)">Aucune catégorie.</p>
                    @endforelse
                </div>
            </section>

            <div class="cta-banner">
                <h2>Vous recrutez ?</h2>
                <p>Publiez vos offres et trouvez les meilleurs talents du Maroc en quelques clics.</p>
                <a href="{{ route('register') }}" class="btn btn-white"
                    style="font-size:1rem;padding:0.8rem 2rem;">Commencer gratuitement <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    @endguest

    </div>

@endsection
