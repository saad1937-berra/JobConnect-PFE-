@extends('layouts.app')
@section('title', 'Candidats matchés')

@push('styles')
<style>
    .matching-page { padding: 2.5rem 0; }
    .page-header { margin-bottom: 2rem; }
    .page-header h1 { font-family: var(--font-head); font-size: 2rem; font-weight: 800; letter-spacing:-0.5px; }
    .page-header p { color: var(--muted); margin-top: 0.25rem; }

    .offre-banner {
        background: var(--ink); color: var(--paper);
        border-radius: var(--radius); padding: 1.5rem 2rem;
        margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;
    }
    .offre-banner h3 { font-family: var(--font-head); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem; }
    .offre-banner p { font-size: 0.85rem; color: #aaa; }

    .candidats-list { display: flex; flex-direction: column; gap: 1rem; }

    .candidat-item {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.5rem;
        display: flex; align-items: center; gap: 1.25rem;
        transition: all 0.2s;
    }
    .candidat-item:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.07); }

    .avatar {
        width: 52px; height: 52px; border-radius: 50%;
        background: var(--ink); color: var(--paper);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-head); font-size: 1.1rem; font-weight: 800;
        flex-shrink: 0;
    }

    .candidat-info { flex: 1; }
    .candidat-info h3 { font-family: var(--font-head); font-size: 1rem; font-weight: 700; margin-bottom: 0.2rem; }
    .candidat-info .email { font-size: 0.82rem; color: var(--muted); margin-bottom: 0.5rem; }

    .comp-tags { display: flex; flex-wrap: wrap; gap: 0.3rem; }
    .comp-match { background: #d4edda; color: #155724; padding: 0.15rem 0.55rem; border-radius: 5px; font-size: 0.72rem; font-weight: 600; }
    .comp-other { background: var(--paper); color: var(--muted); padding: 0.15rem 0.55rem; border-radius: 5px; font-size: 0.72rem; border: 1px solid var(--border); }

    .matching-gauge { flex-shrink: 0; text-align: center; min-width: 110px; }
    .gauge-score { font-family: var(--font-head); font-size: 1.4rem; font-weight: 800; line-height: 1; }
    .gauge-bar { height: 8px; background: var(--paper); border-radius: 4px; overflow: hidden; margin: 0.4rem 0; }
    .gauge-fill { height: 100%; border-radius: 4px; }
    .gauge-niveau { font-size: 0.72rem; color: var(--muted); }

    .candidat-actions { flex-shrink: 0; display: flex; flex-direction: column; gap: 0.4rem; }

    .empty-state { text-align: center; padding: 4rem; color: var(--muted); background: white; border: 1px solid var(--border); border-radius: var(--radius); }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; }
</style>
@endpush

@section('content')
<div class="container matching-page">

    <div class="page-header">
        <h1><i class="fas fa-users" style="color:var(--accent);margin-right:0.5rem;"></i> Candidats matchés</h1>
        <p>{{ $candidatsMatches->count() }} candidat(s) classés par compatibilité</p>
    </div>

    <!-- Offre concernée -->
    <div class="offre-banner">
        <div>
            <h3>{{ $offre->titre }}</h3>
            <p>
                @if($offre->localisation) <i class="fas fa-map-marker-alt"></i> {{ $offre->localisation }} &nbsp; @endif
                @if($offre->contrat) <i class="fas fa-briefcase"></i> {{ $offre->contrat }} @endif
                @if($offre->niveau_etude) &nbsp; <i class="fas fa-graduation-cap"></i> {{ $offre->niveau_etude }} @endif
            </p>
        </div>
        <a href="{{ route('offres.show', $offre->id) }}" class="btn btn-outline" style="color:var(--paper);border-color:rgba(255,255,255,0.2);">
            Voir l'offre <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    @if($candidatsMatches->count() > 0)
        <div class="candidats-list">
            @foreach($candidatsMatches as $particulier)
                @php
                    $score   = $particulier->matching['score'];
                    $couleur = $particulier->matching['couleur'];
                    $niveau  = $particulier->matching['niveau'];
                    $matched = $particulier->matching['criteres']['competences']['detail'] ?? [];
                    $autres  = $particulier->competances->pluck('nom')->diff($matched)->take(3);
                    $initials = strtoupper(substr($particulier->utilisateur->prenom, 0, 1) . substr($particulier->utilisateur->nom, 0, 1));
                @endphp

                <div class="candidat-item">
                    <div class="avatar">{{ $initials }}</div>

                    <div class="candidat-info">
                        <h3>{{ $particulier->utilisateur->prenom }} {{ $particulier->utilisateur->nom }}</h3>
                        <div class="email">{{ $particulier->utilisateur->email }}</div>
                        <div class="comp-tags">
                            @foreach($matched as $comp)
                                <span class="comp-match"><i class="fas fa-check"></i> {{ $comp }}</span>
                            @endforeach
                            @foreach($autres as $comp)
                                <span class="comp-other">{{ $comp }}</span>
                            @endforeach
                        </div>
                        <div style="font-size:0.78rem;color:var(--muted);margin-top:0.4rem;display:flex;gap:0.75rem;">
                            @if($particulier->adresse) <span><i class="fas fa-map-marker-alt"></i> {{ $particulier->adresse }}</span> @endif
                            @if($particulier->cv->count() > 0) <span><i class="fas fa-file-pdf" style="color:var(--accent);"></i> CV dispo</span> @endif
                        </div>
                    </div>

                    <!-- Jauge -->
                    <div class="matching-gauge">
                        <div class="gauge-score" style="color:{{ $couleur }}">{{ $score }}%</div>
                        <div class="gauge-bar">
                            <div class="gauge-fill" style="width:{{ $score }}%;background:{{ $couleur }};"></div>
                        </div>
                        <div class="gauge-niveau" style="color:{{ $couleur }}">{{ $niveau }}</div>
                    </div>

                    <!-- Actions -->
                    <div class="candidat-actions">
                        @if($particulier->cv->count() > 0)
                            <a href="{{ asset('storage/'.$particulier->cv->last()->cv_path) }}" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fas fa-file-pdf"></i> CV
                            </a>
                        @endif
                        <a href="mailto:{{ $particulier->utilisateur->email }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <p style="font-size:1.05rem;font-weight:500;margin-bottom:0.5rem;">Aucun candidat compatible</p>
            <p style="font-size:0.9rem;">Aucun candidat inscrit ne correspond à cette offre pour le moment.</p>
        </div>
    @endif
</div>
@endsection