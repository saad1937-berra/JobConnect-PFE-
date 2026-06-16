@extends('layouts.particulier')
@section('title', 'Offres matchées')

@section('part-content')

    <div class="part-page-header">
        <h1>Offres <span>Matchées</span></h1>
        <p>{{ $offresMatchees->count() }} offre(s) classées par compatibilité avec ton profil</p>
    </div>

    @if($offresMatchees->count() > 0)
        <div style="display:flex;flex-direction:column;gap:0.85rem;">
            @foreach($offresMatchees as $offre)
                @php
                    $score   = $offre->matching['score'];
                    $couleur = $offre->matching['couleur'];
                    $niveau  = $offre->matching['niveau'];
                @endphp
                <div class="part-card" style="display:flex;align-items:center;gap:1.25rem;padding:1.25rem 1.5rem;">

                    {{-- Logo --}}
                    <div class="part-company-logo" style="width:48px;height:48px;">
                        @if($offre->entreprise->logo)
                            <img src="{{ asset('storage/'.$offre->entreprise->logo) }}" alt="">
                        @else
                            {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                        @endif
                    </div>

                    {{-- Info --}}
                    <div style="flex:1;">
                        <div style="font-family:var(--part-font-head);font-size:1rem;font-weight:700;margin-bottom:0.2rem;">
                            {{ $offre->titre }}
                        </div>
                        <div style="font-size:0.82rem;color:var(--part-muted);margin-bottom:0.5rem;">
                            {{ $offre->entreprise->nom }} • {{ $offre->localisation ?? 'Non précisé' }}
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:0.4rem;">
                            @if($offre->contrat)
                                <span class="part-badge part-badge-black">{{ $offre->contrat }}</span>
                            @endif
                            @if($offre->salaire)
                                <span class="part-chip"><i class="fas fa-money-bill-wave"></i> {{ $offre->salaire }}</span>
                            @endif
                            @foreach(array_slice($offre->matching['criteres']['competences']['detail'], 0, 3) as $comp)
                                <span class="part-comp-matched"><i class="fas fa-check"></i> {{ $comp }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Jauge --}}
                    <div class="part-gauge">
                        <div class="part-gauge-score" style="color:{{ $couleur }}">{{ $score }}%</div>
                        <div class="part-gauge-bar">
                            <div class="part-gauge-fill" style="width:{{ $score }}%;background:{{ $couleur }};"></div>
                        </div>
                        <div class="part-gauge-niveau" style="color:{{ $couleur }}">{{ $niveau }}</div>
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;flex-direction:column;gap:0.4rem;flex-shrink:0;">
                        <a href="{{ route('particulier.matching.score', $offre->id) }}" class="part-btn part-btn-primary part-btn-sm">
                            <i class="fas fa-chart-bar"></i> Détails
                        </a>
                        <a href="{{ route('offres.show', $offre->id) }}" class="part-btn part-btn-outline part-btn-sm">
                            Voir
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="part-empty">
            <i class="fas fa-search"></i>
            <strong>Aucune offre matchée</strong>
            <p>Complète ton profil et ajoute des compétences pour voir tes résultats.</p>
            <div style="display:flex;gap:0.75rem;justify-content:center;margin-top:1.25rem;">
                <a href="{{ route('particulier.profil') }}" class="part-btn part-btn-primary">Mon profil</a>
                <a href="{{ route('offres.index') }}" class="part-btn part-btn-outline">Toutes les offres</a>
            </div>
        </div>
    @endif

@endsection