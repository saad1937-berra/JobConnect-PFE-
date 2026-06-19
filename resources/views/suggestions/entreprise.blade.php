@extends('layouts.entreprise')
@section('title', 'Suggestions de candidats')

@section('ent-content')

    <div class="ent-page-header">
        <div>
            <h1>Suggestions de candidats</h1>
            <p>{{ $suggestions->count() }} candidat(s) suggere(s) selon les competences de l'offre</p>
        </div>
        <a href="{{ route('entreprise.offres.matching', $offre->id) }}" class="ent-btn ent-btn-primary">
            <i class="fas fa-chart-line"></i> Voir les matchs
        </a>
    </div>

    <div class="ent-card" style="padding:1.25rem 1.5rem;margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
            <div>
                <div class="ent-td-title">{{ $offre->titre }}</div>
                <div class="ent-td-sub">
                    @if($offre->localisation)
                        <i class="fas fa-map-marker-alt"></i> {{ $offre->localisation }}
                    @endif
                    @if($offre->contrat)
                        &nbsp; <i class="fas fa-briefcase"></i> {{ $offre->contrat }}
                    @endif
                    @if($offre->categorie)
                        &nbsp; <i class="fas fa-tag"></i> {{ $offre->categorie->nom }}
                    @endif
                </div>
            </div>
            <a href="{{ route('offres.show', $offre->id) }}" class="ent-btn ent-btn-outline ent-btn-sm">
                <i class="fas fa-eye"></i> Voir l'offre
            </a>
        </div>
    </div>

    @if($suggestions->count() > 0)
        <div style="display:flex;flex-direction:column;gap:0.85rem;">
            @foreach($suggestions as $item)
                @php
                    $particulier = $item['particulier'];
                    $user = $particulier->utilisateur;
                    $matched = $item['matched'];
                    $initials = strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1));
                    $cv = $particulier->cv->last();
                @endphp

                <div class="ent-card" style="display:flex;align-items:center;gap:1.25rem;padding:1.25rem 1.5rem;">
                    <div class="ent-candidate-avatar" style="width:44px;height:44px;font-size:0.85rem;">
                        {{ $initials }}
                    </div>

                    <div style="flex:1;">
                        <div class="ent-td-title">{{ $user->prenom }} {{ $user->nom }}</div>
                        <div class="ent-td-sub">{{ $user->email }}</div>

                        <div style="display:flex;flex-wrap:wrap;gap:0.3rem;margin-top:0.45rem;">
                            @foreach($matched as $comp)
                                <span class="ent-badge ent-badge-teal">
                                    <i class="fas fa-check"></i> {{ $comp }}
                                </span>
                            @endforeach
                        </div>

                        <div style="font-size:0.75rem;color:var(--ent-muted);margin-top:0.4rem;display:flex;gap:0.75rem;flex-wrap:wrap;">
                            @if($particulier->niveau_etude)
                                <span><i class="fas fa-graduation-cap"></i> {{ $particulier->niveau_etude }}</span>
                            @endif
                            @if($particulier->adresse)
                                <span><i class="fas fa-map-marker-alt"></i> {{ $particulier->adresse }}</span>
                            @endif
                            @if($cv)
                                <span><i class="fas fa-file-pdf" style="color:#dc2626;"></i> CV disponible</span>
                            @endif
                        </div>
                    </div>

                    <div style="min-width:90px;text-align:center;">
                        <div style="font-family:var(--ent-font-head);font-size:1.4rem;font-weight:800;color:var(--ent-green);">
                            {{ $item['score'] }}
                        </div>
                        <div style="font-size:0.72rem;color:var(--ent-muted);text-transform:uppercase;font-weight:700;">
                            competence{{ $item['score'] > 1 ? 's' : '' }}
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:0.4rem;flex-shrink:0;">
                        @if($cv)
                            <span class="ent-badge ent-badge-gray"><i class="fas fa-lock"></i> CV prive</span>
                        @endif
                        <form method="POST" action="{{ route('messages.start') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="ent-btn ent-btn-outline ent-btn-sm">
                                <i class="fas fa-comments"></i> Message
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="ent-card" style="text-align:center;padding:3rem;color:var(--ent-muted);">
            <i class="fas fa-magic" style="font-size:2.5rem;margin-bottom:1rem;display:block;color:var(--ent-border);"></i>
            <strong style="display:block;font-size:1rem;font-weight:700;color:var(--ent-ink);margin-bottom:0.35rem;">
                Aucun candidat suggere
            </strong>
            <p style="font-size:0.88rem;">Ajoutez des competences a cette offre pour obtenir des suggestions plus pertinentes.</p>
        </div>
    @endif

@endsection
