@extends('layouts.entreprise')
@section('title', 'Profil entreprise')

@section('ent-content')

    <div class="ent-page-header">
        <div>
            <h1>Profil entreprise</h1>
            <p>Gérez les informations de votre entreprise</p>
        </div>
        <button onclick="toggleForm()" class="ent-btn ent-btn-outline">
            <i class="fas fa-edit"></i> Modifier
        </button>
    </div>

    <div style="display:grid;grid-template-columns:280px 1fr;gap:1.5rem;align-items:start;">

        <!-- Sidebar profil -->
        <div>
            <div class="ent-section-card" style="text-align:center;">

                {{-- Logo cliquable --}}
                <div style="margin-bottom:1.25rem;">
                    <div class="ent-logo-wrapper" onclick="document.getElementById('logo-input').click()">
                        <div class="ent-logo-box" style="width:90px;height:90px;font-size:1.75rem;margin:0 auto;">
                            @if($entreprise->logo)
                                <img src="{{ asset('storage/'.$entreprise->logo) }}" alt="Logo">
                            @else
                                {{ strtoupper(substr($entreprise->nom, 0, 2)) }}
                            @endif
                        </div>
                        <div class="ent-logo-overlay" style="border-radius:14px;">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('entreprise.logo.upload') }}"
                          enctype="multipart/form-data" id="logo-form">
                        @csrf
                        <input type="file" id="logo-input" name="logo"
                               accept="image/jpeg,image/png,image/webp"
                               style="display:none;"
                               onchange="document.getElementById('logo-form').submit()">
                    </form>
                    <p style="font-size:0.72rem;color:var(--ent-muted);margin-top:0.5rem;">Cliquez pour changer</p>
                </div>

                <h2 style="font-family:var(--ent-font-head);font-size:1.1rem;font-weight:800;margin-bottom:0.2rem;">{{ $entreprise->nom }}</h2>
                <p style="font-size:0.85rem;color:var(--ent-muted);margin-bottom:1.25rem;">{{ $entreprise->secteur ?? 'Secteur non renseigné' }}</p>

                @if($entreprise->site_web)
                    <a href="{{ $entreprise->site_web }}" target="_blank"
                       style="font-size:0.82rem;color:var(--ent-green);text-decoration:none;display:block;">
                        <i class="fas fa-external-link-alt"></i> {{ $entreprise->site_web }}
                    </a>
                @endif
            </div>

            <!-- Stats rapides -->
            <div class="ent-section-card">
                <div style="display:flex;flex-direction:column;gap:0.75rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;border-bottom:1px solid var(--ent-border);">
                        <span style="font-size:0.85rem;color:var(--ent-muted);">Offres publiées</span>
                        <strong style="font-family:var(--ent-font-head);font-size:1.2rem;color:var(--ent-green);">{{ $entreprise->offres()->count() }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;border-bottom:1px solid var(--ent-border);">
                        <span style="font-size:0.85rem;color:var(--ent-muted);">Offres actives</span>
                        <strong style="font-family:var(--ent-font-head);font-size:1.2rem;color:var(--ent-green);">{{ $entreprise->offres()->where('statut','active')->count() }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;">
                        <span style="font-size:0.85rem;color:var(--ent-muted);">Candidatures reçues</span>
                        <strong style="font-family:var(--ent-font-head);font-size:1.2rem;color:var(--ent-green);">{{ \App\Models\Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div>
            <div class="ent-section-card">
                <div class="ent-section-card-header">
                    <h3><i class="fas fa-building"></i> Informations de l'entreprise</h3>
                </div>

                {{-- Affichage --}}
                <div id="view-info">
                    <div class="ent-info-row">
                        <span class="lbl"><i class="fas fa-building"></i> Nom</span>
                        <span class="val">{{ $entreprise->nom }}</span>
                    </div>
                    <div class="ent-info-row">
                        <span class="lbl"><i class="fas fa-industry"></i> Secteur</span>
                        <span class="val">{{ $entreprise->secteur ?? '—' }}</span>
                    </div>
                    <div class="ent-info-row">
                        <span class="lbl"><i class="fas fa-map-marker-alt"></i> Adresse</span>
                        <span class="val">{{ $entreprise->adresse ?? '—' }}</span>
                    </div>
                    <div class="ent-info-row">
                        <span class="lbl"><i class="fas fa-globe"></i> Site web</span>
                        <span class="val">
                            @if($entreprise->site_web)
                                <a href="{{ $entreprise->site_web }}" target="_blank" style="color:var(--ent-green);text-decoration:none;">{{ $entreprise->site_web }}</a>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                    <div class="ent-info-row" style="align-items:flex-start;">
                        <span class="lbl"><i class="fas fa-align-left"></i> Description</span>
                        <span class="val" style="font-weight:400;line-height:1.6;">{{ $entreprise->description ?? 'Aucune description.' }}</span>
                    </div>
                </div>

                {{-- Formulaire modification --}}
                <form id="edit-form" style="display:none;margin-top:1.25rem;"
                      method="POST" action="{{ route('entreprise.profil.update') }}">
                    @csrf @method('PUT')

                    <div class="ent-form-row">
                        <div class="ent-form-group">
                            <label>Nom de l'entreprise <span style="color:#dc2626">*</span></label>
                            <input type="text" name="nom" class="ent-form-control"
                                   value="{{ old('nom', $entreprise->nom) }}" required>
                            @error('nom')<p class="ent-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="ent-form-group">
                            <label>Secteur d'activité</label>
                            <input type="text" name="secteur" class="ent-form-control"
                                   value="{{ old('secteur', $entreprise->secteur) }}"
                                   placeholder="Informatique, Finance...">
                        </div>
                        <div class="ent-form-group">
                            <label>Adresse</label>
                            <input type="text" name="adresse" class="ent-form-control"
                                   value="{{ old('adresse', $entreprise->adresse) }}"
                                   placeholder="Casablanca, Maroc">
                        </div>
                        <div class="ent-form-group">
                            <label>Site web</label>
                            <input type="url" name="site_web" class="ent-form-control"
                                   value="{{ old('site_web', $entreprise->site_web) }}"
                                   placeholder="https://...">
                            @error('site_web')<p class="ent-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="ent-form-group" style="grid-column:1/-1;">
                            <label>Description</label>
                            <textarea name="description" class="ent-form-control" rows="4"
                                      placeholder="Décrivez votre entreprise...">{{ old('description', $entreprise->description) }}</textarea>
                        </div>
                    </div>

                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" class="ent-btn ent-btn-primary">Enregistrer</button>
                        <button type="button" onclick="toggleForm()" class="ent-btn ent-btn-outline">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    function toggleForm() {
        const form = document.getElementById('edit-form');
        const view = document.getElementById('view-info');
        const visible = form.style.display !== 'none';
        form.style.display = visible ? 'none' : 'block';
        view.style.display = visible ? 'block' : 'none';
    }
</script>
@endpush

@endsection