@extends('layouts.app')
@section('title', 'Gestion Entreprises')

@push('styles')
<style>
    .admin-page { padding: 2.5rem 0; }
    .page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem; }
    .page-header h1 { font-family:var(--font-head);font-size:2rem;font-weight:800;letter-spacing:-0.5px; }
    .filter-bar { display:flex;gap:.75rem;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap; }
    .filter-bar input { padding:.5rem .85rem;border:1.5px solid var(--border);border-radius:8px;font-family:var(--font-body);font-size:.88rem;background:white;outline:none;min-width:220px; }
    .table-card { background:white;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden; }
    table { width:100%;border-collapse:collapse; }
    thead th { padding:.75rem 1.5rem;text-align:left;font-size:.78rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;background:var(--paper);border-bottom:1px solid var(--border); }
    tbody td { padding:1rem 1.5rem;border-bottom:1px solid var(--border);font-size:.9rem;vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover { background:#fafafa; }
    .td-title { font-weight:600;margin-bottom:.15rem; }
    .td-sub   { font-size:.78rem;color:var(--muted); }
    .company-initial { width:40px;height:40px;border-radius:8px;background:var(--paper);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-weight:800;color:var(--accent);font-size:.95rem;flex-shrink:0;overflow:hidden; }
    .company-initial img { width:100%;height:100%;object-fit:cover; }
    .company-row { display:flex;align-items:center;gap:.75rem; }
    .actions-cell { display:flex;gap:.4rem; }
</style>
@endpush

@section('content')
<div class="container admin-page">
    <div class="page-header">
        <div>
            <h1>Entreprises</h1>
            <p style="color:var(--muted);font-size:.9rem;">{{ $entreprises->total() }} entreprise(s)</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>

    <form method="GET" action="{{ route('admin.entreprises') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une entreprise...">
        <button type="submit" class="btn btn-outline btn-sm">Rechercher</button>
        @if(request('search'))
            <a href="{{ route('admin.entreprises') }}" class="btn btn-outline btn-sm">Réinitialiser</a>
        @endif
    </form>

    <div class="table-card">
        <table>
            <thead>
                <tr><th>Entreprise</th><th>Secteur</th><th>Offres</th><th>Email</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($entreprises as $ent)
                    <tr>
                        <td>
                            <div class="company-row">
                                <div class="company-initial">
                                    @if($ent->logo)
                                        <img src="{{ asset('storage/'.$ent->logo) }}" alt="">
                                    @else
                                        {{ strtoupper(substr($ent->nom, 0, 2)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="td-title">{{ $ent->nom }}</div>
                                    <div class="td-sub">{{ $ent->adresse ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $ent->secteur ?? '—' }}</td>
                        <td><strong>{{ $ent->offres_count }}</strong></td>
                        <td>{{ $ent->utilisateur->email }}</td>
                        <td>
                            <div class="actions-cell">
                                <form method="POST" action="{{ route('admin.entreprises.valider', $ent->id) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline btn-sm" title="Valider">
                                        <i class="fas fa-check" style="color:#28a745;"></i> Valider
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.utilisateurs.bloquer', $ent->utilisateur_id) }}"
                                      onsubmit="return confirm('Bloquer cette entreprise ?')" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-ban"></i> Bloquer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--muted);">Aucune entreprise.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.5rem;display:flex;justify-content:center;">{{ $entreprises->withQueryString()->links() }}</div>
</div>
@endsection
