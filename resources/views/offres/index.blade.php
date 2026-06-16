@extends(auth()->check() && auth()->user()->isParticulier() ? 'layouts.particulier' : 'layouts.app')
@section('title', "Offres d'emploi")

@section(auth()->check() && auth()->user()->isParticulier() ? 'part-content' : 'content')
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
                        <option value="CDI"       {{ request('contrat') == 'CDI'       ? 'selected' : '' }}>CDI</option>
                        <option value="CDD"       {{ request('contrat') == 'CDD'       ? 'selected' : '' }}>CDD</option>
                        <option value="Stage"     {{ request('contrat') == 'Stage'     ? 'selected' : '' }}>Stage</option>
                        <option value="Freelance" {{ request('contrat') == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                        <option value="Alternance" {{ request('contrat') == 'Alternance' ? 'selected' : '' }}>Alternance</option>
                    </select>
                </div>

                <div class="filtre-group">
                    <label>Ville</label>
                    <input type="text" name="localisation" value="{{ request('localisation') }}" placeholder="Casablanca...">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                    <i class="fas fa-search"></i> Appliquer
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
                            <div class="company">{{ $offre->entreprise->nom }} • {{ $offre->localisation ?? '—' }}</div>
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