@extends('layouts.app')
@section('title', 'Candidatures reçues')

@push('styles')
<style>
    .cand-page { padding: 2.5rem 0; }

    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem;
    }
    .page-header h1 { font-family: var(--font-head); font-size: 2rem; font-weight: 800; letter-spacing:-0.5px; }
    .page-header p   { color: var(--muted); margin-top: 0.2rem; font-size: 0.9rem; }

    .filter-bar {
        display: flex; gap: 0.75rem; align-items: center;
        margin-bottom: 1.5rem; flex-wrap: wrap;
    }
    .filter-bar select, .filter-bar input {
        padding: 0.5rem 0.85rem;
        border: 1.5px solid var(--border); border-radius: 8px;
        font-family: var(--font-body); font-size: 0.88rem;
        background: white; color: var(--ink); outline: none;
    }

    .table-card { background: white; border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }

    table { width: 100%; border-collapse: collapse; }
    thead th {
        padding: 0.75rem 1.5rem; text-align: left;
        font-size: 0.78rem; font-weight: 600; color: var(--muted);
        text-transform: uppercase; letter-spacing: 0.4px;
        background: var(--paper); border-bottom: 1px solid var(--border);
    }
    tbody td {
        padding: 1rem 1.5rem; border-bottom: 1px solid var(--border);
        font-size: 0.9rem; vertical-align: middle;
    }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafafa; }

    .td-title { font-weight: 600; margin-bottom: 0.15rem; }
    .td-sub   { font-size: 0.78rem; color: var(--muted); }

    .candidate-info { display: flex; align-items: center; gap: 0.75rem; }
    .candidate-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: var(--ink); color: var(--paper);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-head); font-size: 0.75rem; font-weight: 800;
        flex-shrink: 0;
    }

    .actions-cell { display: flex; gap: 0.4rem; align-items: center; }

    .empty-state { text-align: center; padding: 4rem 2rem; color: var(--muted); }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; }
</style>
@endpush

@section('content')
<div class="container cand-page">

    <div class="page-header">
        <div>
            <h1>Candidatures reçues</h1>
            <p>{{ $candidatures->total() }} candidature(s) au total</p>
        </div>
        <a href="{{ route('entreprise.dashboard') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <!-- Filtres -->
    <form method="GET" action="{{ route('entreprise.candidatures') }}" class="filter-bar">
        <select name="statut">
            <option value="">Tous les statuts</option>
            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
            <option value="en_cours"   {{ request('statut') == 'en_cours'   ? 'selected' : '' }}>En cours</option>
            <option value="acceptee"   {{ request('statut') == 'acceptee'   ? 'selected' : '' }}>Acceptée</option>
            <option value="refusee"    {{ request('statut') == 'refusee'    ? 'selected' : '' }}>Refusée</option>
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Filtrer</button>
        @if(request()->anyFilled(['statut']))
            <a href="{{ route('entreprise.candidatures') }}" class="btn btn-outline btn-sm">Réinitialiser</a>
        @endif
    </form>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Candidat</th>
                    <th>Poste</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($candidatures as $cand)
                    @php
                        $initiales = strtoupper(
                            substr($cand->particulier->utilisateur->prenom, 0, 1) .
                            substr($cand->particulier->utilisateur->nom, 0, 1)
                        );
                        $bc = match($cand->statut) {
                            'acceptee'   => 'badge-green',
                            'refusee'    => 'badge-red',
                            'en_cours'   => 'badge-blue',
                            default      => 'badge-yellow',
                        };
                        $bl = match($cand->statut) {
                            'acceptee'   => 'Acceptée',
                            'refusee'    => 'Refusée',
                            'en_cours'   => 'En cours',
                            default      => 'En attente',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="candidate-info">
                                <div class="candidate-avatar">{{ $initiales }}</div>
                                <div>
                                    <div class="td-title">
                                        {{ $cand->particulier->utilisateur->prenom }}
                                        {{ $cand->particulier->utilisateur->nom }}
                                    </div>
                                    <div class="td-sub">{{ $cand->particulier->utilisateur->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="td-title">{{ $cand->offre->titre }}</div>
                            <div class="td-sub">{{ $cand->offre->contrat ?? '—' }}</div>
                        </td>
                        <td>{{ $cand->date->format('d/m/Y') }}</td>
                        <td><span class="badge {{ $bc }}">{{ $bl }}</span></td>
                        <td>
                            <div class="actions-cell">
                                <a href="{{ route('entreprise.candidature.show', $cand->id) }}"
                                   class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <a href="{{ route('entreprise.candidature.cv', $cand->id) }}"
                                   class="btn btn-secondary btn-sm" title="Télécharger CV">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <p style="font-size:1rem;font-weight:500;">Aucune candidature reçue</p>
                                <p style="font-size:0.88rem;margin-top:0.4rem;">
                                    Publiez des offres pour commencer à recevoir des candidatures.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.5rem;display:flex;justify-content:center;">
        {{ $candidatures->withQueryString()->links() }}
    </div>
</div>
@endsection
