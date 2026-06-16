@extends('layouts.admin')
@section('title', 'Gestion Offres')

@section('admin-content')
<div class="container admin-page">
    <div class="page-header">
        <div>
            <h1>Toutes les offres</h1>
            <p style="color:var(--muted);font-size:.9rem;">{{ $offres->total() }} offre(s)</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>

    <form method="GET" action="{{ route('admin.offres') }}" class="filter-bar">
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
                            <div style="display:flex;gap:.4rem;">
                                <a href="{{ route('offres.show', $offre->id) }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.offres.supprimer', $offre->id) }}"
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