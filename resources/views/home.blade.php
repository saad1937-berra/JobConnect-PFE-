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
        background: radial-gradient(ellipse at 70% 50%, rgba(232,76,30,0.15) 0%, transparent 60%);
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
        background: rgba(232,76,30,0.15);
        color: var(--accent);
        border: 1px solid rgba(232,76,30,0.3);
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
        position: relative;
    }

    .hero p {
        font-size: 1.15rem;
        color: #aaa;
        max-width: 520px;
        line-height: 1.7;
        margin-bottom: 2.5rem;
    }

    .hero-actions { display: flex; gap: 1rem; flex-wrap: wrap; }

    .hero-stat {
        display: flex;
        gap: 3rem;
        margin-top: 4rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    .hero-stat div { text-align: left; }
    .hero-stat strong {
        display: block;
        font-family: var(--font-head);
        font-size: 2rem;
        font-weight: 800;
        color: var(--paper);
    }
    .hero-stat span { font-size: 0.85rem; color: #777; }

    /* Search bar */
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

    /* Offres section */
    .section { padding: 4rem 0; }
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

    /* Offre card */
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
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        border-color: var(--accent);
    }

    .offre-card-header { display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1rem; }

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

    .company-logo img { width: 100%; height: 100%; object-fit: cover; }

    .offre-card h3 {
        font-family: var(--font-head);
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .offre-card .company-name { font-size: 0.85rem; color: var(--muted); }

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

    .offre-date { font-size: 0.78rem; color: var(--muted); }

    /* Categories */
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

    .categorie-card span { font-size: 0.8rem; color: var(--muted); }
    .categorie-card:hover span { color: #aaa; }

    /* CTA banner */
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

    .cta-banner p { font-size: 1rem; opacity: 0.85; margin-bottom: 2rem; }

    .btn-white { background: white; color: var(--accent) !important; }
    .btn-white:hover { opacity: 0.9; }
</style>
@endpush

@section('content')
<!-- Hero -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-tag">🚀 Plateforme d'emploi #1 au Maroc</div>
        <h1>Trouvez le job<br>qui vous <em>ressemble</em></h1>
        <p>Des milliers d'offres d'emploi, de stage et d'alternance. Connectez-vous avec les meilleures entreprises du pays.</p>
        <div class="hero-actions">
            <a href="{{ route('offres.index') }}" class="btn btn-primary" style="font-size:1rem;padding:0.8rem 1.8rem;">
                <i class="fas fa-search"></i> Parcourir les offres
            </a>
            @guest
                <a href="{{ route('register') }}" class="btn btn-outline" style="color:var(--paper);border-color:rgba(255,255,255,0.2);font-size:1rem;padding:0.8rem 1.8rem;">
                    Créer un compte
                </a>
            @endguest
        </div>

        <div class="hero-stat">
            <div><strong>{{ $stats['offres'] ?? '1 200' }}+</strong><span>Offres actives</span></div>
            <div><strong>{{ $stats['entreprises'] ?? '340' }}+</strong><span>Entreprises</span></div>
            <div><strong>{{ $stats['particuliers'] ?? '8 500' }}+</strong><span>Candidats</span></div>
        </div>
    </div>
</section>

<!-- Search -->
<div class="search-section">
    <form action="{{ route('offres.index') }}" method="GET">
        <div class="search-bar">
            <i class="fas fa-search" style="padding:0.5rem 0 0.5rem 0.75rem;color:var(--muted);align-self:center;"></i>
            <input type="text" name="search" placeholder="Titre du poste, mot-clé..." value="{{ request('search') }}">
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
    <!-- Offres récentes -->
    <section class="section">
        <div class="section-header">
            <h2>Offres récentes</h2>
            <a href="{{ route('offres.index') }}" class="btn btn-outline btn-sm">Voir tout <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="offres-grid">
            @forelse($offres ?? [] as $offre)
                <a href="{{ route('offres.show', $offre->id) }}" class="offre-card">
                    <div class="offre-card-header">
                        <div class="company-logo">
                            @if($offre->entreprise->logo)
                                <img src="{{ asset('storage/'.$offre->entreprise->logo) }}" alt="">
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
                        {{ Str::limit($offre->description, 100) }}
                    </p>
                    <div class="offre-meta">
                        @if($offre->localisation)
                            <span class="meta-chip"><i class="fas fa-map-marker-alt"></i> {{ $offre->localisation }}</span>
                        @endif
                        @if($offre->contrat)
                            <span class="meta-chip"><i class="fas fa-briefcase"></i> {{ $offre->contrat }}</span>
                        @endif
                        @if($offre->salaire)
                            <span class="meta-chip"><i class="fas fa-money-bill-wave"></i> {{ $offre->salaire }}</span>
                        @endif
                    </div>
                    <div class="offre-card-footer">
                        <span class="offre-date">{{ $offre->date_publication->diffForHumans() }}</span>
                        <span class="badge badge-green">{{ $offre->categorie->nom ?? 'Autre' }}</span>
                    </div>
                </a>
            @empty
                <p style="color:var(--muted);grid-column:1/-1;">Aucune offre disponible pour le moment.</p>
            @endforelse
        </div>
    </section>

    <!-- Catégories -->
    <section class="section" style="padding-top:0;">
        <div class="section-header">
            <h2>Parcourir par catégorie</h2>
        </div>
        <div class="categories-grid">
            @php
                $icons = ['💻','🏗️','📊','🎨','⚕️','📚','🔬','🏦','✈️','🛒'];
            @endphp
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

    <!-- CTA -->
    <div class="cta-banner">
        <h2>Vous recrutez ?</h2>
        <p>Publiez vos offres et trouvez les meilleurs talents du Maroc en quelques clics.</p>
        <a href="{{ route('register') }}" class="btn btn-white" style="font-size:1rem;padding:0.8rem 2rem;">
            Commencer gratuitement <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endsection