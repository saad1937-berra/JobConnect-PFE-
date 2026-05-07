{{-- ════════════════════════════════════════ --}}
{{-- resources/views/admin/utilisateurs.blade.php --}}
{{-- ════════════════════════════════════════ --}}
@extends('layouts.app')
@section('title', 'Gestion Utilisateurs')

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
            <h1>Utilisateurs</h1>
            <p style="color:var(--muted);font-size:.9rem;">{{ $utilisateurs->total() }} utilisateur(s)</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>

    <form method="GET" action="{{ route('admin.utilisateurs') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email...">
        <select name="role">
            <option value="">Tous les rôles</option>
            <option value="particulier" {{ request('role')=='particulier'?'selected':'' }}>Candidat</option>
            <option value="entreprise"  {{ request('role')=='entreprise' ?'selected':'' }}>Entreprise</option>
            <option value="admin"       {{ request('role')=='admin'      ?'selected':'' }}>Admin</option>
            <option value="bloque"      {{ request('role')=='bloque'     ?'selected':'' }}>Bloqué</option>
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Filtrer</button>
        @if(request()->anyFilled(['search','role']))
            <a href="{{ route('admin.utilisateurs') }}" class="btn btn-outline btn-sm">Réinitialiser</a>
        @endif
    </form>

    <div class="table-card">
        <table>
            <thead>
                <tr><th>Utilisateur</th><th>Rôle</th><th>Inscrit le</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($utilisateurs as $u)
                    <tr>
                        <td>
                            <div class="td-title">{{ $u->prenom }} {{ $u->nom }}</div>
                            <div class="td-sub">{{ $u->email }}</div>
                        </td>
                        <td>
                            @php $rc = match($u->role){ 'admin'=>'badge-blue','entreprise'=>'badge-green','bloque'=>'badge-red', default=>'badge-yellow' }; @endphp
                            <span class="badge {{ $rc }}">{{ ucfirst($u->role) }}</span>
                        </td>
                        <td>{{ $u->date_inscription->format('d/m/Y') }}</td>
                        <td>
                            @if($u->role !== 'bloque' && $u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.utilisateurs.bloquer', $u->id) }}" onsubmit="return confirm('Bloquer cet utilisateur ?')" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-ban"></i> Bloquer</button>
                                </form>
                            @else
                                <span style="color:var(--muted);font-size:.82rem;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--muted);">Aucun utilisateur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.5rem;display:flex;justify-content:center;">{{ $utilisateurs->withQueryString()->links() }}</div>
</div>
@endsection
