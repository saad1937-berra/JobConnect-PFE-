@extends('layouts.particulier')
@section('title', 'Mon Profil')

@section('part-content')

<div class="part-profil-layout">

    <!-- Sidebar -->
    <aside class="part-profil-sidebar">
        <div class="part-profil-card">

            {{-- Avatar cliquable --}}
            <div style="margin:0 auto 1rem;display:inline-block;">
                <div class="part-avatar-upload" onclick="document.getElementById('photo-input').click()">
                    <div class="part-avatar-box">
                        @if($particulier->photo)
                            <img src="{{ asset('storage/'.$particulier->photo) }}" alt="">
                        @else
                            {{ strtoupper(substr($utilisateur->prenom, 0, 1)) }}
                        @endif
                    </div>
                    <div class="part-avatar-overlay"><i class="fas fa-camera"></i></div>
                </div>
                <form method="POST" action="{{ route('particulier.photo.upload') }}"
                      enctype="multipart/form-data" id="photo-form">
                    @csrf
                    <input type="file" id="photo-input" name="photo"
                           accept="image/jpeg,image/png,image/webp"
                           style="display:none;"
                           onchange="document.getElementById('photo-form').submit()">
                </form>
            </div>

            <h2>{{ $utilisateur->prenom }} {{ $utilisateur->nom }}</h2>
            <div class="email">{{ $utilisateur->email }}</div>

            {{-- Stats --}}
            <div class="part-profil-stat">
                <div class="part-stat-item">
                    <strong>{{ $particulier->candidatures->count() }}</strong>
                    <span>Candidatures</span>
                </div>
                <div class="part-stat-item">
                    <strong>{{ $particulier->competances->count() }}</strong>
                    <span>Skills</span>
                </div>
                <div class="part-stat-item">
                    <strong>{{ $particulier->cv->count() }}</strong>
                    <span>CV</span>
                </div>
            </div>

            {{-- Nav --}}
            <ul class="part-profil-nav">
                <li><a href="{{ route('particulier.profil') }}" class="active">
                    <i class="fas fa-user"></i> Mon profil</a></li>
                <li><a href="{{ route('particulier.candidatures') }}">
                    <i class="fas fa-paper-plane"></i> Mes candidatures</a></li>
                <li><a href="{{ route('particulier.suggestions') }}">
                    <i class="fas fa-magic"></i> Suggestions</a></li>
                <li><a href="{{ route('particulier.matching') }}">
                    <i class="fas fa-percentage"></i> Matching</a></li>
                <li><a href="{{ route('notifications.index') }}">
                    <i class="fas fa-bell"></i> Notifications</a></li>
            </ul>
        </div>

        {{-- Profil complété --}}
        @php
            $checks = [
                'Bio' => !empty($particulier->bio),
                'Téléphone' => !empty($particulier->tel),
                'Adresse' => !empty($particulier->adresse),
                'Niveau d\'études' => !empty($particulier->niveau_etude),
                'CV' => $particulier->cv->count() > 0,
                'Compétences' => $particulier->competances->count() > 0,
            ];
            $score = round(collect($checks)->filter()->count() / count($checks) * 100);
        @endphp
        <div class="part-section-card" style="margin-top:1rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
                <span style="font-size:0.8rem;font-weight:800;text-transform:uppercase;letter-spacing:0.3px;">Profil complété</span>
                <span style="font-family:var(--part-font-head);font-size:1.3rem;font-weight:800;color:{{ $score >= 80 ? 'var(--part-success)' : ($score >= 50 ? '#f59e0b' : 'var(--part-error)') }};">{{ $score }}%</span>
            </div>
            <div style="background:var(--part-gray);border:1px solid var(--part-border);border-radius:4px;height:8px;margin-bottom:1rem;">
                <div style="width:{{ $score }}%;height:8px;border-radius:4px;background:{{ $score >= 80 ? 'var(--part-success)' : ($score >= 50 ? '#f59e0b' : 'var(--part-error)') }};"></div>
            </div>
            @foreach($checks as $label => $done)
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;font-size:0.83rem;font-weight:500;">
                    <span style="width:18px;height:18px;border-radius:50%;background:{{ $done ? 'var(--part-success)' : 'var(--part-border)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        @if($done) <i class="fas fa-check" style="font-size:0.6rem;color:white;"></i>
                        @else <i class="fas fa-times" style="font-size:0.6rem;color:var(--part-muted);"></i>
                        @endif
                    </span>
                    {{ $label }}
                </div>
            @endforeach
        </div>
    </aside>

    <!-- Contenu principal -->
    <div>

        <!-- Informations personnelles -->
        <div class="part-section-card">
            <div class="part-section-header">
                <h3><i class="fas fa-user" style="color:var(--part-yellow);background:var(--part-black);padding:0.3rem 0.4rem;border-radius:4px;font-size:0.8rem;"></i> Infos personnelles</h3>
                <button onclick="toggleForm()" class="part-btn part-btn-outline part-btn-sm">
                    <i class="fas fa-edit"></i> Modifier
                </button>
            </div>

            {{-- Affichage --}}
            <div id="view-info">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <p style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.3px;color:var(--part-muted);margin-bottom:0.2rem;">Téléphone</p>
                        <p style="font-weight:600;">{{ $particulier->tel ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.3px;color:var(--part-muted);margin-bottom:0.2rem;">Date de naissance</p>
                        <p style="font-weight:600;">{{ $particulier->date_naissance ? $particulier->date_naissance->format('d/m/Y') : '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.3px;color:var(--part-muted);margin-bottom:0.2rem;">Niveau d'études</p>
                        <p style="font-weight:600;">{{ $particulier->niveau_etude ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.3px;color:var(--part-muted);margin-bottom:0.2rem;">Adresse</p>
                        <p style="font-weight:600;">{{ $particulier->adresse ?? '—' }}</p>
                    </div>
                    <div style="grid-column:1/-1;">
                        <p style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.3px;color:var(--part-muted);margin-bottom:0.2rem;">Bio</p>
                        <p style="line-height:1.6;">{{ $particulier->bio ?? 'Aucune bio renseignée.' }}</p>
                    </div>
                </div>
            </div>

            {{-- Formulaire --}}
            <form id="edit-form" style="display:none;margin-top:1rem;"
                  method="POST" action="{{ route('particulier.profil.update') }}">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="part-form-group">
                        <label>Téléphone</label>
                        <input type="text" name="tel" class="part-form-control"
                               value="{{ old('tel', $particulier->tel) }}" placeholder="06 00 00 00 00">
                    </div>
                    <div class="part-form-group">
                        <label>Date de naissance</label>
                        <input type="date" name="date_naissance" class="part-form-control"
                               value="{{ old('date_naissance', $particulier->date_naissance?->format('Y-m-d')) }}">
                    </div>
                    <div class="part-form-group">
                        <label>Niveau d'études</label>
                        <select name="niveau_etude" class="part-form-control">
                            <option value="">-- Choisir --</option>
                            @foreach(['Bac', 'Bac+2', 'Bac+3', 'Bac+4', 'Bac+5', 'Doctorat'] as $niv)
                                <option value="{{ $niv }}" {{ old('niveau_etude', $particulier->niveau_etude) == $niv ? 'selected' : '' }}>
                                    {{ $niv }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="part-form-group">
                        <label>Adresse</label>
                        <input type="text" name="adresse" class="part-form-control"
                               value="{{ old('adresse', $particulier->adresse) }}" placeholder="Casablanca, Maroc">
                    </div>
                    <div class="part-form-group" style="grid-column:1/-1;">
                        <label>Bio</label>
                        <textarea name="bio" class="part-form-control" rows="3"
                                  placeholder="Parle de toi...">{{ old('bio', $particulier->bio) }}</textarea>
                    </div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <button type="submit" class="part-btn part-btn-primary">Enregistrer</button>
                    <button type="button" onclick="toggleForm()" class="part-btn part-btn-outline">Annuler</button>
                </div>
            </form>
        </div>

        <!-- CV -->
        <div class="part-section-card">
            <div class="part-section-header">
                <h3><i class="fas fa-file-pdf" style="color:var(--part-yellow);background:var(--part-black);padding:0.3rem 0.4rem;border-radius:4px;font-size:0.8rem;"></i> Mes CV</h3>
            </div>

            @forelse($particulier->cv as $cv)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem 1rem;background:var(--part-gray);border-radius:var(--part-radius);margin-bottom:0.5rem;border:1.5px solid var(--part-black);">
                    <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.88rem;font-weight:600;">
                        <i class="fas fa-file-pdf" style="color:#dc2626;"></i>
                        CV_{{ $cv->created_at->format('d-m-Y') }}.pdf
                    </div>
                    <div style="display:flex;gap:0.5rem;">
                        <a href="{{ asset('storage/'.$cv->cv_path) }}" target="_blank" class="part-btn part-btn-outline part-btn-sm">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <a href="{{ asset('storage/'.$cv->cv_path) }}" download class="part-btn part-btn-black part-btn-sm">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
            @empty
                <p style="color:var(--part-muted);font-size:0.9rem;margin-bottom:1rem;">Aucun CV uploadé.</p>
            @endforelse

            <form method="POST" action="{{ route('particulier.cv.upload') }}"
                  enctype="multipart/form-data"
                  style="margin-top:1.25rem;padding-top:1.25rem;border-top:2px solid var(--part-black);">
                @csrf
                <div style="display:flex;gap:0.75rem;align-items:flex-end;">
                    <div class="part-form-group" style="flex:1;margin-bottom:0;">
                        <label>Uploader un CV (PDF, max 5 Mo)</label>
                        <input type="file" name="cv" class="part-form-control" accept=".pdf,.doc,.docx">
                        @error('cv')<p class="part-form-error">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="part-btn part-btn-primary">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>

        <!-- Compétences -->
        <div class="part-section-card">
            <div class="part-section-header">
                <h3><i class="fas fa-star" style="color:var(--part-yellow);background:var(--part-black);padding:0.3rem 0.4rem;border-radius:4px;font-size:0.8rem;"></i> Mes Skills</h3>
            </div>

            {{-- Tags avec niveau --}}
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:1.25rem;">
                @forelse($particulier->competances as $comp)
                    @php
                        $niveauBg = match($comp->pivot->niveau ?? 'Débutant') {
                            'Expert'        => '#0a0a0a',
                            'Avancé'        => '#1e3a8a',
                            'Intermédiaire' => '#92400e',
                            default         => '#6b6b6b',
                        };
                    @endphp
                    <span style="display:inline-flex;align-items:center;gap:0.5rem;background:var(--part-yellow);color:var(--part-black);border:2px solid var(--part-black);padding:0.3rem 0.7rem;border-radius:4px;font-size:0.82rem;font-weight:700;">
                        {{ $comp->nom }}
                        <span style="background:{{ $niveauBg }};color:white;font-size:0.62rem;padding:0.08rem 0.4rem;border-radius:3px;font-weight:800;text-transform:uppercase;letter-spacing:0.2px;">
                            {{ $comp->pivot->niveau ?? '—' }}
                        </span>
                        <form method="POST" action="{{ route('particulier.competence.supprimer', $comp->id) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--part-black);font-size:0.75rem;padding:0;line-height:1;" title="Supprimer">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </span>
                @empty
                    <p style="color:var(--part-muted);font-size:0.9rem;">Aucune compétence ajoutée.</p>
                @endforelse
            </div>

            {{-- Légende niveaux --}}
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.25rem;padding:0.65rem 1rem;background:var(--part-gray);border-radius:var(--part-radius);border:1px solid var(--part-border);">
                <span style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.3px;color:var(--part-muted);">Niveaux :</span>
                @foreach(['Débutant' => '#6b6b6b', 'Intermédiaire' => '#92400e', 'Avancé' => '#1e3a8a', 'Expert' => '#0a0a0a'] as $niv => $color)
                    <span style="font-size:0.75rem;display:inline-flex;align-items:center;gap:0.3rem;font-weight:600;">
                        <span style="width:10px;height:10px;border-radius:2px;background:{{ $color }};display:inline-block;border:1px solid rgba(0,0,0,0.2);"></span>
                        {{ $niv }}
                    </span>
                @endforeach
            </div>

            {{-- Formulaire ajout --}}
            <form method="POST" action="{{ route('particulier.competence.ajouter') }}"
                  style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                @csrf
                <select name="competance_id" class="part-form-control" style="flex:2;min-width:180px;">
                    <option value="">-- Choisir une compétence --</option>
                    @foreach($competances ?? [] as $comp)
                        <option value="{{ $comp->id }}">{{ $comp->nom }}</option>
                    @endforeach
                </select>
                <select name="niveau" class="part-form-control" style="flex:1;min-width:140px;">
                    <option value="Débutant">Débutant</option>
                    <option value="Intermédiaire" selected>Intermédiaire</option>
                    <option value="Avancé">Avancé</option>
                    <option value="Expert">Expert</option>
                </select>
                <button type="submit" class="part-btn part-btn-primary">
                    <i class="fas fa-plus"></i> Ajouter
                </button>
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