@extends('layouts.app')
@section('title', 'Compétences')

@push('styles')
<style>
    .admin-page { padding: 2.5rem 0; }
    .page-layout { display:grid;grid-template-columns:1fr 360px;gap:2rem;align-items:start; }
    .page-header { margin-bottom:2rem; }
    .page-header h1 { font-family:var(--font-head);font-size:2rem;font-weight:800;letter-spacing:-0.5px; }
    .table-card { background:white;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden; }
    .table-header { display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid var(--border); }
    .table-header h3 { font-family:var(--font-head);font-size:1rem;font-weight:700; }
    table { width:100%;border-collapse:collapse; }
    thead th { padding:.75rem 1.5rem;text-align:left;font-size:.78rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;background:var(--paper);border-bottom:1px solid var(--border); }
    tbody td { padding:1rem 1.5rem;border-bottom:1px solid var(--border);font-size:.9rem;vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover { background:#fafafa; }
    .form-card { background:white;border:1px solid var(--border);border-radius:var(--radius);padding:1.75rem;position:sticky;top:84px; }
    .form-card h3 { font-family:var(--font-head);font-size:1rem;font-weight:700;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid var(--border); }
    .actions-cell { display:flex;gap:.4rem; }
    @media(max-width:900px){ .page-layout{grid-template-columns:1fr;} .form-card{position:static;} }
</style>
@endpush

@section('content')
<div class="container admin-page">
    <div class="page-header">
        <h1>Compétences</h1>
    </div>

    <div class="page-layout">
        <!-- Liste -->
        <div class="table-card">
            <div class="table-header">
                <h3>{{ $competances->count() }} compétence(s)</h3>
            </div>
            <table>
                <thead>
                    <tr><th>Nom</th><th>Description</th><th>Candidats</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($competances as $comp)
                        <tr>
                            <td>
                                <span style="background:var(--ink);color:var(--paper);padding:.2rem .7rem;border-radius:20px;font-size:.82rem;font-weight:600;">
                                    {{ $comp->nom }}
                                </span>
                            </td>
                            <td style="color:var(--muted);max-width:200px;">{{ Str::limit($comp->description, 60) ?? '—' }}</td>
                            <td><span class="badge badge-green">{{ $comp->particuliers_count }}</span></td>
                            <td>
                                <div class="actions-cell">
                                    <button onclick="fillEditComp({{ $comp->id }}, '{{ addslashes($comp->nom) }}', '{{ addslashes($comp->description ?? '') }}')"
                                            class="btn btn-outline btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.competances.supprimer', $comp->id) }}"
                                          onsubmit="return confirm('Supprimer cette compétence ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--muted);">Aucune compétence.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Formulaire -->
        <div class="form-card">
            <h3 id="form-title-comp"><i class="fas fa-plus" style="color:var(--accent);margin-right:.5rem;"></i> Nouvelle compétence</h3>
            <form method="POST" id="comp-form" action="{{ route('admin.competances.store') }}">
                @csrf
                <input type="hidden" name="_method" id="comp-method" value="POST">
                <div class="form-group">
                    <label>Nom <span style="color:var(--accent)">*</span></label>
                    <input type="text" name="nom" id="comp-nom" class="form-control @error('nom') is-invalid @enderror"
                           value="{{ old('nom') }}" placeholder="Ex: PHP, Laravel, React..." required>
                    @error('nom')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="comp-desc" class="form-control" rows="3"
                              placeholder="Description de la compétence...">{{ old('description') }}</textarea>
                </div>
                <div style="display:flex;gap:.75rem;">
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Enregistrer</button>
                    <button type="button" onclick="resetCompForm()" class="btn btn-outline">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fillEditComp(id, nom, desc) {
        document.getElementById('comp-nom').value = nom;
        document.getElementById('comp-desc').value = desc;
        document.getElementById('comp-method').value = 'PUT';
        document.getElementById('comp-form').action = `/admin/competances/${id}`;
        document.getElementById('form-title-comp').innerHTML = '<i class="fas fa-edit" style="color:var(--accent);margin-right:.5rem;"></i> Modifier la compétence';
        window.scrollTo({ top: document.getElementById('comp-form').getBoundingClientRect().top + window.scrollY - 100, behavior: 'smooth' });
    }
    function resetCompForm() {
        document.getElementById('comp-nom').value = '';
        document.getElementById('comp-desc').value = '';
        document.getElementById('comp-method').value = 'POST';
        document.getElementById('comp-form').action = '{{ route('admin.competances.store') }}';
        document.getElementById('form-title-comp').innerHTML = '<i class="fas fa-plus" style="color:var(--accent);margin-right:.5rem;"></i> Nouvelle compétence';
    }
</script>
@endpush
@endsection
