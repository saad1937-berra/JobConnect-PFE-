@extends('layouts.app')
@section('title', 'Score de matching')

@push('styles')
<style>
    .matching-page { padding: 2.5rem 0; }

    .breadcrumb { font-size: 0.85rem; color: var(--muted); margin-bottom: 1.5rem; }
    .breadcrumb a { color: var(--accent2); text-decoration: none; }

    .matching-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 2rem;
        align-items: start;
    }

    /* Score principal */
    .score-card {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 2rem;
        text-align: center; margin-bottom: 1.25rem;
    }

    .score-circle {
        position: relative;
        width: 160px; height: 160px;
        margin: 0 auto 1.5rem;
    }

    .score-circle svg { transform: rotate(-90deg); }

    .score-circle .score-text {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .score-circle .score-number {
        font-family: var(--font-head);
        font-size: 2.8rem;
        font-weight: 800;
        line-height: 1;
    }

    .score-circle .score-label {
        font-size: 0.8rem;
        color: var(--muted);
        margin-top: 0.25rem;
    }

    .score-niveau {
        font-family: var(--font-head);
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .score-desc { font-size: 0.88rem; color: var(--muted); }

    /* Critères */
    .criteres-card {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.75rem;
        margin-bottom: 1.25rem;
    }

    .criteres-card h3 {
        font-family: var(--font-head); font-size: 1rem; font-weight: 700;
        margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);
    }

    .critere-item { margin-bottom: 1.25rem; }
    .critere-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    .critere-label {
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.88rem; font-weight: 600;
    }
    .critere-score { font-size: 0.82rem; color: var(--muted); font-weight: 500; }

    .progress-bar {
        height: 8px; background: var(--paper);
        border-radius: 4px; overflow: hidden;
    }

    .progress-fill {
        height: 100%; border-radius: 4px;
        transition: width 0.8s ease;
    }

    .critere-detail {
        font-size: 0.78rem; color: var(--muted);
        margin-top: 0.35rem;
    }

    .matched-comp {
        display: inline-flex; align-items: center; gap: 0.25rem;
        background: #d4edda; color: #155724;
        padding: 0.15rem 0.55rem; border-radius: 5px;
        font-size: 0.75rem; font-weight: 600; margin: 0.15rem;
    }

    /* Sidebar offre */
    .offre-card {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.5rem;
        margin-bottom: 1.25rem;
    }

    .offre-card h3 {
        font-family: var(--font-head); font-size: 1rem; font-weight: 700;
        margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);
    }

    .info-row {
        display: flex; justify-content: space-between;
        padding: 0.5rem 0; border-bottom: 1px solid var(--border);
        font-size: 0.85rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .lbl { color: var(--muted); }
    .info-row .val { font-weight: 600; }

    .company-header {
        display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;
    }
    .company-logo {
        width: 48px; height: 48px; border-radius: 10px;
        background: var(--paper); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-head); font-weight: 800; color: var(--accent);
        overflow: hidden;
    }
    .company-logo img { width: 100%; height: 100%; object-fit: cover; }

    @media(max-width:900px) { .matching-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="container matching-page">

    <div class="breadcrumb">
        <a href="{{ route('home') }}">Accueil</a> /
        <a href="{{ route('particulier.matching') }}">Matching</a> /
        {{ $offre->titre }}
    </div>

    <div class="matching-layout">

        <!-- Colonne principale -->
        <div>
            <!-- Score global -->
            <div class="score-card">
                @php $score = $matching['score']; $couleur = $matching['couleur']; @endphp

                <div class="score-circle">
                    <svg width="160" height="160" viewBox="0 0 160 160">
                        <!-- Fond -->
                        <circle cx="80" cy="80" r="65" fill="none" stroke="#f0ede6" stroke-width="12"/>
                        <!-- Jauge -->
                        <circle cx="80" cy="80" r="65" fill="none"
                                stroke="{{ $couleur }}" stroke-width="12"
                                stroke-linecap="round"
                                stroke-dasharray="{{ round(2 * M_PI * 65) }}"
                                stroke-dashoffset="{{ round(2 * M_PI * 65 * (1 - $score / 100)) }}"
                                style="transition: stroke-dashoffset 1s ease;"/>
                    </svg>
                    <div class="score-text">
                        <span class="score-number" style="color:{{ $couleur }}">{{ $score }}</span>
                        <span class="score-label">/ 100</span>
                    </div>
                </div>

                <div class="score-niveau" style="color:{{ $couleur }}">{{ $matching['niveau'] }}</div>
                <div class="score-desc">
                    @if($score >= 80) Excellent profil pour cette offre — postulez dès maintenant !
                    @elseif($score >= 60) Bon profil — vous correspondez bien à cette offre.
                    @elseif($score >= 40) Profil moyen — quelques points à améliorer.
                    @else Profil peu compatible — consultez d'autres offres.
                    @endif
                </div>

                <div style="display:flex;gap:0.75rem;justify-content:center;margin-top:1.5rem;">
                    <form method="POST" action="{{ route('particulier.postuler') }}">
                        @csrf
                        <input type="hidden" name="offre_id" value="{{ $offre->id }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Postuler
                        </button>
                    </form>
                    <a href="{{ route('offres.show', $offre->id) }}" class="btn btn-outline">
                        Voir l'offre
                    </a>
                </div>
            </div>

            <!-- Détail par critère -->
            <div class="criteres-card">
                <h3><i class="fas fa-chart-bar" style="color:var(--accent);margin-right:0.5rem;"></i> Détail du score</h3>

                @foreach($matching['criteres'] as $key => $critere)
                    @php $pct = $critere['max'] > 0 ? round($critere['score'] / $critere['max'] * 100) : 0; @endphp
                    <div class="critere-item">
                        <div class="critere-header">
                            <span class="critere-label">
                                <i class="fas {{ $critere['icone'] }}" style="color:var(--accent);width:16px;"></i>
                                {{ $critere['label'] }}
                            </span>
                            <span class="critere-score">{{ $critere['score'] }} / {{ $critere['max'] }} pts</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width:{{ $pct }}%;background:{{ $pct >= 80 ? '#28a745' : ($pct >= 50 ? '#17a2b8' : ($pct >= 30 ? '#ffc107' : '#dc3545')) }};"></div>
                        </div>

                        @if(isset($critere['detail']) && is_array($critere['detail']) && count($critere['detail']) > 0)
                            <div class="critere-detail">
                                @foreach($critere['detail'] as $comp)
                                    <span class="matched-comp"><i class="fas fa-check"></i> {{ $comp }}</span>
                                @endforeach
                            </div>
                        @elseif(isset($critere['detail']) && is_string($critere['detail']))
                            <div class="critere-detail">{{ $critere['detail'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Conseils pour améliorer -->
            @php $score = $matching['score']; @endphp
            @if($score < 80)
                <div style="background:white;border:1px solid var(--border);border-radius:var(--radius);padding:1.75rem;">
                    <h3 style="font-family:var(--font-head);font-size:1rem;font-weight:700;margin-bottom:1.25rem;">
                        <i class="fas fa-lightbulb" style="color:#ffc107;margin-right:0.5rem;"></i> Comment améliorer votre score
                    </h3>
                    @if($matching['criteres']['competences']['score'] < 30)
                        <div style="display:flex;gap:0.75rem;align-items:flex-start;margin-bottom:0.85rem;">
                            <i class="fas fa-star" style="color:var(--accent);margin-top:0.1rem;"></i>
                            <div>
                                <strong style="font-size:0.88rem;">Ajoutez des compétences à votre profil</strong>
                                <p style="font-size:0.82rem;color:var(--muted);">Plus vous avez de compétences correspondantes, meilleur est votre score.</p>
                            </div>
                        </div>
                    @endif
                    @if($matching['criteres']['profil']['score'] < 8)
                        <div style="display:flex;gap:0.75rem;align-items:flex-start;margin-bottom:0.85rem;">
                            <i class="fas fa-user" style="color:var(--accent2);margin-top:0.1rem;"></i>
                            <div>
                                <strong style="font-size:0.88rem;">Complétez votre profil</strong>
                                <p style="font-size:0.82rem;color:var(--muted);">Ajoutez votre bio, téléphone et uploadez votre CV.</p>
                            </div>
                        </div>
                    @endif
                    @if($matching['criteres']['localisation']['score'] < 15)
                        <div style="display:flex;gap:0.75rem;align-items:flex-start;">
                            <i class="fas fa-map-marker-alt" style="color:#28a745;margin-top:0.1rem;"></i>
                            <div>
                                <strong style="font-size:0.88rem;">Renseignez votre adresse</strong>
                                <p style="font-size:0.82rem;color:var(--muted);">Une localisation précise améliore votre score de matching.</p>
                            </div>
                        </div>
                    @endif
                    <a href="{{ route('particulier.profil') }}" class="btn btn-primary btn-sm" style="margin-top:1rem;">
                        <i class="fas fa-edit"></i> Améliorer mon profil
                    </a>
                </div>
            @endif
        </div>

        <!-- Sidebar : infos offre -->
        <div>
            <div class="offre-card">
                <div class="company-header">
                    <div class="company-logo">
                        @if($offre->entreprise->logo)
                            <img src="{{ asset('storage/'.$offre->entreprise->logo) }}" alt="">
                        @else
                            {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        <div style="font-family:var(--font-head);font-weight:700;">{{ $offre->titre }}</div>
                        <div style="font-size:0.82rem;color:var(--muted);">{{ $offre->entreprise->nom }}</div>
                    </div>
                </div>

                <h3>Détails du poste</h3>
                <div class="info-row"><span class="lbl"><i class="fas fa-briefcase"></i> Contrat</span><span class="val">{{ $offre->contrat ?? '—' }}</span></div>
                <div class="info-row"><span class="lbl"><i class="fas fa-map-marker-alt"></i> Lieu</span><span class="val">{{ $offre->localisation ?? '—' }}</span></div>
                <div class="info-row"><span class="lbl"><i class="fas fa-graduation-cap"></i> Niveau</span><span class="val">{{ $offre->niveau_etude ?? '—' }}</span></div>
                <div class="info-row"><span class="lbl"><i class="fas fa-money-bill-wave"></i> Salaire</span><span class="val">{{ $offre->salaire ?? 'Non précisé' }}</span></div>
                <div class="info-row"><span class="lbl"><i class="fas fa-clock"></i> Durée</span><span class="val">{{ $offre->duree ?? '—' }}</span></div>
            </div>

            <!-- Mon profil recap -->
            <div class="offre-card">
                <h3>Mon profil</h3>
                <div class="info-row"><span class="lbl">Compétences</span><span class="val">{{ $particulier->competances->count() }}</span></div>
                <div class="info-row"><span class="lbl">CV</span><span class="val">{{ $particulier->cv->count() > 0 ? '✅ Disponible' : '❌ Manquant' }}</span></div>
                <div class="info-row"><span class="lbl">Localisation</span><span class="val">{{ $particulier->adresse ?? 'Non renseignée' }}</span></div>
                <div class="info-row"><span class="lbl">Bio</span><span class="val">{{ !empty($particulier->bio) ? '✅' : '❌' }}</span></div>
            </div>

            <a href="{{ route('particulier.matching') }}" class="btn btn-outline" style="width:100%;justify-content:center;">
                <i class="fas fa-arrow-left"></i> Toutes les offres matchées
            </a>
        </div>

    </div>
</div>
@endsection