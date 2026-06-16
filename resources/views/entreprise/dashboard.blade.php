@extends('layouts.entreprise')
@section('title', 'Dashboard')

@section('ent-content')

    <div class="ent-page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Bienvenue, {{ $entreprise->nom }}</p>
        </div>
        <a href="{{ route('entreprise.offres.creer') }}" class="ent-btn ent-btn-primary">
            <i class="fas fa-plus"></i> Nouvelle offre
        </a>
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
            <strong>{{ $stats['candidatures_recentes']->count() }}</strong>
            <span>Nouvelles (7j)</span>
        </div>
    </div>

    <!-- Offres récentes -->
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
                                <a href="{{ route('entreprise.offres.edit', $offre->id) }}" class="ent-btn ent-btn-outline ent-btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('entreprise.offres.suggestions', $offre->id) }}" class="ent-btn ent-btn-outline ent-btn-sm" title="Suggestions"><i class="fas fa-magic"></i></a>
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