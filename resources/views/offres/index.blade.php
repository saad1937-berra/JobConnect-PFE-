@extends('layouts.app')
@section('title', 'Offres d\'emploi')

@push('styles')
<style>
    .offres-page { padding: 2.5rem 0; }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-family: var(--font-head);
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .page-header p { color: var(--muted); margin-top: 0.25rem; }

    .offres-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
        align-items: start;
    }

    /* Sidebar filtres */
    .filtres-sidebar {
        background: white;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        position: sticky;
        top: 84px;
    }

    .filtres-sidebar h3 {
        font-family: var(--font-head);
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .filtre-group { margin-bottom: 1.5rem; }
    .filtre-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--muted);
        margin-bottom: 0.6rem;
    }

    .filtre-group select,
    .filtre-group input {
        width: 100%;
        padding: 0.55rem 0.8rem;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        font-family: var(--font-body);
        font-size: 0.88rem;
        background: var(--paper);
        color: var(--ink);
        outline: none;
    }

    .filtre-group select:focus,
    .filtre-group input:focus { border-color: var(--accent2); }

    /* Offres list */
    .offres-list { display: flex; flex-direction: column; gap: 1rem; }

    .offre-item {
        background: white;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        display: flex;
        gap: 1.25rem;
        align-items: flex-start;
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
    }

    .offre-item:hover {
        border-color: var(--accent);
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        transform: translateX(3px);
    }

    .company-logo {
        width: 52px; height: 52px;
        border-radius: 10px;
        background: var(--paper);
        border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-head); font-weight: 800; font-size: 1.1rem; color: var(--accent);
        flex-shrink: 0; overflow: hidden;
    }

    .company-logo img { width: 100%; height: 100%; object-fit: cover; }

    .offre-info { flex: 1; }
    .offre-info h3 {
        font-family: var(--font-head);
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }

    .offre-info .company { font-size: 0.88rem; color: var(--muted); margin-bottom: 0.75rem; }

    .offre-chips { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .chip {
        display: inline-flex; align-items: center; gap: 0.3rem;
        font-size: 0.78rem; color: var(--muted);
        padding: 0.2rem 0.6rem; background: var(--paper); border-radius: 5px;
    }

    .offre-right { text-align: right; flex-shrink: 0; }
    .offre-right .salaire {
        font-family: var(--font-head);
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--ink);
        margin-bottom: 0.5rem;
    }

    .offre-right .date { font-size: 0.78rem; color: var(--muted); margin-bottom: 0.75rem; }

    .pagination-wrapper { margin-top: 2rem; display: flex; justify-content: center; }

    @media (max-width: 900px) {
        .offres-layout { grid-template-columns: 1fr; }
        .filtres-sidebar { position: static; }
    }
</style>
@endpush

@section('content')
<div class="container offres-page">
    <div class="page-header">
        <h1>Offres d'emploi</h1>
        <p>{{ $offres->total() }} offres disponibles</p>
    </div>

    <div class="offres-layout">
        <!-- Sidebar filtres -->
        <aside class="filtres-sidebar">
            <h3>
                Filtres
                @if(request()->anyFilled(['search','localisation','contrat','categorie_id']))
                    <a href="{{ route('offres.index') }}" style="font-size:0.75rem;font-weight:500;color:var(--accent);text-decoration:none;">
                        Réinitialiser
                    </a>
                @endif
            </h3>

            <form method="GET" action="{{ route('offres.index') }}">
                <div class="filtre-group">
                    <label>Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre, mot-clé...">
                </div>

                <div class="filtre-group">
                    <label>Catégorie</label>
                    <select name="categorie_id">
                        <option value="">Toutes</option>
                        @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ request('categorie_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filtre-group">
                    <label>Type de contrat</label>
                    <select name="contrat">
                        <option value="">Tous</option>
                        <option value="CDI"   {{ request('contrat') == 'CDI'   ? 'selected' : '' }}>CDI</option>
                        <option value="CDD"   {{ request('contrat') == 'CDD'   ? 'selected' : '' }}>CDD</option>
                        <option value="Stage" {{ request('contrat') == 'Stage' ? 'selected' : '' }}>Stage</option>
                        <option value="Freelance" {{ request('contrat') == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                    </select>
                </div>

                <div class="filtre-group">
                    <label>Ville</label>
                    <input type="text" name="localisation" value="{{ request('localisation') }}" placeholder="Casablanca...">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                    Appliquer
                </button>
            </form>
        </aside>

        <!-- Liste des offres -->
        <div>
            <div class="offres-list">
                @forelse($offres as $offre)
                    <a href="{{ route('offres.show', $offre->id) }}" class="offre-item">
                        <div class="company-logo">
                            @if($offre->entreprise->logo)
                                <img src="{{ asset('storage/'.$offre->entreprise->logo) }}" alt="">
                            @else
                                {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                            @endif
                        </div>
                        <div class="offre-info">
                            <h3>{{ $offre->titre }}</h3>
                            <div class="company">{{ $offre->entreprise->nom }} • {{ $offre->localisation }}</div>
                            <div class="offre-chips">
                                @if($offre->contrat)
                                    <span class="badge badge-blue">{{ $offre->contrat }}</span>
                                @endif
                                @if($offre->niveau_etude)
                                    <span class="chip"><i class="fas fa-graduation-cap"></i> {{ $offre->niveau_etude }}</span>
                                @endif
                                @if($offre->categorie)
                                    <span class="chip"><i class="fas fa-tag"></i> {{ $offre->categorie->nom }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="offre-right">
                            @if($offre->salaire)
                                <div class="salaire">{{ $offre->salaire }}</div>
                            @endif
                            <div class="date">{{ $offre->date_publication->diffForHumans() }}</div>
                            @auth
                                @if(auth()->user()->isParticulier())
                                    <span class="btn btn-primary btn-sm">Postuler</span>
                                @endif
                            @else
                                <span class="btn btn-outline btn-sm">Voir</span>
                            @endauth
                        </div>
                    </a>
                @empty
                    <div style="text-align:center;padding:4rem 2rem;color:var(--muted);">
                        <i class="fas fa-search" style="font-size:2.5rem;margin-bottom:1rem;display:block;"></i>
                        <p>Aucune offre ne correspond à vos critères.</p>
                        <a href="{{ route('offres.index') }}" class="btn btn-outline" style="margin-top:1rem;">Réinitialiser</a>
                    </div>
                @endforelse
            </div>

            <div class="pagination-wrapper">
                {{ $offres->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection