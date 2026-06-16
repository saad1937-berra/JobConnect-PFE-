@extends(auth()->check() && auth()->user()->isParticulier() ? 'layouts.particulier' : 'layouts.app')
@section('title', $offre->titre)

@section(auth()->check() && auth()->user()->isParticulier() ? 'part-content' : 'content')
    <div class="container offre-detail-page">

        <div class="breadcrumb">
            <a href="{{ route('home') }}">Accueil</a> /
            <a href="{{ route('offres.index') }}">Offres</a> /
            {{ $offre->titre }}
        </div>

        <div class="offre-detail-layout">

            <!-- Contenu principal -->
            <div>
                <div class="offre-main">
                    <div class="offre-header">
                        <div class="company-logo">
                            @if ($offre->entreprise->logo)
                                <img src="{{ asset('storage/' . $offre->entreprise->logo) }}" alt="">
                            @else
                                {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                            @endif
                        </div>
                        <div class="offre-title-block">
                            <h1>{{ $offre->titre }}</h1>
                            <a href="#" class="company-link">{{ $offre->entreprise->nom }}</a>
                            <div class="offre-chips">
                                @if ($offre->contrat)
                                    <span class="badge badge-blue">{{ $offre->contrat }}</span>
                                @endif
                                @if ($offre->statut === 'active')
                                    <span class="badge badge-green">Active</span>
                                @endif
                                @if ($offre->categorie)
                                    <span class="chip"><i class="fas fa-tag"></i> {{ $offre->categorie->nom }}</span>
                                @endif
                                @if ($offre->localisation)
                                    <span class="chip"><i class="fas fa-map-marker-alt"></i>
                                        {{ $offre->localisation }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="offre-section">
                        <h2>Description du poste</h2>
                        <p>{{ $offre->description }}</p>
                    </div>

                    @if ($offre->niveau_etude || $offre->duree)
                        <div class="offre-section">
                            <h2>Profil recherché</h2>
                            <ul>
                                @if ($offre->niveau_etude)
                                    <li>Niveau d'études : {{ $offre->niveau_etude }}</li>
                                @endif
                                @if ($offre->duree)
                                    <li>Durée : {{ $offre->duree }}</li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    {{-- Compétences requises --}}
                    @if ($offre->competances->count() > 0)
                        <div class="offre-section">
                            <h2>Compétences requises</h2>
                            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                                @foreach ($offre->competances as $comp)
                                    <span class="badge badge-blue">{{ $comp->nom }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="offre-section">
                        <h2>À propos de l'entreprise</h2>
                        <p>{{ $offre->entreprise->description ?? 'Aucune description disponible.' }}</p>
                        @if ($offre->entreprise->site_web)
                            <a href="{{ $offre->entreprise->site_web }}" target="_blank" class="btn btn-outline btn-sm"
                                style="margin-top:0.75rem;">
                                <i class="fas fa-external-link-alt"></i> Site web
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="offre-sidebar">

                <!-- Bouton postuler -->
                <div class="sidebar-card">
                    @auth
                        @if (auth()->user()->isParticulier())
                            @if ($dejaCandidaté ?? false)
                                <div class="already-applied">
                                    <i class="fas fa-check-circle"></i> Candidature envoyée
                                </div>
                            @else
                                <form method="POST" action="{{ route('particulier.postuler') }}">
                                    @csrf
                                    <input type="hidden" name="offre_id" value="{{ $offre->id }}">
                                    <button type="submit" class="btn btn-primary apply-btn">
                                        <i class="fas fa-paper-plane"></i> Postuler maintenant
                                    </button>
                                </form>
                            @endif

                            {{-- Lien matching --}}
                            <a href="{{ route('particulier.matching.score', $offre->id) }}" class="part-btn part-btn-primary"
                                style="width:100%;justify-content:center;">
                                <i class="fas fa-percentage"></i> Voir mon score
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary apply-btn">
                            <i class="fas fa-sign-in-alt"></i> Connectez-vous pour postuler
                        </a>
                    @endauth

                    <a href="{{ route('offres.index') }}" class="part-btn part-btn-outline"
                        style="width:100%;justify-content:center;margin-top:0.5rem;">
                        <i class="fas fa-arrow-left"></i> Retour aux offres
                    </a>
                </div>

                <!-- Infos rapides -->
                <div class="sidebar-card">
                    <h3>Détails du poste</h3>
                    <div class="info-row">
                        <span class="label"><i class="fas fa-briefcase"></i> Contrat</span>
                        <span class="value">{{ $offre->contrat ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label"><i class="fas fa-map-marker-alt"></i> Lieu</span>
                        <span class="value">{{ $offre->localisation ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label"><i class="fas fa-money-bill-wave"></i> Salaire</span>
                        <span class="value">{{ $offre->salaire ?? 'Non précisé' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label"><i class="fas fa-graduation-cap"></i> Niveau</span>
                        <span class="value">{{ $offre->niveau_etude ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label"><i class="fas fa-clock"></i> Durée</span>
                        <span class="value">{{ $offre->duree ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label"><i class="fas fa-calendar"></i> Publiée le</span>
                        <span class="value">{{ $offre->date_publication->format('d/m/Y') }}</span>
                    </div>
                    @if ($offre->date_expiration)
                        <div class="info-row">
                            <span class="label"><i class="fas fa-calendar-times"></i> Expire le</span>
                            <span class="value">{{ $offre->date_expiration->format('d/m/Y') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Entreprise -->
                <div class="sidebar-card company-card">
                    <div class="logo-big">
                        @if ($offre->entreprise->logo)
                            <img src="{{ asset('storage/' . $offre->entreprise->logo) }}" alt="">
                        @else
                            {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                        @endif
                    </div>
                    <h4>{{ $offre->entreprise->nom }}</h4>
                    @if ($offre->entreprise->secteur)
                        <div class="secteur">{{ $offre->entreprise->secteur }}</div>
                    @endif
                    @if ($offre->entreprise->adresse)
                        <p><i class="fas fa-map-marker-alt" style="color:var(--accent);"></i>
                            {{ $offre->entreprise->adresse }}</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
