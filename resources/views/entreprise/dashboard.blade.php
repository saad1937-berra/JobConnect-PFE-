@extends('layouts.app')
@section('title', 'Dashboard Entreprise')

@push('styles')
<style>
    .dashboard-page { padding: 2.5rem 0; }

    .dashboard-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 2rem;
        align-items: start;
    }

    /* Sidebar */
    .dash-sidebar { position: sticky; top: 84px; }

    .dash-nav {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden;
    }

    .dash-nav-header {
        padding: 1.5rem; background: var(--ink); color: var(--paper);
        text-align: center;
    }

    .dash-nav-header .company-initial {
        width: 100px; height: 100px; border-radius: 12px;
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-head); font-size: 1.4rem; font-weight: 800; color: var(--accent);
        margin: 0 auto 0.75rem; overflow: hidden;
    }
    .dash-nav-header .company-initial img { width: 100%; height: 100%; object-fit: cover; }
    .dash-nav-header h3 { font-family: var(--font-head); font-size: 1rem; font-weight: 700; margin-bottom: 0.2rem; }
    .dash-nav-header span { font-size: 0.78rem; color: #aaa; }

    .dash-nav ul { list-style: none; padding: 0.5rem; }
    .dash-nav ul li a {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.65rem 0.85rem; border-radius: 8px;
        text-decoration: none; color: var(--muted); font-size: 0.9rem;
        transition: all 0.2s;
    }
    .dash-nav ul li a:hover, .dash-nav ul li a.active {
        background: var(--paper); color: var(--ink); font-weight: 500;
    }

    /* Stats cards */
    .stats-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.25rem;
    }

    .stat-card .stat-icon {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; margin-bottom: 0.85rem;
    }

    .stat-card strong {
        display: block; font-family: var(--font-head); font-size: 1.9rem;
        font-weight: 800; letter-spacing: -0.5px; line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-card span { font-size: 0.8rem; color: var(--muted); }

    /* Offres table */
    .table-card {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden; margin-bottom: 1.25rem;
    }

    .table-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border);
    }

    .table-header h3 { font-family: var(--font-head); font-size: 1rem; font-weight: 700; }

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

    .td-title { font-weight: 600; margin-bottom: 0.2rem; }
    .td-sub { font-size: 0.78rem; color: var(--muted); }

    .actions-cell { display: flex; gap: 0.4rem; }

    @media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 900px) { .dashboard-layout { grid-template-columns: 1fr; } .dash-sidebar { position: static; } }
</style>
@endpush

