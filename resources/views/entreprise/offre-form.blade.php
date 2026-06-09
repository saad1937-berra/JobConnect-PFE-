@extends('layouts.app')
@section('title', isset($offre) ? 'Modifier l\'offre' : 'Publier une offre')

@push('styles')
    <style>
        .form-page {
            padding: 2.5rem 0;
        }

        .form-page-header {
            margin-bottom: 2rem;
        }

        .form-page-header h1 {
            font-family: var(--font-head);
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .form-page-header p {
            color: var(--muted);
            margin-top: 0.25rem;
        }

        .form-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 2rem;
            align-items: start;
        }

        .form-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 2rem;
        }

        .form-card h3 {
            font-family: var(--font-head);
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 140px;
        }

        .tips-card {
            background: var(--ink);
            color: var(--paper);
            border-radius: var(--radius);
            padding: 1.5rem;
            position: sticky;
            top: 84px;
        }

        .tips-card h4 {
            font-family: var(--font-head);
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--paper);
        }

        .tip-item {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .tip-item .tip-icon {
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .tip-item p {
            font-size: 0.83rem;
            color: #aaa;
            line-height: 1.5;
        }

        .tip-item p strong {
            color: var(--paper);
            display: block;
            margin-bottom: 0.15rem;
        }

        @media (max-width: 900px) {
            .form-layout {
                grid-template-columns: 1fr;
            }

            .tips-card {
                position: static;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container form-page">
        <div class="form-page-header">
            <h1>{{ isset($offre) ? 'Modifier l\'offre' : 'Publier une offre' }}</h1>
            <p>{{ isset($offre) ? 'Mettez à jour les détails de votre offre.' : 'Remplissez le formulaire pour publier votre offre d\'emploi.' }}
            </p>
        </div>

        <div class="form-layout">
            <div>
                <form method="POST"
                    action="{{ isset($offre) ? route('entreprise.offres.update', $offre->id) : route('entreprise.offres.store') }}">
                    @csrf
                    @if (isset($offre))
                        @method('PUT')
                    @endif

                    <!-- Informations principales -->
                    <div class="form-card" style="margin-bottom:1.25rem;">
                        <h3><i class="fas fa-info-circle" style="color:var(--accent);margin-right:0.5rem;"></i> Informations
                            principales</h3>

                        <div class="form-group">
                            <label>Titre du poste <span style="color:var(--accent)">*</span></label>
                            <input type="text" name="titre" class="form-control @error('titre') is-invalid @enderror"
                                value="{{ old('titre', $offre->titre ?? '') }}"
                                placeholder="Ex: Développeur Full-Stack Senior" required>
                            @error('titre')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Catégorie <span style="color:var(--accent)">*</span></label>
                                <select name="categorie_id" class="form-control @error('categorie_id') is-invalid @enderror"
                                    required>
                                    <option value="">-- Choisir --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('categorie_id', $offre->categorie_id ?? '') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categorie_id')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Type de contrat</label>
                                <select name="contrat" class="form-control">
                                    <option value="">-- Choisir --</option>
                                    @foreach (['CDI', 'CDD', 'Stage', 'Alternance', 'Freelance', 'Intérim'] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('contrat', $offre->contrat ?? '') == $type ? 'selected' : '' }}>
                                            {{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description du poste <span style="color:var(--accent)">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" required
                                placeholder="Décrivez le poste, les missions, les responsabilités...">{{ old('description', $offre->description ?? '') }}</textarea>
                            @error('description')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Conditions -->
                    <div class="form-card" style="margin-bottom:1.25rem;">
                        <h3><i class="fas fa-list-ul" style="color:var(--accent);margin-right:0.5rem;"></i> Conditions du
                            poste</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Localisation</label>
                                <input type="text" name="localisation" class="form-control"
                                    value="{{ old('localisation', $offre->localisation ?? '') }}" placeholder="Casablanca">
                            </div>
                            <div class="form-group">
                                <label>Salaire</label>
                                <input type="text" name="salaire" class="form-control"
                                    value="{{ old('salaire', $offre->salaire ?? '') }}" placeholder="5 000 – 8 000 MAD">
                            </div>
                            <div class="form-group">
                                <label>Niveau d'études</label>
                                <select name="niveau_etude" class="form-control">
                                    <option value="">-- Choisir --</option>
                                    @foreach (['Bac', 'Bac+2', 'Bac+3', 'Bac+5', 'Doctorat', 'Sans diplôme'] as $niv)
                                        <option value="{{ $niv }}"
                                            {{ old('niveau_etude', $offre->niveau_etude ?? '') == $niv ? 'selected' : '' }}>
                                            {{ $niv }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Durée</label>
                                <input type="text" name="duree" class="form-control"
                                    value="{{ old('duree', $offre->duree ?? '') }}" placeholder="Ex: 6 mois">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Date d'expiration</label>
                                <input type="date" name="date_expiration" class="form-control"
                                    value="{{ old('date_expiration', isset($offre->date_expiration) ? $offre->date_expiration->format('Y-m-d') : '') }}">
                            </div>
                            <div class="form-group">
                                <label>Statut</label>
                                <select name="statut" class="form-control">
                                    <option value="active"
                                        {{ old('statut', $offre->statut ?? 'active') === 'active' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="brouillon"
                                        {{ old('statut', $offre->statut ?? '') === 'brouillon' ? 'selected' : '' }}>
                                        Brouillon</option>
                                    <option value="expiree"
                                        {{ old('statut', $offre->statut ?? '') === 'expiree' ? 'selected' : '' }}>Expirée
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- Compétences requises --}}
                    <div class="form-card" style="margin-bottom:1.25rem;">
                        <h3><i class="fas fa-star" style="color:var(--accent);margin-right:0.5rem;"></i> Compétences
                            requises</h3>

                        <div class="form-group">
                            <label>Sélectionnez les compétences requises pour ce poste</label>
                            <div
                                style="display:flex;flex-wrap:wrap;gap:0.5rem;padding:1rem;background:var(--paper);border-radius:8px;border:1.5px solid var(--border);">
                                @foreach ($competances as $comp)
                                    <label
                                        style="display:inline-flex;align-items:center;gap:0.4rem;cursor:pointer;background:white;border:1.5px solid var(--border);padding:0.3rem 0.75rem;border-radius:20px;font-size:0.85rem;transition:all 0.2s;"
                                        onmouseover="this.style.borderColor='var(--accent)'"
                                        onmouseout="this.style.borderColor= this.querySelector('input').checked ? 'var(--accent)' : 'var(--border)'"
                                        id="label_comp_{{ $comp->id }}">
                                        <input type="checkbox" name="competances[]" value="{{ $comp->id }}"
                                            style="cursor:pointer;"
                                            {{ isset($offre) && $offre->competances->contains($comp->id) ? 'checked' : '' }}
                                            onchange="toggleLabel(this)">
                                        {{ $comp->nom }}
                                    </label>
                                @endforeach
                            </div>
                            @error('competances')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div style="display:flex;gap:1rem;">
                        <button type="submit" class="btn btn-primary" style="padding:0.75rem 2rem;font-size:1rem;">
                            <i class="fas fa-{{ isset($offre) ? 'save' : 'paper-plane' }}"></i>
                            {{ isset($offre) ? 'Enregistrer les modifications' : 'Publier l\'offre' }}
                        </button>
                        <a href="{{ route('entreprise.offres') }}" class="btn btn-outline">Annuler</a>
                    </div>
                </form>
            </div>

            <!-- Tips -->
            <div class="tips-card">
                <h4>💡 Conseils pour une bonne offre</h4>
                <div class="tip-item">
                    <span class="tip-icon">📝</span>
                    <p><strong>Titre clair</strong>Utilisez le vrai intitulé du poste, évitez les mots vagues.</p>
                </div>
                <div class="tip-item">
                    <span class="tip-icon">📋</span>
                    <p><strong>Description complète</strong>Listez les missions, les compétences requises et les avantages
                        offerts.</p>
                </div>
                <div class="tip-item">
                    <span class="tip-icon">💰</span>
                    <p><strong>Salaire visible</strong>Les offres avec salaire reçoivent 40% plus de candidatures.</p>
                </div>
                <div class="tip-item">
                    <span class="tip-icon">📅</span>
                    <p><strong>Date d'expiration</strong>Fixez une date réaliste pour garder votre annonce à jour.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    // Mettre en évidence les compétences déjà cochées
    document.querySelectorAll('input[name="competances[]"]:checked').forEach(input => {
        input.closest('label').style.borderColor = 'var(--accent)';
        input.closest('label').style.background  = 'rgba(232,76,30,0.05)';
    });

    function toggleLabel(input) {
        const label = input.closest('label');
        if (input.checked) {
            label.style.borderColor = 'var(--accent)';
            label.style.background  = 'rgba(232,76,30,0.05)';
        } else {
            label.style.borderColor = 'var(--border)';
            label.style.background  = 'white';
        }
    }
</script>
@endpush
