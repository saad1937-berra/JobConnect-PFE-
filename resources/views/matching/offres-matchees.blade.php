@extends('layouts.app')
@section('title', 'Offres matchées')

@push('styles')
<style>
    .matching-page { padding: 2.5rem 0; }
    .page-header { margin-bottom: 2rem; }
    .page-header h1 { font-family: var(--font-head); font-size: 2rem; font-weight: 800; letter-spacing:-0.5px; }
    .page-header p { color: var(--muted); margin-top: 0.25rem; }

    .offres-list { display: flex; flex-direction: column; gap: 1rem; }

    .offre-item {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.5rem;
        display: flex; align-items: center; gap: 1.25rem;
        transition: all 0.2s;
    }
    .offre-item:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.07); border-color: var(--accent2); }

    .company-logo {
        width: 52px; height: 52px; border-radius: 10px;
        background: var(--paper); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-head); font-weight: 800; color: var(--accent);
        flex-shrink: 0; overflow: hidden;
    }
    .company-logo img { width: 100%; height: 100%; object-fit: cover; }

    .offre-info { flex: 1; }
    .offre-info h3 { font-family: var(--font-head); font-size: 1rem; font-weight: 700; margin-bottom: 0.2rem; }
    .offre-info .company { font-size: 0.85rem; color: var(--muted); margin-bottom: 0.5rem; }

    .offre-chips { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .chip { display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.78rem; color: var(--muted); padding: 0.2rem 0.6rem; background: var(--paper); border-radius: 5px; }

    /* Jauge de matching */
    .matching-gauge { flex-shrink: 0; text-align: center; min-width: 120px; }

    .gauge-bar {
        height: 8px; background: var(--paper);
        border-radius: 4px; overflow: hidden; margin: 0.4rem 0;
    }
    .gauge-fill { height: 100%; border-radius: 4px; }

    .gauge-score {
        font-family: var(--font-head); font-size: 1.4rem; font-weight: 800; line-height: 1;
    }
    .gauge-niveau { font-size: 0.72rem; color: var(--muted); }

    .offre-actions { flex-shrink: 0; display: flex; flex-direction: column; gap: 0.4rem; }

    .empty-state { text-align: center; padding: 4rem; color: var(--muted); background: white; border: 1px solid var(--border); border-radius: var(--radius); }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; }
</style>
@endpush

@section('content')
<div class="container matching-page">

    <div class="page-header">
        <h1><i class="fas fa-percentage" style="color:var(--accent);margin-right:0.5rem;"></i> Offres matchées</h1>
        <p>{{ $offresMatchees->count() }} offre(s) classées par compatibilité avec votre profil</p>
    </div>

    @if($offresMatchees->count() > 0)
        <div class="offres-list">
            @foreach($offresMatchees as $offre)
                @php
                    $score   = $offre->matching['score'];
                    $couleur = $offre->matching['couleur'];
                    $niveau  = $offre->matching['niveau'];
                @endphp
                <div class="offre-item">
                    <div class="company-logo">
                        @if($offre->entreprise->logo)
                            <img src="{{ asset('storage/'.$offre->entreprise->logo) }}" alt="">
                        @else
                            {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                        @endif
                    </div>

                    <div class="offre-info">
                        <h3>{{ $offre->titre }}</h3>
                        <div class="company">{{ $offre->entreprise->nom }} • {{ $offre->localisation ?? 'Non précisé' }}</div>
                        <div class="offre-chips">
                            @if($offre->contrat) <span class="badge badge-blue">{{ $offre->contrat }}</span> @endif
                            @if($offre->salaire) <span class="chip"><i class="fas fa-money-bill-wave"></i> {{ $offre->salaire }}</span> @endif
                            @if(count($offre->matching['criteres']['competences']['detail']) > 0)
                                @foreach(array_slice($offre->matching['criteres']['competences']['detail'], 0, 3) as $comp)
                                    <span style="background:#d4edda;color:#155724;padding:0.15rem 0.55rem;border-radius:5px;font-size:0.72rem;font-weight:600;">
                                        <i class="fas fa-check"></i> {{ $comp }}
                                    </span>
                                @endforeach
                            @endif
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
                    <div class="offre-actions">
                        <a href="{{ route('particulier.matching.score', $offre->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-chart-bar"></i> Détails
                        </a>
                        <a href="{{ route('offres.show', $offre->id) }}" class="btn btn-outline btn-sm">
                            Voir l'offre
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <p style="font-size:1.05rem;font-weight:500;margin-bottom:0.5rem;">Aucune offre disponible</p>
            <p style="font-size:0.9rem;">Complétez votre profil et ajoutez des compétences pour obtenir des résultats.</p>
            <div style="display:flex;gap:0.75rem;justify-content:center;margin-top:1.5rem;">
                <a href="{{ route('particulier.profil') }}" class="btn btn-primary">Compléter mon profil</a>
                <a href="{{ route('offres.index') }}" class="btn btn-outline">Toutes les offres</a>
            </div>
        </div>
    @endif
</div>
@endsection