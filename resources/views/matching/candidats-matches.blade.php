@extends('layouts.entreprise')
@section('title', 'Candidats matchés')

@section('ent-content')

    <div class="ent-page-header">
        <div>
            <h1>Candidats matchés</h1>
            <p>{{ $candidatsMatches->count() }} candidat(s) classés par compatibilité</p>
        </div>
    </div>

    <!-- Offre concernée -->
    <div style="background:var(--ent-green);color:white;border-radius:var(--ent-radius);padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
        <div>
            <h3 style="font-family:var(--ent-font-head);font-size:1rem;font-weight:700;margin-bottom:0.2rem;">{{ $offre->titre }}</h3>
            <p style="font-size:0.83rem;color:rgba(255,255,255,0.7);">
                @if($offre->localisation) <i class="fas fa-map-marker-alt"></i> {{ $offre->localisation }} &nbsp; @endif
                @if($offre->contrat) <i class="fas fa-briefcase"></i> {{ $offre->contrat }} @endif
            </p>
        </div>
        <a href="{{ route('offres.show', $offre->id) }}" class="ent-btn ent-btn-outline" style="color:white;border-color:rgba(255,255,255,0.3);">
            Voir l'offre <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    @if($candidatsMatches->count() > 0)
        <div style="display:flex;flex-direction:column;gap:0.85rem;">
            @foreach($candidatsMatches as $particulier)
                @php
                    $score   = $particulier->matching['score'];
                    $couleur = $particulier->matching['couleur'];
                    $niveau  = $particulier->matching['niveau'];
                    $matched = $particulier->matching['criteres']['competences']['detail'] ?? [];
                    $autres  = $particulier->competances->pluck('nom')->diff($matched)->take(3);
                    $initials = strtoupper(substr($particulier->utilisateur->prenom, 0, 1) . substr($particulier->utilisateur->nom, 0, 1));
                @endphp

                <div class="ent-card" style="display:flex;align-items:center;gap:1.25rem;padding:1.25rem 1.5rem;">
                    <div class="ent-candidate-avatar" style="width:44px;height:44px;font-size:0.85rem;">{{ $initials }}</div>

                    <div style="flex:1;">
                        <div class="ent-td-title">{{ $particulier->utilisateur->prenom }} {{ $particulier->utilisateur->nom }}</div>
                        <div class="ent-td-sub">{{ $particulier->utilisateur->email }}</div>
                        <div style="display:flex;flex-wrap:wrap;gap:0.3rem;margin-top:0.4rem;">
                            @foreach($matched as $comp)
                                <span class="ent-badge ent-badge-teal"><i class="fas fa-check"></i> {{ $comp }}</span>
                            @endforeach
                            @foreach($autres as $comp)
                                <span class="ent-badge ent-badge-gray">{{ $comp }}</span>
                            @endforeach
                        </div>
                        <div style="font-size:0.75rem;color:var(--ent-muted);margin-top:0.35rem;display:flex;gap:0.75rem;">
                            @if($particulier->adresse) <span><i class="fas fa-map-marker-alt"></i> {{ $particulier->adresse }}</span> @endif
                            @if($particulier->cv->count() > 0) <span><i class="fas fa-file-pdf" style="color:#dc2626;"></i> CV dispo</span> @endif
                        </div>
                    </div>

                    <div class="ent-candidate-info" style="flex-direction:column;text-align:center;min-width:90px;gap:0.25rem;">
                        <div style="font-family:var(--ent-font-head);font-size:1.4rem;font-weight:800;color:{{ $couleur }};">{{ $score }}%</div>
                        <div style="height:6px;background:var(--ent-bg);border-radius:3px;width:80px;overflow:hidden;">
                            <div style="width:{{ $score }}%;height:100%;background:{{ $couleur }};border-radius:3px;"></div>
                        </div>
                        <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;color:{{ $couleur }};">{{ $niveau }}</div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:0.4rem;flex-shrink:0;">
                        @if($particulier->cv->count() > 0)
                            <a href="{{ asset('storage/'.$particulier->cv->last()->cv_path) }}" target="_blank" class="ent-btn ent-btn-primary ent-btn-sm">
                                <i class="fas fa-file-pdf"></i> CV
                            </a>
                        @endif
                        <a href="mailto:{{ $particulier->utilisateur->email }}" class="ent-btn ent-btn-outline ent-btn-sm">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="ent-card" style="text-align:center;padding:3rem;color:var(--ent-muted);">
            <i class="fas fa-users" style="font-size:2.5rem;margin-bottom:1rem;display:block;color:var(--ent-border);"></i>
            <strong style="display:block;font-size:1rem;font-weight:700;color:var(--ent-ink);margin-bottom:0.35rem;">Aucun candidat compatible</strong>
            <p style="font-size:0.88rem;">Aucun candidat inscrit ne correspond à cette offre pour le moment.</p>
        </div>
    @endif

@endsection