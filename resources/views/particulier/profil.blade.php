@extends('layouts.app')
@section('title', 'Mon Profil')

@push('styles')
<style>
    .profil-page { padding: 2.5rem 0; }

    .profil-layout {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 2rem;
        align-items: start;
    }

    .profil-sidebar { position: sticky; top: 84px; }

    .profil-card {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 2rem; text-align: center;
    }

    .avatar {
        width: 90px; height: 90px; border-radius: 50%;
        background: var(--ink); color: var(--paper);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-head); font-size: 2rem; font-weight: 800;
        margin: 0 auto 1rem;
    }

    .profil-card h2 { font-family: var(--font-head); font-size: 1.2rem; font-weight: 800; margin-bottom: 0.25rem; }
    .profil-card .email { font-size: 0.85rem; color: var(--muted); margin-bottom: 1.25rem; }

    .profil-stat {
        display: flex; justify-content: space-around;
        padding: 1rem 0; border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border); margin-bottom: 1.25rem;
    }

    .stat-item strong { display: block; font-family: var(--font-head); font-size: 1.4rem; font-weight: 800; }
    .stat-item span { font-size: 0.75rem; color: var(--muted); }

    .nav-side { list-style: none; }
    .nav-side li a {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.65rem 0.75rem; border-radius: 8px;
        text-decoration: none; color: var(--muted); font-size: 0.9rem; transition: all 0.2s;
    }
    .nav-side li a:hover, .nav-side li a.active { background: var(--paper); color: var(--ink); font-weight: 500; }
    .nav-side li a .icon { width: 20px; text-align: center; }

    .profil-main { display: flex; flex-direction: column; gap: 1.25rem; }

    .section-card {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.75rem;
    }

    .section-card-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);
    }

    .section-card-header h3 { font-family: var(--font-head); font-size: 1.1rem; font-weight: 700; }

    /* Compétences */
    .competence-tags { display: flex; flex-wrap: wrap; gap: 0.6rem; }

    .competence-tag {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: var(--ink); color: var(--paper);
        padding: 0.35rem 0.75rem; border-radius: 20px;
        font-size: 0.82rem; font-weight: 500;
    }

    .niveau-badge {
        font-size: 0.65rem; font-weight: 700;
        padding: 0.1rem 0.45rem; border-radius: 10px;
        text-transform: uppercase; letter-spacing: 0.3px;
    }

    .competence-tag button {
        background: none; border: none; cursor: pointer;
        color: #aaa; padding: 0; line-height: 1; font-size: 0.75rem;
        transition: color 0.2s;
    }
    .competence-tag button:hover { color: var(--accent); }

    /* CV */
    .cv-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.75rem 1rem; background: var(--paper); border-radius: 8px; margin-bottom: 0.5rem;
    }
    .cv-item .cv-name { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 500; }
    .cv-item .cv-name i { color: var(--accent); }

    @media (max-width: 900px) {
        .profil-layout { grid-template-columns: 1fr; }
        .profil-sidebar { position: static; }
    }
</style>
@endpush

