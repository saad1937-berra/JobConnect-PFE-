@extends('layouts.particulier')
@section('title', 'Score de matching')

@section('part-content')

    <div style="font-size:0.82rem;color:var(--part-muted);margin-bottom:1.5rem;display:flex;align-items:center;gap:0.4rem;font-weight:600;">
        <a href="{{ route('home') }}" style="color:var(--part-black);text-decoration:none;">Accueil</a>
        <i class="fas fa-chevron-right" style="font-size:0.65rem;"></i>
        <a href="{{ route('particulier.matching') }}" style="color:var(--part-black);text-decoration:none;">Matching</a>
        <i class="fas fa-chevron-right" style="font-size:0.65rem;"></i>
        <span>{{ $offre->titre }}</span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start;">

        <!-- Colonne principale -->
        <div>

            <!-- Score global -->
            <div class="part-section-card" style="text-align:center;margin-bottom:1.25rem;">
                @php $score = $matching['score']; $couleur = $matching['couleur']; @endphp

                {{-- Cercle SVG --}}
                <div class="part-score-circle">
                    <svg width="150" height="150" viewBox="0 0 150 150">
                        <circle cx="75" cy="75" r="60" fill="none" stroke="var(--part-gray)" stroke-width="10"/>
                        <circle cx="75" cy="75" r="60" fill="none"
                                stroke="{{ $score >= 60 ? 'var(--part-yellow)' : ($score >= 40 ? '#f59e0b' : '#ef4444') }}"
                                stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="{{ round(2 * M_PI * 60) }}"
                                stroke-dashoffset="{{ round(2 * M_PI * 60 * (1 - $score / 100)) }}"
                                style="transition: stroke-dashoffset 1s ease;"/>
                    </svg>
                    <div class="part-score-text">
                        <span class="part-score-number">{{ $score }}</span>
                        <span class="part-score-label">/ 100</span>
                    </div>
                </div>

                <div style="font-family:var(--part-font-head);font-size:1.4rem;font-weight:800;margin-bottom:0.4rem;">
                    {{ $matching['niveau'] }}
                </div>
                <p style="font-size:0.88rem;color:var(--part-muted);margin-bottom:1.5rem;">
                    @if($score >= 80) Excellent profil — postule maintenant !
                    @elseif($score >= 60) Bon profil — tu corresponds bien à cette offre.
                    @elseif($score >= 40) Profil moyen — quelques points à améliorer.
                    @else Profil peu compatible — consulte d'autres offres.
                    @endif
                </p>

                <div style="display:flex;gap:0.75rem;justify-content:center;">
                    <form method="POST" action="{{ route('particulier.postuler') }}">
                        @csrf
                        <input type="hidden" name="offre_id" value="{{ $offre->id }}">
                        <button type="submit" class="part-btn part-btn-primary">
                            <i class="fas fa-paper-plane"></i> Postuler
                        </button>
                    </form>
                    <a href="{{ route('offres.show', $offre->id) }}" class="part-btn part-btn-outline">
                        Voir l'offre
                    </a>
                </div>
            </div>

            <!-- Détail critères -->
            <div class="part-section-card" style="margin-bottom:1.25rem;">
                <div class="part-section-header">
                    <h3><i class="fas fa-chart-bar" style="color:var(--part-yellow);background:var(--part-black);padding:0.3rem 0.4rem;border-radius:4px;font-size:0.8rem;"></i> Détail du score</h3>
                </div>

                @foreach($matching['criteres'] as $key => $critere)
                    @php $pct = $critere['max'] > 0 ? round($critere['score'] / $critere['max'] * 100) : 0; @endphp
                    <div style="margin-bottom:1.25rem;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                            <span style="font-size:0.85rem;font-weight:700;display:flex;align-items:center;gap:0.5rem;">
                                <i class="fas {{ $critere['icone'] }}" style="background:var(--part-yellow);color:var(--part-black);width:22px;height:22px;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:0.72rem;"></i>
                                {{ $critere['label'] }}
                            </span>
                            <span style="font-size:0.8rem;font-weight:700;font-family:var(--part-font-head);">
                                {{ $critere['score'] }} / {{ $critere['max'] }} pts
                            </span>
                        </div>
                        <div style="background:var(--part-gray);border:1.5px solid var(--part-black);border-radius:4px;height:10px;overflow:hidden;">
                            <div style="width:{{ $pct }}%;height:100%;background:{{ $pct >= 80 ? 'var(--part-success)' : ($pct >= 50 ? 'var(--part-yellow)' : ($pct >= 30 ? '#f59e0b' : 'var(--part-error)') ) }};transition:width 0.8s ease;"></div>
                        </div>

                        @if($key === 'competences' && isset($critere['detail_niveau']) && count($critere['detail_niveau']) > 0)
                            <div style="display:flex;flex-wrap:wrap;gap:0.3rem;margin-top:0.5rem;">
                                @foreach($critere['detail_niveau'] as $item)
                                    <span class="part-comp-matched">
                                        <i class="fas fa-check"></i> {{ $item['nom'] }}
                                        <span style="background:var(--part-black);color:var(--part-yellow);font-size:0.62rem;padding:0.05rem 0.35rem;border-radius:3px;">
                                            {{ $item['niveau'] }}
                                        </span>
                                    </span>
                                @endforeach
                            </div>
                        @elseif(isset($critere['note']) && $critere['note'])
                            <div style="font-size:0.78rem;color:var(--part-muted);margin-top:0.35rem;font-weight:500;">{{ $critere['note'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Conseils amélioration -->
            @if($score < 80)
                <div class="part-card-yellow" style="border:2px solid var(--part-black);">
                    <h3 style="font-family:var(--part-font-head);font-size:1rem;font-weight:800;margin-bottom:1rem;">
                        <i class="fas fa-lightbulb"></i> Comment améliorer ton score
                    </h3>
                    @if($matching['criteres']['competences']['score'] < 25)
                        <div style="display:flex;gap:0.75rem;margin-bottom:0.75rem;font-size:0.85rem;">
                            <i class="fas fa-star" style="margin-top:0.1rem;"></i>
                            <div><strong>Ajoute des compétences</strong><br><span style="opacity:0.75;">Plus de skills = meilleur matching.</span></div>
                        </div>
                    @endif
                    @if($matching['criteres']['profil']['score'] < 7)
                        <div style="display:flex;gap:0.75rem;margin-bottom:0.75rem;font-size:0.85rem;">
                            <i class="fas fa-user" style="margin-top:0.1rem;"></i>
                            <div><strong>Complète ton profil</strong><br><span style="opacity:0.75;">Bio, téléphone, CV — ça compte !</span></div>
                        </div>
                    @endif
                    @if($matching['criteres']['localisation']['score'] < 15)
                        <div style="display:flex;gap:0.75rem;font-size:0.85rem;">
                            <i class="fas fa-map-marker-alt" style="margin-top:0.1rem;"></i>
                            <div><strong>Renseigne ton adresse</strong><br><span style="opacity:0.75;">La localisation améliore le score.</span></div>
                        </div>
                    @endif
                    <a href="{{ route('particulier.profil') }}" class="part-btn part-btn-black part-btn-sm" style="margin-top:1rem;">
                        <i class="fas fa-edit"></i> Améliorer mon profil
                    </a>
                </div>
            @endif
        </div>

        <!-- Sidebar infos offre -->
        <div style="position:sticky;top:calc(var(--part-topbar-h) + 1rem);">

            <div class="part-section-card" style="margin-bottom:1rem;">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;padding-bottom:1rem;border-bottom:2px solid var(--part-black);">
                    <div class="part-company-logo" style="width:48px;height:48px;">
                        @if($offre->entreprise->logo)
                            <img src="{{ asset('storage/'.$offre->entreprise->logo) }}" alt="">
                        @else
                            {{ strtoupper(substr($offre->entreprise->nom, 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        <div style="font-family:var(--part-font-head);font-weight:700;font-size:0.9rem;">{{ $offre->titre }}</div>
                        <div style="font-size:0.78rem;color:var(--part-muted);">{{ $offre->entreprise->nom }}</div>
                    </div>
                </div>

                @foreach([['fas fa-briefcase', 'Contrat', $offre->contrat ?? '—'], ['fas fa-map-marker-alt', 'Lieu', $offre->localisation ?? '—'], ['fas fa-graduation-cap', 'Niveau', $offre->niveau_etude ?? '—'], ['fas fa-money-bill-wave', 'Salaire', $offre->salaire ?? 'Non précisé'], ['fas fa-clock', 'Durée', $offre->duree ?? '—']] as [$icon, $lbl, $val])
                    <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--part-border);font-size:0.83rem;">
                        <span style="color:var(--part-muted);display:flex;align-items:center;gap:0.4rem;"><i class="{{ $icon }}"></i> {{ $lbl }}</span>
                        <span style="font-weight:700;">{{ $val }}</span>
                    </div>
                @endforeach
            </div>

            <div class="part-section-card" style="margin-bottom:1rem;">
                <h3 style="font-family:var(--part-font-head);font-size:0.88rem;font-weight:800;margin-bottom:0.75rem;text-transform:uppercase;letter-spacing:0.3px;">Mon profil</h3>
                @foreach([['Skills', $particulier->competances->count().' compétences'], ['CV', $particulier->cv->count() > 0 ? '✅ Disponible' : '❌ Manquant'], ['Localisation', $particulier->adresse ?? 'Non renseignée'], ['Bio', !empty($particulier->bio) ? '✅' : '❌']] as [$lbl, $val])
                    <div style="display:flex;justify-content:space-between;padding:0.4rem 0;border-bottom:1px solid var(--part-border);font-size:0.82rem;">
                        <span style="color:var(--part-muted);">{{ $lbl }}</span>
                        <span style="font-weight:700;">{{ $val }}</span>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('particulier.matching') }}" class="part-btn part-btn-outline" style="width:100%;justify-content:center;">
                <i class="fas fa-arrow-left"></i> Toutes les offres matchées
            </a>
        </div>

    </div>

@endsection