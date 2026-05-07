@extends('layouts.app')
@section('title', $offre->titre)

@push('styles')
<style>
    .offre-detail-page { padding: 2.5rem 0; }

    .offre-detail-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 2rem;
        align-items: start;
    }

    .offre-main { background: white; border: 1px solid var(--border); border-radius: var(--radius); padding: 2rem; }

    .offre-header { display: flex; align-items: flex-start; gap: 1.25rem; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border); }

    .company-logo {
        width: 72px; height: 72px; border-radius: 14px;
        background: var(--paper); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-head); font-weight: 800; font-size: 1.4rem; color: var(--accent);
        flex-shrink: 0; overflow: hidden;
    }
    .company-logo img { width: 100%; height: 100%; object-fit: cover; }

    .offre-title-block h1 {
        font-family: var(--font-head);
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 0.35rem;
    }

    .offre-title-block .company-link {
        color: var(--accent2); text-decoration: none; font-weight: 500; font-size: 1rem;
    }

    .offre-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.75rem; }
    .chip { display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: var(--muted); padding: 0.25rem 0.65rem; background: var(--paper); border-radius: 6px; }

    .offre-section { margin-bottom: 2rem; }
    .offre-section h2 {
        font-family: var(--font-head); font-size: 1.1rem; font-weight: 700;
        margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--paper);
    }
    .offre-section p, .offre-section li { font-size: 0.95rem; line-height: 1.75; color: #333; }
    .offre-section ul { padding-left: 1.5rem; }
    .offre-section ul li { margin-bottom: 0.35rem; }

    /* Sidebar sticky */
    .offre-sidebar { position: sticky; top: 84px; }

    .sidebar-card {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.5rem; margin-bottom: 1rem;
    }

    .sidebar-card h3 {
        font-family: var(--font-head); font-size: 1rem; font-weight: 700;
        margin-bottom: 1.25rem;
    }

    .info-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.6rem 0; border-bottom: 1px solid var(--border);
        font-size: 0.88rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .label { color: var(--muted); display: flex; align-items: center; gap: 0.4rem; }
    .info-row .value { font-weight: 600; text-align: right; }

    .apply-btn {
        width: 100%; justify-content: center;
        padding: 0.85rem; font-size: 1rem;
        margin-bottom: 0.75rem;
    }

    .already-applied {
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.85rem; background: #d4edda; color: #155724;
        border-radius: 8px; font-size: 0.9rem; font-weight: 600;
    }

    .company-card { text-align: center; }
    .company-card .logo-big {
        width: 80px; height: 80px; border-radius: 14px;
        background: var(--paper); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-head); font-weight: 800; font-size: 1.6rem; color: var(--accent);
        margin: 0 auto 1rem; overflow: hidden;
    }
    .company-card .logo-big img { width: 100%; height: 100%; object-fit: cover; }
    .company-card h4 { font-family: var(--font-head); font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem; }
    .company-card .secteur { font-size: 0.83rem; color: var(--muted); margin-bottom: 0.75rem; }
    .company-card p { font-size: 0.85rem; color: #555; line-height: 1.6; }

    .breadcrumb { font-size: 0.85rem; color: var(--muted); margin-bottom: 1.25rem; }
    .breadcrumb a { color: var(--accent2); text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }

    @media (max-width: 900px) {
        .offre-detail-layout { grid-template-columns: 1fr; }
        .offre-sidebar { position: static; }
    }
</style>
@endpush

@section('content')
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
                        @if($offre->entreprise->logo)
                            <img src="{{ asset('storage/'.$offre->entreprise->logo) }}" alt="">
                        @else
                            {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                        @endif
                    </div>
                    <div class="offre-title-block">
                        <h1>{{ $offre->titre }}</h1>
                        <a href="#" class="company-link">{{ $offre->entreprise->nom }}</a>
                        <div class="offre-chips">
                            @if($offre->contrat)  <span class="badge badge-blue">{{ $offre->contrat }}</span> @endif
                            @if($offre->statut === 'active') <span class="badge badge-green">Active</span> @endif
                            @if($offre->categorie) <span class="chip"><i class="fas fa-tag"></i> {{ $offre->categorie->nom }}</span> @endif
                            @if($offre->localisation) <span class="chip"><i class="fas fa-map-marker-alt"></i> {{ $offre->localisation }}</span> @endif
                        </div>
                    </div>
                </div>

                <div class="offre-section">
                    <h2>Description du poste</h2>
                    <p>{{ $offre->description }}</p>
                </div>

                @if($offre->niveau_etude)
                <div class="offre-section">
                    <h2>Profil recherché</h2>
                    <ul>
                        <li>Niveau d'études : {{ $offre->niveau_etude }}</li>
                        @if($offre->duree) <li>Durée : {{ $offre->duree }}</li> @endif
                    </ul>
                </div>
                @endif

                <div class="offre-section">
                    <h2>À propos de l'entreprise</h2>
                    <p>{{ $offre->entreprise->description ?? 'Aucune description disponible.' }}</p>
                    @if($offre->entreprise->site_web)
                        <a href="{{ $offre->entreprise->site_web }}" target="_blank" class="btn btn-outline btn-sm" style="margin-top:0.75rem;">
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
                    @if(auth()->user()->isParticulier())
                        @if($dejaCandidaté ?? false)
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
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary apply-btn">
                        <i class="fas fa-sign-in-alt"></i> Connectez-vous pour postuler
                    </a>
                @endauth

                <a href="{{ route('offres.index') }}" class="btn btn-outline" style="width:100%;justify-content:center;">
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
                @if($offre->date_expiration)
                <div class="info-row">
                    <span class="label"><i class="fas fa-calendar-times"></i> Expire le</span>
                    <span class="value">{{ $offre->date_expiration->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>

            <!-- Entreprise -->
            <div class="sidebar-card company-card">
                <div class="logo-big">
                    @if($offre->entreprise->logo)
                        <img src="{{ asset('storage/'.$offre->entreprise->logo) }}" alt="">
                    @else
                        {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                    @endif
                </div>
                <h4>{{ $offre->entreprise->nom }}</h4>
                @if($offre->entreprise->secteur)
                    <div class="secteur">{{ $offre->entreprise->secteur }}</div>
                @endif
                @if($offre->entreprise->adresse)
                    <p><i class="fas fa-map-marker-alt" style="color:var(--accent);"></i> {{ $offre->entreprise->adresse }}</p>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
