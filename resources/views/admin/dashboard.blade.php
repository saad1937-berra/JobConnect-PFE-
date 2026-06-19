@extends('layouts.admin')
@section('title', 'Administration')

@section('admin-content')

    <!-- Stats globales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1ecf1;color:#0c5460;"><i class="fas fa-users"></i></div>
            <strong>{{ $stats['total_utilisateurs'] }}</strong>
            <span>Utilisateurs total</span>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#d4edda;color:#155724;"><i class="fas fa-briefcase"></i></div>
            <strong>{{ $stats['offres_actives'] }}</strong>
            <span>Offres actives</span>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff3cd;color:#856404;"><i class="fas fa-paper-plane"></i></div>
            <strong>{{ $stats['total_candidatures'] }}</strong>
            <span>Candidatures</span>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f8d7da;color:#721c24;"><i class="fas fa-user-tie"></i></div>
            <strong>{{ $stats['total_particuliers'] }}</strong>
            <span>Candidats inscrits</span>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e2d9f3;color:#6f42c1;"><i class="fas fa-building"></i></div>
            <strong>{{ $stats['total_entreprises'] }}</strong>
            <span>Entreprises</span>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce4ec;color:#c2185b;"><i class="fas fa-file-alt"></i></div>
            <strong>{{ $stats['total_offres'] }}</strong>
            <span>Total offres</span>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2;color:#b91c1c;"><i class="fas fa-flag"></i></div>
            <strong>{{ $stats['signalements_ouverts'] }}</strong>
            <span>Signalements ouverts</span>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-comments"></i></div>
            <strong>{{ $stats['messages_total'] }}</strong>
            <span>Messages echanges</span>
        </div>
    </div>

    <!-- Candidatures par statut -->
    <div class="table-card">
        <div class="table-header">
            <h3>Candidatures par statut</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Statut</th>
                    <th>Nombre</th>
                    <th>Proportion</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['candidatures_par_statut'] as $row)
                    @php
                        $pct = $stats['total_candidatures'] > 0 ? round($row->total / $stats['total_candidatures'] * 100) : 0;
                        $bc = match($row->statut) { 'acceptee'=>'badge-green','refusee'=>'badge-red','en_cours'=>'badge-blue', default=>'badge-yellow' };
                        $bl = match($row->statut) { 'acceptee'=>'Acceptée','refusee'=>'Refusée','en_cours'=>'En cours', default=>'En attente' };
                    @endphp
                    <tr>
                        <td><span class="badge {{ $bc }}">{{ $bl }}</span></td>
                        <td><strong>{{ $row->total }}</strong></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div style="flex:1;background:var(--paper);border-radius:4px;height:8px;">
                                    <div style="width:{{ $pct }}%;background:var(--accent);height:8px;border-radius:4px;"></div>
                                </div>
                                <span style="font-size:0.82rem;color:var(--muted);width:35px;">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Entreprises récentes -->
    <div class="table-card">
        <div class="table-header">
            <h3>Dernières entreprises inscrites</h3>
            <a href="{{ route('admin.entreprises') }}" class="btn btn-outline btn-sm">Gérer tout</a>
        </div>
        <table>
            <thead>
                <tr><th>Entreprise</th><th>Secteur</th><th>Offres</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($entreprises as $ent)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $ent->nom }}</div>
                            <div style="font-size:0.78rem;color:var(--muted);">{{ $ent->utilisateur->email }}</div>
                        </td>
                        <td>{{ $ent->secteur ?? '—' }}</td>
                        <td>{{ $ent->offres_count }}</td>
                        <td>
                            <div class="actions-cell">
                                <form method="POST" action="{{ route('admin.entreprises.valider', $ent->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline btn-sm" title="Valider">
                                        <i class="fas fa-check" style="color:#28a745;"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.utilisateurs.bloquer', $ent->utilisateur_id) }}" onsubmit="return confirm('Bloquer cet utilisateur ?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Bloquer">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start;">
        <div class="table-card">
            <div class="table-header">
                <h3>Offres par categorie</h3>
            </div>
            <table>
                <thead>
                    <tr><th>Categorie</th><th>Offres</th><th>Volume</th></tr>
                </thead>
                <tbody>
                    @forelse($stats['offres_par_categorie'] as $cat)
                        @php
                            $maxCategories = max(1, $stats['offres_par_categorie']->max('offres_count') ?: 1);
                            $pct = round($cat->offres_count / $maxCategories * 100);
                        @endphp
                        <tr>
                            <td>{{ $cat->nom }}</td>
                            <td><strong>{{ $cat->offres_count }}</strong></td>
                            <td>
                                <div style="background:var(--paper);border-radius:4px;height:8px;">
                                    <div style="width:{{ $pct }}%;background:var(--accent);height:8px;border-radius:4px;"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:1rem;">Aucune categorie.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>Entreprises les plus actives</h3>
            </div>
            <table>
                <thead>
                    <tr><th>Entreprise</th><th>Offres</th></tr>
                </thead>
                <tbody>
                    @forelse($stats['entreprises_actives'] as $entActive)
                        <tr>
                            <td>
                                <div class="td-title">{{ $entActive->nom }}</div>
                                <div class="td-sub">{{ $entActive->utilisateur?->email }}</div>
                            </td>
                            <td><strong>{{ $entActive->offres_count }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="2" style="text-align:center;color:var(--muted);padding:1rem;">Aucune entreprise.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
