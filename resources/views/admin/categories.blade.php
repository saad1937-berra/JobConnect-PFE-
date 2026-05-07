@extends('layouts.app')
@section('title', 'Catégories')

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
        <h1>Catégories</h1>
    </div>

    <div class="page-layout">
        <!-- Liste -->
        <div class="table-card">
            <div class="table-header">
                <h3>{{ $categories->count() }} catégorie(s)</h3>
            </div>
            <table>
                <thead>
                    <tr><th>Nom</th><th>Description</th><th>Offres</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td><strong>{{ $cat->nom }}</strong></td>
                            <td style="color:var(--muted);max-width:200px;">{{ Str::limit($cat->description, 60) ?? '—' }}</td>
                            <td><span class="badge badge-blue">{{ $cat->offres_count }}</span></td>
                            <td>
                                <div class="actions-cell">
                                    <button onclick="fillEdit('cat', {{ $cat->id }}, '{{ addslashes($cat->nom) }}', '{{ addslashes($cat->description ?? '') }}')"
                                            class="btn btn-outline btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.categories.supprimer', $cat->id) }}"
                                          onsubmit="return confirm('Supprimer cette catégorie ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--muted);">Aucune catégorie.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Formulaire -->
        <div class="form-card">
            <h3 id="form-title-cat"><i class="fas fa-plus" style="color:var(--accent);margin-right:.5rem;"></i> Nouvelle catégorie</h3>
            <form method="POST" id="cat-form" action="{{ route('admin.categories.store') }}">
                @csrf
                <input type="hidden" name="_method" id="cat-method" value="POST">
                <div class="form-group">
                    <label>Nom <span style="color:var(--accent)">*</span></label>
                    <input type="text" name="nom" id="cat-nom" class="form-control @error('nom') is-invalid @enderror"
                           value="{{ old('nom') }}" placeholder="Ex: Informatique" required>
                    @error('nom')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="cat-desc" class="form-control" rows="3"
                              placeholder="Description de la catégorie...">{{ old('description') }}</textarea>
                </div>
                <div style="display:flex;gap:.75rem;">
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Enregistrer</button>
                    <button type="button" onclick="resetCatForm()" class="btn btn-outline">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fillEdit(type, id, nom, desc) {
        document.getElementById('cat-nom').value = nom;
        document.getElementById('cat-desc').value = desc;
        document.getElementById('cat-method').value = 'PUT';
        document.getElementById('cat-form').action = `/admin/categories/${id}`;
        document.getElementById('form-title-cat').innerHTML = '<i class="fas fa-edit" style="color:var(--accent);margin-right:.5rem;"></i> Modifier la catégorie';
        window.scrollTo({ top: document.getElementById('cat-form').getBoundingClientRect().top + window.scrollY - 100, behavior: 'smooth' });
    }
    function resetCatForm() {
        document.getElementById('cat-nom').value = '';
        document.getElementById('cat-desc').value = '';
        document.getElementById('cat-method').value = 'POST';
        document.getElementById('cat-form').action = '{{ route('admin.categories.store') }}';
        document.getElementById('form-title-cat').innerHTML = '<i class="fas fa-plus" style="color:var(--accent);margin-right:.5rem;"></i> Nouvelle catégorie';
    }
</script>
@endpush
@endsection
