@extends('layouts.app')
@section('title', 'Gestion Offres')

@push('styles')
<style>
    .admin-page { padding: 2.5rem 0; }
    .page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem; }
    .page-header h1 { font-family:var(--font-head);font-size:2rem;font-weight:800;letter-spacing:-0.5px; }
    .filter-bar { display:flex;gap:.75rem;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap; }
    .filter-bar select,.filter-bar input { padding:.5rem .85rem;border:1.5px solid var(--border);border-radius:8px;font-family:var(--font-body);font-size:.88rem;background:white;outline:none; }
    .table-card { background:white;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden; }
    table { width:100%;border-collapse:collapse; }
    thead th { padding:.75rem 1.5rem;text-align:left;font-size:.78rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;background:var(--paper);border-bottom:1px solid var(--border); }
    tbody td { padding:1rem 1.5rem;border-bottom:1px solid var(--border);font-size:.9rem;vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover { background:#fafafa; }
    .td-title { font-weight:600;margin-bottom:.15rem; }
    .td-sub   { font-size:.78rem;color:var(--muted); }
</style>
@endpush

@section('content')
<div class="container admin-page">
    <div class="page-header">
        <div>
            <h1>Toutes les offres</h1>
            <p style="color:var(--muted);font-size:.9rem;">{{ $offres->total() }} offre(s)</p>
        </div>
        <a href="{{ route('entreprise.dashboard') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>

    <form method="GET" action="{{ route('entreprise.offres') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un titre...">
        <select name="statut">
            <option value="">Tous les statuts</option>
            <option value="active"    {{ request('statut')=='active'    ?'selected':'' }}>Active</option>
            <option value="brouillon" {{ request('statut')=='brouillon' ?'selected':'' }}>Brouillon</option>
            <option value="expiree"   {{ request('statut')=='expiree'   ?'selected':'' }}>Expirée</option>
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Filtrer</button>
        @if(request()->anyFilled(['search','statut']))
            <a href="{{ route('admin.offres') }}" class="btn btn-outline btn-sm">Réinitialiser</a>
        @endif
    </form>

    <div class="table-card">
        <table>
            <thead>
                <tr><th>Poste</th><th>Entreprise</th><th>Catégorie</th><th>Statut</th><th>Publiée le</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($offres as $offre)
                    <tr>
                        <td>
                            <div class="td-title">{{ $offre->titre }}</div>
                            <div class="td-sub">{{ $offre->localisation ?? '—' }} • {{ $offre->contrat ?? '—' }}</div>
                        </td>
                        <td>{{ $offre->entreprise->nom }}</td>
                        <td>{{ $offre->categorie->nom ?? '—' }}</td>
                        <td>
                            @if($offre->statut === 'active')
                                <span class="badge badge-green">Active</span>
                            @elseif($offre->statut === 'expiree')
                                <span class="badge badge-red">Expirée</span>
                            @else
                                <span class="badge badge-gray">Brouillon</span>
                            @endif
                        </td>
                        <td>{{ $offre->date_publication->format('d/m/Y') }}</td>
                        <td>
                            <div style="display:flex;gap:0.4rem;">
                                <a href="{{ route('offres.show', $offre->id) }}" class="btn btn-outline btn-sm" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('entreprise.offres.edit', $offre->id) }}" class="btn btn-secondary btn-sm" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('entreprise.offres.supprimer', $offre->id) }}"
                                
                                      onsubmit="return confirm('Supprimer cette offre ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--muted);">Aucune offre.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.5rem;display:flex;justify-content:center;">{{ $offres->withQueryString()->links() }}</div>
</div>
@endsection
