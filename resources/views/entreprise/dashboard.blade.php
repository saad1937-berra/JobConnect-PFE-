@extends('layouts.entreprise')
@section('title', 'Dashboard')

@section('ent-content')

    <div class="ent-page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Bienvenue, {{ $entreprise->nom }}</p>
        </div>
        @if($entreprise->peutPublier())
            <a href="{{ route('entreprise.offres.creer') }}" class="ent-btn ent-btn-primary">
                <i class="fas fa-plus"></i> Nouvelle offre
            </a>
        @endif
    </div>

    <!-- Stats -->
    <div class="ent-stats-grid">
        <div class="ent-stat-card">
            <div class="ent-stat-icon" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-briefcase"></i></div>
            <strong>{{ $stats['total_offres'] }}</strong>
            <span>Offres publiées</span>
        </div>
        <div class="ent-stat-card">
            <div class="ent-stat-icon" style="background:#ecfdf5;color:#059669;"><i class="fas fa-check-circle"></i></div>
            <strong>{{ $stats['offres_actives'] }}</strong>
            <span>Offres actives</span>
        </div>
        <div class="ent-stat-card">
            <div class="ent-stat-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-users"></i></div>
            <strong>{{ $stats['total_candidatures'] }}</strong>
            <span>Candidatures reçues</span>
        </div>
        <div class="ent-stat-card">
            <div class="ent-stat-icon" style="background:#fefce8;color:#ca8a04;"><i class="fas fa-clock"></i></div>
            <strong>{{ $stats['nouvelles_7j'] }}</strong>
            <span>Nouvelles (7j)</span>
        </div>
    </div>

    <!-- Offres récentes -->
    @php
        $offreCounts = $stats['offres_par_statut']->pluck('total', 'statut');
        $offreTotal = max(1, $stats['total_offres']);
        $activePct = round(($offreCounts['active'] ?? 0) / $offreTotal * 100);
        $draftPct = round(($offreCounts['brouillon'] ?? 0) / $offreTotal * 100);
        $expiredPct = max(0, 100 - $activePct - $draftPct);
        $o1 = round($activePct * 3.6);
        $o2 = round(($activePct + $draftPct) * 3.6);
        $candRows = collect([
            'en_attente' => ['label' => 'En attente', 'color' => '#ca8a04'],
            'en_cours' => ['label' => 'En cours', 'color' => '#2563eb'],
            'acceptee' => ['label' => 'Acceptees', 'color' => '#16a34a'],
            'refusee' => ['label' => 'Refusees', 'color' => '#dc2626'],
        ])->map(function ($meta, $status) use ($stats) {
            $row = $stats['candidatures_par_statut']->firstWhere('statut', $status);
            return [...$meta, 'total' => $row->total ?? 0];
        });
        $maxCand = max(1, $candRows->max('total'));
        $maxPopular = max(1, $stats['offres_populaires']->max('candidatures_count') ?: 1);
    @endphp

    <div class="ent-visual-grid">
        <section class="ent-chart-card">
            <div class="ent-chart-head">
                <h3>Repartition des offres</h3>
                <span>{{ $stats['total_offres'] }} offres</span>
            </div>
            <div class="ent-donut-wrap">
                <div class="ent-donut" style="--o1: {{ $o1 }}deg; --o2: {{ $o2 }}deg;">
                    <div>
                        <strong>{{ $stats['total_offres'] }}</strong>
                        <span>Total</span>
                    </div>
                </div>
                <div class="ent-chart-legend">
                    <span><i style="background:#16a34a;"></i> Actives {{ $activePct }}%</span>
                    <span><i style="background:#f59e0b;"></i> Brouillons {{ $draftPct }}%</span>
                    <span><i style="background:#dc2626;"></i> Expirees {{ $expiredPct }}%</span>
                </div>
            </div>
        </section>

        <section class="ent-chart-card">
            <div class="ent-chart-head">
                <h3>Candidatures par statut</h3>
                <span>{{ $stats['total_candidatures'] }} total</span>
            </div>
            <div class="ent-column-chart">
                @foreach($candRows as $row)
                    @php $height = round($row['total'] / $maxCand * 100); @endphp
                    <div class="ent-column-item">
                        <div class="ent-column-track">
                            <span style="height:{{ max(4, $height) }}%;background:{{ $row['color'] }};"></span>
                        </div>
                        <strong>{{ $row['total'] }}</strong>
                        <em>{{ $row['label'] }}</em>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="ent-chart-card">
            <div class="ent-chart-head">
                <h3>Offres les plus attractives</h3>
                <span>candidatures</span>
            </div>
            <div class="ent-horizontal-chart">
                @forelse($stats['offres_populaires'] as $offrePopulaire)
                    @php $pct = round($offrePopulaire->candidatures_count / $maxPopular * 100); @endphp
                    <div class="ent-horizontal-row">
                        <div>
                            <strong>{{ $offrePopulaire->titre }}</strong>
                            <span>{{ $offrePopulaire->candidatures_count }} candidature(s)</span>
                        </div>
                        <div class="ent-horizontal-track"><span style="width:{{ $pct }}%;"></span></div>
                    </div>
                @empty
                    <p style="color:var(--ent-muted);font-size:.9rem;">Aucune offre publiee.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="ent-card">
        <div class="ent-card-header">
            <h3><i class="fas fa-briefcase"></i> Mes offres récentes</h3>
            <a href="{{ route('entreprise.offres') }}" class="ent-btn ent-btn-outline ent-btn-sm">Voir tout</a>
        </div>
        <table class="ent-table">
            <thead>
                <tr>
                    <th>Poste</th>
                    <th>Contrat</th>
                    <th>Candidatures</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($offres as $offre)
                    <tr>
                        <td>
                            <div class="ent-td-title">{{ $offre->titre }}</div>
                            <div class="ent-td-sub">{{ $offre->localisation }} • {{ $offre->date_publication->format('d/m/Y') }}</div>
                        </td>
                        <td><span class="ent-badge ent-badge-blue">{{ $offre->contrat ?? '—' }}</span></td>
                        <td><strong>{{ $offre->candidatures_count }}</strong></td>
                        <td>
                            @if($offre->statut === 'active')
                                <span class="ent-badge ent-badge-green">Active</span>
                            @elseif($offre->statut === 'expiree')
                                <span class="ent-badge ent-badge-red">Expirée</span>
                            @else
                                <span class="ent-badge ent-badge-gray">Brouillon</span>
                            @endif
                        </td>
                        <td>
                            <div class="ent-actions">
                                @if($entreprise->peutPublier())
                                    <a href="{{ route('entreprise.offres.edit', $offre->id) }}" class="ent-btn ent-btn-outline ent-btn-sm"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('entreprise.offres.suggestions', $offre->id) }}" class="ent-btn ent-btn-outline ent-btn-sm" title="Suggestions"><i class="fas fa-magic"></i></a>
                                    <a href="{{ route('entreprise.offres.matching', $offre->id) }}" class="ent-btn ent-btn-outline ent-btn-sm" title="Matching"><i class="fas fa-chart-line"></i></a>
                                @endif
                                <form method="POST" action="{{ route('entreprise.offres.supprimer', $offre->id) }}" onsubmit="return confirm('Supprimer ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ent-btn ent-btn-danger ent-btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--ent-muted);">Aucune offre publiée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Candidatures récentes -->
    <div class="ent-card">
        <div class="ent-card-header">
            <h3><i class="fas fa-users"></i> Candidatures récentes</h3>
            <a href="{{ route('entreprise.candidatures') }}" class="ent-btn ent-btn-outline ent-btn-sm">Voir tout</a>
        </div>
        <table class="ent-table">
            <thead>
                <tr>
                    <th>Candidat</th>
                    <th>Poste</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['candidatures_recentes'] as $cand)
                    @php
                        $bc = match($cand->statut) { 'acceptee'=>'green','refusee'=>'red','en_cours'=>'blue', default=>'yellow' };
                        $bl = match($cand->statut) { 'acceptee'=>'Acceptée','refusee'=>'Refusée','en_cours'=>'En cours', default=>'En attente' };
                        $initiales = strtoupper(substr($cand->particulier->utilisateur->prenom,0,1).substr($cand->particulier->utilisateur->nom,0,1));
                    @endphp
                    <tr>
                        <td>
                            <div class="ent-candidate-info">
                                <div class="ent-candidate-avatar">{{ $initiales }}</div>
                                <div>
                                    <div class="ent-td-title">{{ $cand->particulier->utilisateur->prenom }} {{ $cand->particulier->utilisateur->nom }}</div>
                                    <div class="ent-td-sub">{{ $cand->particulier->utilisateur->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $cand->offre->titre }}</td>
                        <td>{{ $cand->date->format('d/m/Y') }}</td>
                        <td><span class="ent-badge ent-badge-{{ $bc }}">{{ $bl }}</span></td>
                        <td>
                            <a href="{{ route('entreprise.candidature.show', $cand->id) }}" class="ent-btn ent-btn-outline ent-btn-sm">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--ent-muted);">Aucune candidature récente.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
