@extends('layouts.entreprise')
@section('title', 'Mes offres')

@section('ent-content')

    <div class="ent-page-header">
        <div>
            <h1>Mes offres</h1>
            <p>{{ $offres->total() }} offre(s)</p>
        </div>
        <a href="{{ route('entreprise.offres.creer') }}" class="ent-btn ent-btn-primary">
            <i class="fas fa-plus"></i> Nouvelle offre
        </a>
    </div>

    <form method="GET" action="{{ route('entreprise.offres') }}" class="ent-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un titre...">
        <select name="statut">
            <option value="">Tous les statuts</option>
            <option value="active"    {{ request('statut')=='active'    ?'selected':'' }}>Active</option>
            <option value="brouillon" {{ request('statut')=='brouillon' ?'selected':'' }}>Brouillon</option>
            <option value="expiree"   {{ request('statut')=='expiree'   ?'selected':'' }}>Expirée</option>
        </select>
        <button type="submit" class="ent-btn ent-btn-outline ent-btn-sm">Filtrer</button>
        @if(request()->anyFilled(['search','statut']))
            <a href="{{ route('entreprise.offres') }}" class="ent-btn ent-btn-outline ent-btn-sm">Réinitialiser</a>
        @endif
    </form>

    <div class="ent-card">
        <table class="ent-table">
            <thead>
                <tr><th>Poste</th><th>Catégorie</th><th>Candidatures</th><th>Statut</th><th>Publiée le</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($offres as $offre)
                    <tr>
                        <td>
                            <div class="ent-td-title">{{ $offre->titre }}</div>
                            <div class="ent-td-sub">{{ $offre->localisation ?? '—' }} • {{ $offre->contrat ?? '—' }}</div>
                        </td>
                        <td>{{ $offre->categorie->nom ?? '—' }}</td>
                        <td><strong>{{ $offre->candidatures_count }}</strong></td>
                        <td>
                            @if($offre->statut === 'active')       <span class="ent-badge ent-badge-green">Active</span>
                            @elseif($offre->statut === 'expiree')  <span class="ent-badge ent-badge-red">Expirée</span>
                            @else                                   <span class="ent-badge ent-badge-gray">Brouillon</span>
                            @endif
                        </td>
                        <td>{{ $offre->date_publication->format('d/m/Y') }}</td>
                        <td>
                            <div class="ent-actions">
                                <a href="{{ route('offres.show', $offre->id) }}" class="ent-btn ent-btn-outline ent-btn-sm" title="Voir"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('entreprise.offres.edit', $offre->id) }}" class="ent-btn ent-btn-outline ent-btn-sm" title="Modifier"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('entreprise.offres.suggestions', $offre->id) }}" class="ent-btn ent-btn-outline ent-btn-sm" title="Suggestions"><i class="fas fa-magic"></i></a>
                                <form method="POST" action="{{ route('entreprise.offres.supprimer', $offre->id) }}" onsubmit="return confirm('Supprimer ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ent-btn ent-btn-danger ent-btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--ent-muted);">Aucune offre.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="display:flex;justify-content:center;margin-top:1.5rem;">
        {{ $offres->withQueryString()->links() }}
    </div>

@endsection