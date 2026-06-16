@extends('layouts.particulier')
@section('title', 'Offres suggérées')

@section('part-content')

    <div class="part-page-header">
        <h1>Pour <span>Toi</span></h1>
        <p>{{ $suggestions->count() }} offre(s) basées sur tes compétences</p>
    </div>

    {{-- Compétences utilisées --}}
    @if($particulier->competances->count() > 0)
        <div style="background:var(--part-yellow);border:2px solid var(--part-black);border-radius:var(--part-radius-lg);padding:1rem 1.5rem;margin-bottom:2rem;display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            <span style="font-size:0.8rem;font-weight:800;text-transform:uppercase;letter-spacing:0.3px;">Tes skills :</span>
            @foreach($particulier->competances as $comp)
                <span class="part-comp-tag">{{ $comp->nom }}</span>
            @endforeach
            <a href="{{ route('particulier.profil') }}" class="part-btn part-btn-black part-btn-sm" style="margin-left:auto;">
                <i class="fas fa-edit"></i> Modifier
            </a>
        </div>
    @else
        <div style="background:var(--part-gray);border:2px dashed var(--part-black);border-radius:var(--part-radius-lg);padding:1rem 1.5rem;margin-bottom:2rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
            <span style="font-size:0.88rem;font-weight:600;">
                <i class="fas fa-exclamation-triangle" style="color:var(--part-yellow);"></i>
                Ajoute des compétences pour recevoir des suggestions personnalisées.
            </span>
            <a href="{{ route('particulier.profil') }}" class="part-btn part-btn-primary part-btn-sm">Ajouter</a>
        </div>
    @endif

    {{-- Grille de suggestions --}}
    @if($suggestions->count() > 0)
        <div class="part-offre-grid">
            @foreach($suggestions as $item)
                <a href="{{ route('offres.show', $item['offre']->id) }}" class="part-offre-card">

                    @if($item['score'] > 0)
                        <div style="position:absolute;top:1rem;right:1rem;">
                            <span class="part-badge part-badge-yellow">
                                <i class="fas fa-star"></i> {{ $item['score'] }} match{{ $item['score'] > 1 ? 's' : '' }}
                            </span>
                        </div>
                    @endif

                    <div class="part-offre-header">
                        <div class="part-company-logo">
                            @if($item['offre']->entreprise->logo)
                                <img src="{{ asset('storage/'.$item['offre']->entreprise->logo) }}" alt="">
                            @else
                                {{ strtoupper(substr($item['offre']->entreprise->nom, 0, 2)) }}
                            @endif
                        </div>
                        <div>
                            <div class="part-offre-title">{{ $item['offre']->titre }}</div>
                            <div class="part-offre-company">{{ $item['offre']->entreprise->nom }}</div>
                        </div>
                    </div>

                    <p style="font-size:0.83rem;line-height:1.5;margin-bottom:0.75rem;opacity:0.75;">
                        {{ Str::limit($item['offre']->description, 80) }}
                    </p>

                    {{-- Compétences matchées --}}
                    @if(count($item['matched']) > 0)
                        <div style="display:flex;flex-wrap:wrap;gap:0.3rem;margin-bottom:0.75rem;">
                            @foreach($item['matched'] as $comp)
                                <span class="part-comp-matched">
                                    <i class="fas fa-check"></i> {{ $comp }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="part-offre-footer">
                        <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                            @if($item['offre']->localisation)
                                <span class="part-chip"><i class="fas fa-map-marker-alt"></i> {{ $item['offre']->localisation }}</span>
                            @endif
                            @if($item['offre']->contrat)
                                <span class="part-badge part-badge-black">{{ $item['offre']->contrat }}</span>
                            @endif
                        </div>
                        <span class="part-offre-date">{{ $item['offre']->date_publication->diffForHumans() }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="part-empty">
            <i class="fas fa-search"></i>
            <strong>Aucune offre correspondante</strong>
            <p>Ajoute plus de compétences ou consulte toutes les offres.</p>
            <div style="display:flex;gap:0.75rem;justify-content:center;margin-top:1.25rem;">
                <a href="{{ route('particulier.profil') }}" class="part-btn part-btn-primary">Mes compétences</a>
                <a href="{{ route('offres.index') }}" class="part-btn part-btn-outline">Toutes les offres</a>
            </div>
        </div>
    @endif

@endsection