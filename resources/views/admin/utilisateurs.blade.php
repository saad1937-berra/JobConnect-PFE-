@extends('layouts.admin')
@section('title', 'Gestion Utilisateurs')

@section('admin-content')
<div class="container admin-page">
    <div class="page-header">
        <div>
            <h1>Utilisateurs</h1>
            <p style="color:var(--muted);font-size:.9rem;">{{ $utilisateurs->total() }} utilisateur(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.utilisateurs') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email...">
        <select name="role">
            <option value="">Tous les rôles</option>
            <option value="particulier" {{ request('role') == 'particulier' ? 'selected' : '' }}>Candidat</option>
            <option value="entreprise" {{ request('role') == 'entreprise' ? 'selected' : '' }}>Entreprise</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="bloque" {{ request('role') == 'bloque' ? 'selected' : '' }}>Bloqué</option>
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Filtrer</button>
        @if (request()->anyFilled(['search', 'role']))
            <a href="{{ route('admin.utilisateurs') }}" class="btn btn-outline btn-sm">Réinitialiser</a>
        @endif
    </form>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Utilisateur</th>
                    <th>Rôle</th>
                    <th>Inscrit le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($utilisateurs as $u)
                    <tr>
                        <td>
                            @if ($u->isParticulier() && $u->particulier?->photo)
                                <img src="{{ asset('storage/' . $u->particulier->photo) }}" class="user-avatar" alt="">
                            @elseif($u->isEntreprise() && $u->entreprise?->logo)
                                <img src="{{ asset('storage/' . $u->entreprise->logo) }}" class="user-avatar-square" alt="">
                            @else
                                <div class="user-avatar-fallback">
                                    {{ strtoupper(substr($u->prenom, 0, 1) . substr($u->nom, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="td-title">{{ $u->prenom }} {{ $u->nom }}</div>
                            <div class="td-sub">{{ $u->email }}</div>
                        </td>
                        <td>
                            @php
                                $rc = match ($u->role) {
                                    'admin' => 'badge-blue',
                                    'entreprise' => 'badge-green',
                                    'bloque' => 'badge-red',
                                    default => 'badge-yellow',
                                };
                            @endphp
                            <span class="badge {{ $rc }}">{{ ucfirst($u->role) }}</span>
                        </td>
                        <td>{{ $u->date_inscription->format('d/m/Y') }}</td>
                        <td>
                            @if ($u->role !== 'bloque' && $u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.utilisateurs.bloquer', $u->id) }}"
                                    onsubmit="return confirm('Bloquer cet utilisateur ?')" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-ban"></i> Bloquer</button>
                                </form>
                            @else
                                <span style="color:var(--muted);font-size:.82rem;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:2rem;color:var(--muted);">Aucun utilisateur.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.5rem;display:flex;justify-content:center;">{{ $utilisateurs->withQueryString()->links() }}</div>
</div>
@endsection