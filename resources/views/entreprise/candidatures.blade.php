@extends('layouts.entreprise')
@section('title', 'Candidatures reçues')

@section('ent-content')

    <div class="ent-page-header">
        <div>
            <h1>Candidatures reçues</h1>
            <p>{{ $candidatures->total() }} candidature(s) au total</p>
        </div>
    </div>

    <form method="GET" action="{{ route('entreprise.candidatures') }}" class="ent-filter-bar">
        <select name="statut">
            <option value="">Tous les statuts</option>
            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
            <option value="en_cours"   {{ request('statut') == 'en_cours'   ? 'selected' : '' }}>En cours</option>
            <option value="acceptee"   {{ request('statut') == 'acceptee'   ? 'selected' : '' }}>Acceptée</option>
            <option value="refusee"    {{ request('statut') == 'refusee'    ? 'selected' : '' }}>Refusée</option>
        </select>
        <button type="submit" class="ent-btn ent-btn-outline ent-btn-sm">Filtrer</button>
        @if(request()->anyFilled(['statut']))
            <a href="{{ route('entreprise.candidatures') }}" class="ent-btn ent-btn-outline ent-btn-sm">Réinitialiser</a>
        @endif
    </form>

    <div class="ent-card">
        <table class="ent-table">
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
                        $initiales = strtoupper(substr($cand->particulier->utilisateur->prenom,0,1).substr($cand->particulier->utilisateur->nom,0,1));
                        $bc = match($cand->statut) { 'acceptee'=>'green','refusee'=>'red','en_cours'=>'blue', default=>'yellow' };
                        $bl = match($cand->statut) { 'acceptee'=>'Acceptée','refusee'=>'Refusée','en_cours'=>'En cours', default=>'En attente' };
                    @endphp
                    <tr>
                        <td>
                            <div class="ent-candidate-info">
                                <div class="ent-candidate-avatar">{{ $initiales }}</div>
                                <div>
                                    <div class="ent-td-title">{{ $cand->particulier->utilisateur->prenom }} {{ $cand->particulier->utilisateur->nom }}</div>
                                    <div class="ent-td-sub">{{ $cand->particulier->utilisateur->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="ent-td-title">{{ $cand->offre->titre }}</div>
                            <div class="ent-td-sub">{{ $cand->offre->contrat ?? '—' }}</div>
                        </td>
                        <td>{{ $cand->date->format('d/m/Y') }}</td>
                        <td><span class="ent-badge ent-badge-{{ $bc }}">{{ $bl }}</span></td>
                        <td>
                            <div class="ent-actions">
                                <a href="{{ route('entreprise.candidature.show', $cand->id) }}" class="ent-btn ent-btn-outline ent-btn-sm">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <a href="{{ route('entreprise.candidature.cv', $cand->id) }}" class="ent-btn ent-btn-secondary ent-btn-sm" title="Télécharger CV">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:3rem;color:var(--ent-muted);">
                            <i class="fas fa-users" style="font-size:2rem;margin-bottom:0.75rem;display:block;"></i>
                            Aucune candidature reçue.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="display:flex;justify-content:center;margin-top:1.5rem;">
        {{ $candidatures->withQueryString()->links() }}
    </div>

@endsection