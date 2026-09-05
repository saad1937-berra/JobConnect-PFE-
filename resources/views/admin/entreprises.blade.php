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
        <select name="statut_validation">
            <option value="">Tous les statuts</option>
            <option value="en_attente" {{ request('statut_validation') === 'en_attente' ? 'selected' : '' }}>En attente</option>
            <option value="validee" {{ request('statut_validation') === 'validee' ? 'selected' : '' }}>Validee</option>
            <option value="refusee" {{ request('statut_validation') === 'refusee' ? 'selected' : '' }}>Refusee</option>
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Rechercher</button>
        @if(request()->anyFilled(['search', 'statut_validation']))
            <a href="{{ route('admin.entreprises') }}" class="btn btn-outline btn-sm">Réinitialiser</a>
        @endif
    </form>

    <div class="table-card">
        <table>
            <thead>
                <tr><th>Entreprise</th><th>Secteur</th><th>Offres</th><th>Email</th><th>Validation</th><th>Actions</th></tr>
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
                        <td>
                            <div>{{ $ent->utilisateur->email }}</div>
                            @if($ent->utilisateur->hasVerifiedEmail())
                                <span class="badge badge-green">Email verifie</span>
                            @else
                                <span class="badge badge-red">Email non verifie</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClass = $ent->isValidee() ? 'badge-green' : ($ent->isRefusee() ? 'badge-red' : 'badge-yellow');
                                $statusLabel = $ent->isValidee() ? 'Validee' : ($ent->isRefusee() ? 'Refusee' : 'En attente');
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            <div class="actions-cell">
                                @if(!$ent->isValidee() && $ent->utilisateur->hasVerifiedEmail())
                                    <form method="POST" action="{{ route('admin.entreprises.valider', $ent->id) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-outline btn-sm" title="Valider">
                                            <i class="fas fa-check" style="color:#28a745;"></i> Valider
                                        </button>
                                    </form>
                                @elseif(!$ent->isValidee())
                                    <span class="badge badge-gray">Email requis</span>
                                @endif
                                @if(!$ent->isRefusee())
                                    <form method="POST" action="{{ route('admin.entreprises.refuser', $ent->id) }}"
                                          onsubmit="return confirm('Refuser cette entreprise ?')" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-outline btn-sm" title="Refuser">
                                            <i class="fas fa-times" style="color:#dc2626;"></i> Refuser
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.utilisateurs.bloquer', $ent->utilisateur_id) }}"
                                      onsubmit="return confirm('Bloquer cette entreprise ?')" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-ban"></i> Bloquer
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('messages.start') }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $ent->utilisateur_id }}">
                                    <input type="hidden" name="body" value="Avertissement administratif : merci de respecter les regles de communication professionnelle de JobConnect.">
                                    <button type="submit" class="btn btn-outline btn-sm">
                                        <i class="fas fa-exclamation-triangle"></i> Avertir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--muted);">Aucune entreprise.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.5rem;display:flex;justify-content:center;">{{ $entreprises->withQueryString()->links() }}</div>
</div>
@endsection