@section('content')
<div class="container profil-page">
    <div class="profil-layout">

        <!-- Sidebar -->
        <aside class="profil-sidebar">
            <div class="profil-card">
                {{-- Avatar cliquable --}}
                <div style="position:relative;width:100px;height:100px;margin:0 auto 1rem;cursor:pointer;"
                    onclick="document.getElementById('photo-input').click()">
                    @if($particulier->photo)
                        <img src="{{ asset('storage/'.$particulier->photo) }}"
                            style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid var(--border);">
                    @else
                        <div class="avatar">
                            {{ strtoupper(substr($utilisateur->prenom, 0, 1) . substr($utilisateur->nom, 0, 1)) }}
                        </div>
                    @endif
                    <div style="position:absolute;inset:0;border-radius:50%;background:rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;color:white;font-size:1.1rem;"
                        onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>

                {{-- Form upload caché --}}
                <form method="POST" action="{{ route('particulier.photo.upload') }}"
                    enctype="multipart/form-data" id="photo-form">
                    @csrf
                    <input type="file" id="photo-input" name="photo"
                        accept="image/jpeg,image/png,image/webp"
                        style="display:none;"
                        onchange="document.getElementById('photo-form').submit()">
                </form>
                <h2>{{ $utilisateur->prenom }} {{ $utilisateur->nom }}</h2>
                <div class="email">{{ $utilisateur->email }}</div>

                <div class="profil-stat">
                    <div class="stat-item">
                        <strong>{{ $particulier->candidatures->count() }}</strong>
                        <span>Candidatures</span>
                    </div>
                    <div class="stat-item">
                        <strong>{{ $particulier->competances->count() }}</strong>
                        <span>Compétences</span>
                    </div>
                    <div class="stat-item">
                        <strong>{{ $particulier->cv->count() }}</strong>
                        <span>CV</span>
                    </div>
                </div>

                <ul class="nav-side">
                    <li><a href="{{ route('particulier.profil') }}" class="active">
                        <span class="icon"><i class="fas fa-user"></i></span> Mon profil</a></li>
                    <li><a href="{{ route('particulier.candidatures') }}">
                        <span class="icon"><i class="fas fa-paper-plane"></i></span> Mes candidatures</a></li>
                    <li><a href="{{ route('particulier.matching') }}">
                        <span class="icon"><i class="fas fa-percentage"></i></span> Matching</a></li>
                    <li><a href="{{ route('particulier.suggestions') }}">
                        <span class="icon"><i class="fas fa-magic"></i></span> Suggestions</a></li>
                    <li><a href="{{ route('offres.index') }}">
                        <span class="icon"><i class="fas fa-search"></i></span> Offres d'emploi</a></li>
                    <li><a href="{{ route('notifications.index') }}">
                        <span class="icon"><i class="fas fa-bell"></i></span> Notifications</a></li>
                </ul>
            </div>
        </aside>

        <!-- Contenu principal -->
        <div class="profil-main">

            <!-- Informations personnelles -->
            <div class="section-card">
                <div class="section-card-header">
                    <h3><i class="fas fa-user" style="color:var(--accent);margin-right:0.5rem;"></i> Informations personnelles</h3>
                    <button onclick="toggleForm()" class="btn btn-outline btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                </div>

                {{-- Affichage --}}
                <div id="view-info">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <p style="font-size:0.78rem;color:var(--muted);margin-bottom:0.2rem;">Téléphone</p>
                            <p style="font-weight:500;">{{ $particulier->tel ?? '—' }}</p>
                        </div>
                        <div>
                            <p style="font-size:0.78rem;color:var(--muted);margin-bottom:0.2rem;">Date de naissance</p>
                            <p style="font-weight:500;">
                                {{ $particulier->date_naissance ? $particulier->date_naissance->format('d/m/Y') : '—' }}
                            </p>
                        </div>
                        <div>
                            <p style="font-size:0.78rem;color:var(--muted);margin-bottom:0.2rem;">Niveau d'études</p>
                            <p style="font-weight:500;">{{ $particulier->niveau_etude ?? '—' }}</p>
                        </div>
                        <div>
                            <p style="font-size:0.78rem;color:var(--muted);margin-bottom:0.2rem;">Adresse</p>
                            <p style="font-weight:500;">{{ $particulier->adresse ?? '—' }}</p>
                        </div>
                        <div style="grid-column:1/-1;">
                            <p style="font-size:0.78rem;color:var(--muted);margin-bottom:0.2rem;">Bio</p>
                            <p style="line-height:1.6;">{{ $particulier->bio ?? 'Aucune bio renseignée.' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Formulaire modification --}}
                <form id="edit-form" style="display:none;margin-top:1rem;"
                      method="POST" action="{{ route('particulier.profil.update') }}">
                    @csrf @method('PUT')

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" name="tel" class="form-control"
                                   value="{{ old('tel', $particulier->tel) }}"
                                   placeholder="06 00 00 00 00">
                        </div>
                        <div class="form-group">
                            <label>Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control"
                                   value="{{ old('date_naissance', $particulier->date_naissance?->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group">
                            <label>Niveau d'études</label>
                            <select name="niveau_etude" class="form-control">
                                <option value="">-- Choisir --</option>
                                @foreach(['Bac', 'Bac+2', 'Bac+3', 'Bac+4', 'Bac+5', 'Doctorat'] as $niv)
                                    <option value="{{ $niv }}"
                                        {{ old('niveau_etude', $particulier->niveau_etude) == $niv ? 'selected' : '' }}>
                                        {{ $niv }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Adresse</label>
                            <input type="text" name="adresse" class="form-control"
                                   value="{{ old('adresse', $particulier->adresse) }}"
                                   placeholder="Casablanca, Maroc">
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label>Bio</label>
                            <textarea name="bio" class="form-control" rows="3"
                                      placeholder="Parlez de vous...">{{ old('bio', $particulier->bio) }}</textarea>
                        </div>
                    </div>

                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                        <button type="button" onclick="toggleForm()" class="btn btn-outline btn-sm">Annuler</button>
                    </div>
                </form>
            </div>

            <!-- CV -->
            <div class="section-card">
                <div class="section-card-header">
                    <h3><i class="fas fa-file-pdf" style="color:var(--accent);margin-right:0.5rem;"></i> Mes CV</h3>
                </div>

                @forelse($particulier->cv as $cv)
                    <div class="cv-item">
                        <div class="cv-name">
                            <i class="fas fa-file-pdf"></i>
                            CV_{{ $cv->created_at->format('d-m-Y') }}.pdf
                        </div>
                        <div style="display:flex;gap:0.5rem;">
                            <a href="{{ asset('storage/'.$cv->cv_path) }}" target="_blank" class="btn btn-outline btn-sm">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            <a href="{{ asset('storage/'.$cv->cv_path) }}" download class="btn btn-secondary btn-sm">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <p style="color:var(--muted);font-size:0.9rem;">Aucun CV uploadé.</p>
                @endforelse

                <form method="POST" action="{{ route('particulier.cv.upload') }}"
                      enctype="multipart/form-data"
                      style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);">
                    @csrf
                    <div style="display:flex;gap:0.75rem;align-items:flex-end;">
                        <div class="form-group" style="flex:1;margin-bottom:0;">
                            <label>Ajouter un nouveau CV (PDF, max 5 Mo)</label>
                            <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
                            @error('cv')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Uploader
                        </button>
                    </div>
                </form>
            </div>

            <!-- Compétences -->
            <div class="section-card">
                <div class="section-card-header">
                    <h3><i class="fas fa-star" style="color:var(--accent);margin-right:0.5rem;"></i> Compétences</h3>
                </div>

                {{-- Tags avec niveau --}}
                <div class="competence-tags" style="margin-bottom:1.5rem;">
                    @forelse($particulier->competances as $comp)
                        @php
                            $niveauColor = match($comp->pivot->niveau) {
                                'Expert'        => '#28a745',
                                'Avancé'        => '#17a2b8',
                                'Intermédiaire' => '#f0a500',
                                default         => '#888',
                            };
                        @endphp
                        <span class="competence-tag">
                            {{ $comp->nom }}
                            <span class="niveau-badge" style="background:{{ $niveauColor }};color:white;">
                                {{ $comp->pivot->niveau }}
                            </span>
                            <form method="POST" action="{{ route('particulier.competence.supprimer', $comp->id) }}"
                                  style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" title="Supprimer"><i class="fas fa-times"></i></button>
                            </form>
                        </span>
                    @empty
                        <p style="color:var(--muted);font-size:0.9rem;">Aucune compétence ajoutée.</p>
                    @endforelse
                </div>

                {{-- Légende niveaux --}}
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.25rem;padding:0.75rem;background:var(--paper);border-radius:8px;">
                    <span style="font-size:0.75rem;color:var(--muted);font-weight:600;">Niveaux :</span>
                    @foreach(['Débutant' => '#888', 'Intermédiaire' => '#f0a500', 'Avancé' => '#17a2b8', 'Expert' => '#28a745'] as $niv => $color)
                        <span style="display:inline-flex;align-items:center;gap:0.3rem;font-size:0.75rem;">
                            <span style="width:10px;height:10px;border-radius:50%;background:{{ $color }};display:inline-block;"></span>
                            {{ $niv }}
                        </span>
                    @endforeach
                </div>

                {{-- Formulaire ajout --}}
                <form method="POST" action="{{ route('particulier.competence.ajouter') }}"
                      style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                    @csrf
                    <select name="competance_id" class="form-control" style="flex:2;min-width:180px;">
                        <option value="">-- Choisir une compétence --</option>
                        @foreach($competances ?? [] as $comp)
                            <option value="{{ $comp->id }}">{{ $comp->nom }}</option>
                        @endforeach
                    </select>
                    <select name="niveau" class="form-control" style="flex:1;min-width:140px;">
                        <option value="Débutant">🔵 Débutant</option>
                        <option value="Intermédiaire" selected>🟡 Intermédiaire</option>
                        <option value="Avancé">🔷 Avancé</option>
                        <option value="Expert">🟢 Expert</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleForm() {
        const form    = document.getElementById('edit-form');
        const view    = document.getElementById('view-info');
        const visible = form.style.display !== 'none';
        form.style.display = visible ? 'none'  : 'block';
        view.style.display = visible ? 'block' : 'none';
    }
</script>
@endpush
@endsection