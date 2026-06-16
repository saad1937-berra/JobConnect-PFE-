@extends('layouts.entreprise')
@section('title', isset($offre) ? 'Modifier l\'offre' : 'Publier une offre')

@section('ent-content')

    <div class="ent-page-header">
        <div>
            <h1>{{ isset($offre) ? 'Modifier l\'offre' : 'Publier une offre' }}</h1>
            <p>{{ isset($offre) ? 'Mettez à jour les détails de votre offre.' : 'Remplissez le formulaire pour publier votre offre d\'emploi.' }}</p>
        </div>
        <a href="{{ route('entreprise.offres') }}" class="ent-btn ent-btn-outline">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 280px;gap:1.5rem;align-items:start;">

        <!-- Formulaire -->
        <div>
            <form method="POST"
                  action="{{ isset($offre) ? route('entreprise.offres.update', $offre->id) : route('entreprise.offres.store') }}">
                @csrf
                @if(isset($offre)) @method('PUT') @endif

                <!-- Informations principales -->
                <div class="ent-section-card">
                    <div class="ent-section-card-header">
                        <h3><i class="fas fa-info-circle"></i> Informations principales</h3>
                    </div>

                    <div class="ent-form-group">
                        <label>Titre du poste <span style="color:#dc2626">*</span></label>
                        <input type="text" name="titre" class="ent-form-control @error('titre') is-invalid @enderror"
                               value="{{ old('titre', $offre->titre ?? '') }}"
                               placeholder="Ex: Développeur Full-Stack Senior" required>
                        @error('titre')<p class="ent-form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="ent-form-row">
                        <div class="ent-form-group">
                            <label>Catégorie <span style="color:#dc2626">*</span></label>
                            <select name="categorie_id" class="ent-form-control" required>
                                <option value="">-- Choisir --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('categorie_id', $offre->categorie_id ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categorie_id')<p class="ent-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="ent-form-group">
                            <label>Type de contrat</label>
                            <select name="contrat" class="ent-form-control">
                                <option value="">-- Choisir --</option>
                                @foreach(['CDI', 'CDD', 'Stage', 'Alternance', 'Freelance', 'Intérim'] as $type)
                                    <option value="{{ $type }}"
                                        {{ old('contrat', $offre->contrat ?? '') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="ent-form-group">
                        <label>Description du poste <span style="color:#dc2626">*</span></label>
                        <textarea name="description" class="ent-form-control" rows="6" required
                                  placeholder="Décrivez le poste, les missions, les responsabilités...">{{ old('description', $offre->description ?? '') }}</textarea>
                        @error('description')<p class="ent-form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Conditions du poste -->
                <div class="ent-section-card">
                    <div class="ent-section-card-header">
                        <h3><i class="fas fa-list-ul"></i> Conditions du poste</h3>
                    </div>

                    <div class="ent-form-row">
                        <div class="ent-form-group">
                            <label>Localisation</label>
                            <input type="text" name="localisation" class="ent-form-control"
                                   value="{{ old('localisation', $offre->localisation ?? '') }}" placeholder="Casablanca">
                        </div>
                        <div class="ent-form-group">
                            <label>Salaire</label>
                            <input type="text" name="salaire" class="ent-form-control"
                                   value="{{ old('salaire', $offre->salaire ?? '') }}" placeholder="5 000 – 8 000 MAD">
                        </div>
                        <div class="ent-form-group">
                            <label>Niveau d'études</label>
                            <select name="niveau_etude" class="ent-form-control">
                                <option value="">-- Choisir --</option>
                                @foreach(['Bac', 'Bac+2', 'Bac+3', 'Bac+4', 'Bac+5', 'Doctorat'] as $niv)
                                    <option value="{{ $niv }}"
                                        {{ old('niveau_etude', $offre->niveau_etude ?? '') == $niv ? 'selected' : '' }}>
                                        {{ $niv }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ent-form-group">
                            <label>Durée</label>
                            <input type="text" name="duree" class="ent-form-control"
                                   value="{{ old('duree', $offre->duree ?? '') }}" placeholder="Ex: 6 mois">
                        </div>
                        <div class="ent-form-group">
                            <label>Date d'expiration</label>
                            <input type="date" name="date_expiration" class="ent-form-control"
                                   value="{{ old('date_expiration', isset($offre->date_expiration) ? $offre->date_expiration->format('Y-m-d') : '') }}">
                        </div>
                        <div class="ent-form-group">
                            <label>Statut</label>
                            <select name="statut" class="ent-form-control">
                                <option value="active"    {{ old('statut', $offre->statut ?? 'active') === 'active'    ? 'selected' : '' }}>Active</option>
                                <option value="brouillon" {{ old('statut', $offre->statut ?? '')        === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                <option value="expiree"   {{ old('statut', $offre->statut ?? '')        === 'expiree'   ? 'selected' : '' }}>Expirée</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Compétences requises -->
                <div class="ent-section-card">
                    <div class="ent-section-card-header">
                        <h3><i class="fas fa-star"></i> Compétences requises</h3>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:0.5rem;padding:1rem;background:var(--ent-bg);border-radius:8px;border:1px solid var(--ent-border);">
                        @foreach($competances as $comp)
                            <label style="display:inline-flex;align-items:center;gap:0.4rem;cursor:pointer;background:white;border:1.5px solid var(--ent-border);padding:0.3rem 0.75rem;border-radius:20px;font-size:0.83rem;transition:all 0.15s;">
                                <input type="checkbox" name="competances[]" value="{{ $comp->id }}"
                                       style="cursor:pointer;accent-color:var(--ent-green);"
                                       {{ isset($offre) && $offre->competances->contains($comp->id) ? 'checked' : '' }}
                                       onchange="toggleCompLabel(this)">
                                {{ $comp->nom }}
                            </label>
                        @endforeach
                    </div>
                    @error('competances')<p class="ent-form-error">{{ $message }}</p>@enderror
                </div>

                <div style="display:flex;gap:1rem;">
                    <button type="submit" class="ent-btn ent-btn-primary" style="padding:0.65rem 2rem;font-size:0.95rem;">
                        <i class="fas fa-{{ isset($offre) ? 'save' : 'paper-plane' }}"></i>
                        {{ isset($offre) ? 'Enregistrer les modifications' : 'Publier l\'offre' }}
                    </button>
                    <a href="{{ route('entreprise.offres') }}" class="ent-btn ent-btn-outline">Annuler</a>
                </div>
            </form>
        </div>

        <!-- Tips -->
        <div class="ent-tips-card" style="position:sticky;top:calc(var(--ent-topbar-h) + 1rem);">
            <h4>💡 Conseils</h4>
            <div class="ent-tip-item">
                <span class="icon">📝</span>
                <p><strong>Titre clair</strong>Utilisez l'intitulé exact du poste.</p>
            </div>
            <div class="ent-tip-item">
                <span class="icon">📋</span>
                <p><strong>Description complète</strong>Listez missions, compétences et avantages.</p>
            </div>
            <div class="ent-tip-item">
                <span class="icon">💰</span>
                <p><strong>Salaire visible</strong>+40% de candidatures avec salaire affiché.</p>
            </div>
            <div class="ent-tip-item">
                <span class="icon">⭐</span>
                <p><strong>Compétences</strong>Cochez les compétences pour activer le matching.</p>
            </div>
        </div>

    </div>

@push('scripts')
<script>
    // Mettre en évidence les compétences déjà cochées au chargement
    document.querySelectorAll('input[name="competances[]"]:checked').forEach(input => {
        const label = input.closest('label');
        label.style.borderColor = 'var(--ent-green)';
        label.style.background  = 'var(--ent-green-pale)';
        label.style.color       = 'var(--ent-green)';
    });

    function toggleCompLabel(input) {
        const label = input.closest('label');
        if (input.checked) {
            label.style.borderColor = 'var(--ent-green)';
            label.style.background  = 'var(--ent-green-pale)';
            label.style.color       = 'var(--ent-green)';
        } else {
            label.style.borderColor = 'var(--ent-border)';
            label.style.background  = 'white';
            label.style.color       = 'inherit';
        }
    }
</script>
@endpush

@endsection