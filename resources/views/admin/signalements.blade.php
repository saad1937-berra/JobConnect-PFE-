@extends('layouts.admin')
@section('title', 'Signalements')

@push('styles')
<style>
    .reports-page { width:100%; min-width:0; padding:0; }
    .reports-page .filter-bar { display:flex; flex-wrap:wrap; align-items:center; gap:.65rem; padding:1rem; background:#fff; border:1px solid var(--border, #e4e2da); border-radius:14px; margin-bottom:1.25rem; }
    .reports-page select, .reports-page textarea { box-sizing:border-box; width:100%; border:1px solid #d9dfd9; border-radius:9px; background:#fff; color:#243b30; padding:.7rem .8rem; font:inherit; font-size:.85rem; line-height:1.5; transition:border-color .15s, box-shadow .15s; }
    .reports-page .filter-bar select { width:auto; min-width:190px; }
    .reports-page select:focus, .reports-page textarea:focus { outline:2px solid #287451; outline-offset:2px; border-color:#287451; box-shadow:0 0 0 4px #28745112; }
    .reports-page textarea { resize:vertical; min-height:100px; }
    .reports-page textarea::placeholder { color:#839087; }
    .reports-page .table-card { border-radius:16px; border:1px solid var(--border, #e4e2da); box-shadow:0 5px 22px #203b2b06; overflow-x:auto; }
    .reports-page table { width:100%; }
    .reports-page th { padding:1rem; font-size:.7rem; letter-spacing:.04em; }
    .reports-page td { padding:1.2rem 1rem; vertical-align:top; }
    .reports-page .td-title { line-height:1.5; }
    .reports-page .td-sub { margin-top:.35rem; line-height:1.6; overflow-wrap:anywhere; }
    .reports-page .report-reference { display:inline-block; color:#246044; background:#edf5ef; padding:.2rem .55rem; border-radius:6px; font-weight:700; font-size:.78rem; margin-bottom:.45rem; }
    .reports-page .report-reason { margin-top:.7rem; white-space:pre-wrap; color:#4e5d54; }
    .reports-page .badge { white-space:nowrap; padding:.4rem .7rem; }
    .reports-page .report-actions { display:grid; gap:.75rem; min-width:245px; }
    .reports-page .report-view { justify-self:start; }
    .reports-page .report-form { display:grid; gap:.5rem; padding:1rem; background:#f7f9f6; border:1px solid #e3e9e1; border-radius:12px; }
    .reports-page .report-form label { font-size:.78rem; font-weight:700; color:#344d3e; }
    .reports-page .report-form label:not(:first-of-type) { margin-top:.35rem; }
    .reports-page .report-form small { font-size:.72rem; color:#68776d; line-height:1.6; }
    .reports-page .btn { border-radius:8px; min-height:36px; }
    .reports-page .report-save { display:flex; align-items:center; justify-content:center; gap:.5rem; margin-top:.4rem; padding:.75rem 1rem; background:#22543d; border:1px solid #22543d; color:#fff; font:inherit; font-size:.82rem; font-weight:700; cursor:pointer; border-radius:9px; transition:background .15s; }
    .reports-page .report-save:hover { background:#163e2b; }
    .reports-page .report-save:focus-visible { outline:2px solid #287451; outline-offset:3px; }
    @media(max-width:1100px) { .reports-page table { min-width:780px; } }
    @media(max-width:600px) { .reports-page .filter-bar select { width:100%; } }
</style>
@endpush

@section('admin-content')
<div class="reports-page">
    @if($errors->any())
        <div role="alert" style="padding:1rem;margin-bottom:1rem;background:#fef2f2;color:#b91c1c;border-radius:8px;">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif
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
                            <span class="report-reference">Signalement #{{ $report->id }}</span>
                            <div class="td-title">Conversation #{{ $report->conversation_id }}</div>
                            <div class="td-sub report-reason">{{ $report->reason ?: 'Aucun motif detaille.' }}</div>
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
                            <div class="report-actions">
                                <a href="{{ route('messages.show', $report->conversation_id) }}" class="btn btn-outline btn-sm report-view">
                                    <i class="fas fa-eye" aria-hidden="true"></i> Voir la conversation
                                </a>
                                <form method="POST" action="{{ route('admin.signalements.update', $report->id) }}" class="report-form">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="report_id" value="{{ $report->id }}">
                                    <label for="status-{{ $report->id }}">Statut</label>
                                    <select id="status-{{ $report->id }}" name="status">
                                        @foreach(['nouveau' => 'Nouveau', 'en_cours' => 'En cours', 'traite' => 'Traite', 'rejete' => 'Rejete'] as $value => $label)
                                            <option value="{{ $value }}" @selected((old('report_id') == $report->id ? old('status', $report->status) : $report->status) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <label for="treatment-{{ $report->id }}">Traitement / justification</label>
                                    <textarea id="treatment-{{ $report->id }}" name="admin_note" rows="3" maxlength="2000" placeholder="Précisez les mesures prises ou le motif du rejet…">{{ old('report_id') == $report->id ? old('admin_note', $report->admin_note) : $report->admin_note }}</textarea>
                                    <small>Obligatoire pour un signalement traité ou rejeté. Ce texte sera envoyé à son auteur.</small>
                                    <button type="submit" class="report-save"><i class="fas fa-check" aria-hidden="true"></i> Mettre à jour</button>
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
