@extends('layouts.admin')
@section('title', 'Gestion Entreprises')

@section('admin-content')
<div class="container admin-page">
    <div class="page-header">
        <div>
            <h1>Entreprises</h1>
            <p style="color:var(--muted);font-size:.9rem;">{{ $entreprises->total() }} entreprise(s)</p>
        </div>
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