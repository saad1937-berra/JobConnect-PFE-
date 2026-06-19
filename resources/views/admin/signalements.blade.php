@extends('layouts.admin')
@section('title', 'Signalements')

@section('admin-content')
<div class="container admin-page">
    <div class="page-header">
        <div>
            <h1>Signalements</h1>
            <p style="color:var(--muted);font-size:.9rem;">{{ $reports->total() }} signalement(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.signalements') }}" class="filter-bar">
        <select name="status">
            <option value="">Tous les statuts</option>
            @foreach(['nouveau' => 'Nouveau', 'en_cours' => 'En cours', 'traite' => 'Traite', 'rejete' => 'Rejete'] as $value => $label)
                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Filtrer</button>
        @if(request('status'))
            <a href="{{ route('admin.signalements') }}" class="btn btn-outline btn-sm">Reinitialiser</a>
        @endif
    </form>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Signalement</th>
                    <th>Entreprise</th>
                    <th>Utilisateur signale</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    @php
                        $badge = match($report->status) {
                            'traite' => 'badge-green',
                            'rejete' => 'badge-red',
                            'en_cours' => 'badge-blue',
                            default => 'badge-yellow',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="td-title">#{{ $report->id }} - Conversation #{{ $report->conversation_id }}</div>
                            <div class="td-sub">{{ $report->reason ?: 'Aucun motif detaille.' }}</div>
                            @if($report->admin_note)
                                <div class="td-sub"><strong>Note admin :</strong> {{ $report->admin_note }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="td-title">{{ $report->reporter?->prenom }} {{ $report->reporter?->nom }}</div>
                            <div class="td-sub">{{ $report->reporter?->email }}</div>
                        </td>
                        <td>
                            <div class="td-title">{{ $report->reported?->prenom }} {{ $report->reported?->nom }}</div>
                            <div class="td-sub">{{ $report->reported?->email }}</div>
                        </td>
                        <td><span class="badge {{ $badge }}">{{ str_replace('_', ' ', ucfirst($report->status)) }}</span></td>
                        <td>
                            <div class="actions-cell" style="align-items:flex-start;">
                                <a href="{{ route('messages.show', $report->conversation_id) }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <form method="POST" action="{{ route('admin.signalements.update', $report->id) }}" style="display:grid;gap:.45rem;min-width:220px;">
                                    @csrf @method('PATCH')
                                    <select name="status">
                                        @foreach(['nouveau' => 'Nouveau', 'en_cours' => 'En cours', 'traite' => 'Traite', 'rejete' => 'Rejete'] as $value => $label)
                                            <option value="{{ $value }}" {{ $report->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <textarea name="admin_note" rows="2" placeholder="Note admin...">{{ $report->admin_note }}</textarea>
                                    <button type="submit" class="btn btn-outline btn-sm">Mettre a jour</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:2rem;color:var(--muted);">Aucun signalement.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.5rem;display:flex;justify-content:center;">{{ $reports->withQueryString()->links() }}</div>
</div>
@endsection