@section('content')
<div class="container dashboard-page">
    <div class="dashboard-layout">

        <!-- Sidebar nav -->
        <aside class="dash-sidebar">
            <div class="dash-nav">
                <div class="dash-nav-header">
                    {{-- Logo cliquable --}}
                    <div style="position:relative;width:100px;height:100px;margin:0 auto 0.75rem;cursor:pointer;"
                        onclick="document.getElementById('logo-input').click()">
                        <div class="company-initial">
                            @if($entreprise->logo)
                                <img src="{{ asset('storage/'.$entreprise->logo) }}" alt="">
                            @else
                                {{ strtoupper(substr($entreprise->nom, 0, 2)) }}
                            @endif
                        </div>
                        <div style="position:absolute;inset:0;border-radius:12px;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;color:white;font-size:0.9rem;"
                            onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>

                    {{-- Form upload caché --}}
                    <form method="POST" action="{{ route('entreprise.logo.upload') }}"
                        enctype="multipart/form-data" id="logo-form">
                        @csrf
                        <input type="file" id="logo-input" name="logo"
                            accept="image/jpeg,image/png,image/webp"
                            style="display:none;"
                            onchange="document.getElementById('logo-form').submit()">
                    </form>

                    <h3>{{ $entreprise->nom }}</h3>
                    <span>{{ $entreprise->secteur ?? 'Entreprise' }}</span>
                </div>
                <ul>
                    <li><a href="{{ route('entreprise.dashboard') }}" class="active"><i class="fas fa-chart-bar"></i> Dashboard</a></li>
                    <li><a href="{{ route('entreprise.offres') }}"><i class="fas fa-briefcase"></i> Mes offres</a></li>
                    <li><a href="{{ route('entreprise.offres.creer') }}"><i class="fas fa-plus-circle"></i> Publier une offre</a></li>
                    <li><a href="{{ route('entreprise.candidatures') }}"><i class="fas fa-users"></i> Candidatures</a></li>
                    <li><a href="{{ route('entreprise.profil') }}"><i class="fas fa-building"></i> Profil entreprise</a></li>
                    <li><a href="{{ route('notifications.index') }}"><i class="fas fa-bell"></i> Notifications</a></li>
                </ul>
            </div>
        </aside>

        <!-- Contenu -->
        <div>
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fff3cd;color:#856404;"><i class="fas fa-briefcase"></i></div>
                    <strong>{{ $stats['total_offres'] }}</strong>
                    <span>Total offres</span>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#d4edda;color:#155724;"><i class="fas fa-check-circle"></i></div>
                    <strong>{{ $stats['offres_actives'] }}</strong>
                    <span>Offres actives</span>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#d1ecf1;color:#0c5460;"><i class="fas fa-paper-plane"></i></div>
                    <strong>{{ $stats['total_candidatures'] }}</strong>
                    <span>Candidatures reçues</span>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f8d7da;color:#721c24;"><i class="fas fa-eye"></i></div>
                    <strong>{{ $stats['candidatures_recentes']->count() }}</strong>
                    <span>Nouvelles (7j)</span>
                </div>
            </div>

            <!-- Offres récentes -->
            <div class="table-card">
                <div class="table-header">
                    <h3>Mes offres récentes</h3>
                    <a href="{{ route('entreprise.offres.creer') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nouvelle offre
                    </a>
                </div>
                <table>
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
                                    <div class="td-title">{{ $offre->titre }}</div>
                                    <div class="td-sub">{{ $offre->localisation }} • {{ $offre->date_publication->format('d/m/Y') }}</div>
                                </td>
                                <td><span class="badge badge-blue">{{ $offre->contrat ?? '—' }}</span></td>
                                <td>
                                    <strong>{{ $offre->candidatures_count }}</strong>
                                    <span style="color:var(--muted);font-size:0.8rem;"> candidats</span>
                                </td>
                                <td>
                                    @if($offre->statut === 'active')
                                        <span class="badge badge-green">Active</span>
                                    @elseif($offre->statut === 'expiree')
                                        <span class="badge badge-red">Expirée</span>
                                    @else
                                        <span class="badge badge-gray">Brouillon</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="{{ route('entreprise.offres.edit', $offre->id) }}" class="btn btn-outline btn-sm"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="{{ route('entreprise.offres.supprimer', $offre->id) }}" onsubmit="return confirm('Supprimer cette offre ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:2rem;">Aucune offre publiée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Candidatures récentes -->
            <div class="table-card">
                <div class="table-header">
                    <h3>Candidatures récentes</h3>
                    <a href="{{ route('entreprise.candidatures') }}" class="btn btn-outline btn-sm">Voir tout</a>
                </div>
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
                        @forelse($stats['candidatures_recentes'] as $cand)
                            <tr>
                                <td>
                                    <div class="td-title">{{ $cand->particulier->utilisateur->prenom }} {{ $cand->particulier->utilisateur->nom }}</div>
                                    <div class="td-sub">{{ $cand->particulier->utilisateur->email }}</div>
                                </td>
                                <td>{{ $cand->offre->titre }}</td>
                                <td>{{ $cand->date->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $bc = match($cand->statut) { 'acceptee'=>'badge-green','refusee'=>'badge-red','en_cours'=>'badge-blue', default=>'badge-yellow' };
                                        $bl = match($cand->statut) { 'acceptee'=>'Acceptée','refusee'=>'Refusée','en_cours'=>'En cours', default=>'En attente' };
                                    @endphp
                                    <span class="badge {{ $bc }}">{{ $bl }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('entreprise.candidature.show', $cand->id) }}" class="btn btn-outline btn-sm">Voir</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:2rem;">Aucune candidature récente.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
