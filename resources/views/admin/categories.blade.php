@extends('layouts.admin')
@section('title', 'Catégories')

@section('admin-content')
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