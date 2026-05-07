@extends('layouts.app')
@section('title', 'Administration')

@push('styles')
<style>
    .admin-page { padding: 2.5rem 0; }

    .admin-layout { display: grid; grid-template-columns: 240px 1fr; gap: 2rem; align-items: start; }

    .admin-nav {
        background: var(--ink); border-radius: var(--radius); overflow: hidden;
        position: sticky; top: 84px;
    }

    .admin-nav-header { padding: 1.5rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
    .admin-nav-header h3 { font-family: var(--font-head); font-weight: 800; color: var(--paper); font-size: 1rem; }
    .admin-nav-header span { font-size: 0.78rem; color: #888; }

    .admin-nav ul { list-style: none; padding: 0.5rem; }
    .admin-nav ul li a {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.65rem 0.85rem; border-radius: 8px;
        text-decoration: none; color: #888; font-size: 0.9rem;
        transition: all 0.2s;
    }
    .admin-nav ul li a:hover, .admin-nav ul li a.active {
        background: rgba(255,255,255,0.08); color: var(--paper);
    }

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }

    .stat-card { background: white; border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; }
    .stat-card .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; margin-bottom: 1rem; }
    .stat-card strong { display: block; font-family: var(--font-head); font-size: 2.2rem; font-weight: 800; letter-spacing:-1px; line-height:1; margin-bottom:0.25rem; }
    .stat-card span { font-size: 0.82rem; color: var(--muted); }

    .table-card { background: white; border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; margin-bottom: 1.25rem; }
    .table-header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
    .table-header h3 { font-family: var(--font-head); font-size: 1rem; font-weight: 700; }

    table { width: 100%; border-collapse: collapse; }
    thead th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.78rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.4px; background: var(--paper); border-bottom: 1px solid var(--border); }
    tbody td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); font-size: 0.9rem; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafafa; }

    .actions-cell { display: flex; gap: 0.4rem; }

    @media (max-width: 900px) { .admin-layout { grid-template-columns: 1fr; } .admin-nav { position: static; } .stats-grid { grid-template-columns: repeat(2,1fr); } }
</style>
@endpush

@section('content')
<div class="container admin-page">
    <div class="admin-layout">

        <!-- Nav -->
        <aside>
            <div class="admin-nav">
                <div class="admin-nav-header">
                    <h3>🛡 Administration</h3>
                    <span>{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</span>
                </div>
                <ul>
                    <li><a href="{{ route('admin.dashboard') }}" class="active"><i class="fas fa-chart-pie"></i> Statistiques</a></li>
                    <li><a href="{{ route('admin.entreprises') }}"><i class="fas fa-building"></i> Entreprises</a></li>
                    <li><a href="{{ route('admin.utilisateurs') }}"><i class="fas fa-users"></i> Utilisateurs</a></li>
                    <li><a href="{{ route('admin.offres') }}"><i class="fas fa-briefcase"></i> Offres</a></li>
                    <li><a href="{{ route('admin.categories') }}"><i class="fas fa-tags"></i> Catégories</a></li>
                    <li><a href="{{ route('admin.competances') }}"><i class="fas fa-star"></i> Compétences</a></li>
                </ul>
            </div>
        </aside>

        <!-- Contenu -->
        <div>
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
        </div>
    </div>
</div>
@endsection
