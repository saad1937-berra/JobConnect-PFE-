@extends('layouts.entreprise')
@section('title', 'Détail candidature')

@section('ent-content')

    <div class="ent-breadcrumb">
        <a href="{{ route('entreprise.dashboard') }}">Dashboard</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('entreprise.candidatures') }}">Candidatures</a>
        <i class="fas fa-chevron-right"></i>
        {{ $candidature->particulier->utilisateur->prenom }} {{ $candidature->particulier->utilisateur->nom }}
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">

        <!-- Colonne principale -->
        <div>

            <!-- Profil candidat -->
            <div class="ent-section-card">
                <div class="ent-section-card-header">
                    <h3><i class="fas fa-user"></i> Profil du candidat</h3>
                    @php
                        $bc = match($candidature->statut) { 'acceptee'=>'green','refusee'=>'red','en_cours'=>'blue', default=>'yellow' };
                        $bl = match($candidature->statut) { 'acceptee'=>'Acceptée','refusee'=>'Refusée','en_cours'=>'En cours', default=>'En attente' };
                    @endphp
                    <span class="ent-badge ent-badge-{{ $bc }}">{{ $bl }}</span>
                </div>

                <div style="display:flex;align-items:center;gap:1.25rem;margin-bottom:1.5rem;">
                    @if($particulier->photo)
                        <img src="{{ asset('storage/'.$particulier->photo) }}"
                             style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid var(--ent-border);">
                    @else
                        <div style="width:72px;height:72px;border-radius:50%;background:var(--ent-green-pale);color:var(--ent-green);display:flex;align-items:center;justify-content:center;font-family:var(--ent-font-head);font-size:1.5rem;font-weight:800;flex-shrink:0;">
                            {{ strtoupper(substr($candidature->particulier->utilisateur->prenom,0,1).substr($candidature->particulier->utilisateur->nom,0,1)) }}
                        </div>
                    @endif
                     <div>
                         <h2 style="font-family:var(--ent-font-head);font-size:1.25rem;font-weight:800;margin-bottom:0.2rem;">
                             {{ $candidature->particulier->utilisateur->prenom }}
                             {{ $candidature->particulier->utilisateur->nom }}
                         </h2>
                         <div style="font-size:0.85rem;color:var(--ent-muted);">{{ $candidature->particulier->utilisateur->email }}</div>
                     </div>
                    <form method="POST" action="{{ route('messages.start') }}" style="margin-left:auto;">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $candidature->particulier->utilisateur->id }}">
                        <button type="submit" class="ent-btn ent-btn-primary ent-btn-sm">
                            <i class="fas fa-comments"></i> Message
                        </button>
                    </form>
                 </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <p style="font-size:0.72rem;color:var(--ent-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.2rem;">Téléphone</p>
                        <p style="font-size:0.9rem;font-weight:500;">{{ $candidature->particulier->tel ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.72rem;color:var(--ent-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.2rem;">Date de naissance</p>
                        <p style="font-size:0.9rem;font-weight:500;">{{ $candidature->particulier->date_naissance?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.72rem;color:var(--ent-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.2rem;">Adresse</p>
                        <p style="font-size:0.9rem;font-weight:500;">{{ $candidature->particulier->adresse ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.72rem;color:var(--ent-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.2rem;">Niveau d'études</p>
                        <p style="font-size:0.9rem;font-weight:500;">{{ $candidature->particulier->niveau_etude ?? '—' }}</p>
                    </div>
                    @if($candidature->particulier->bio)
                    <div style="grid-column:1/-1;">
                        <p style="font-size:0.72rem;color:var(--ent-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.2rem;">Bio</p>
                        <p style="font-size:0.88rem;line-height:1.65;color:#444;">{{ $candidature->particulier->bio }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Compétences -->
            <div class="ent-section-card">
                <div class="ent-section-card-header">
                    <h3><i class="fas fa-star"></i> Compétences</h3>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                    @forelse($candidature->particulier->competances as $comp)
                        @php
                            $niveauColor = match($comp->pivot->niveau ?? 'Débutant') {
                                'Expert'        => '#059669',
                                'Avancé'        => '#2563eb',
                                'Intermédiaire' => '#d97706',
                                default         => '#6b7280',
                            };
                        @endphp
                        <span style="display:inline-flex;align-items:center;gap:0.4rem;background:var(--ent-bg);border:1px solid var(--ent-border);padding:0.25rem 0.75rem;border-radius:20px;font-size:0.8rem;font-weight:500;">
                            {{ $comp->nom }}
                            <span style="background:{{ $niveauColor }};color:white;font-size:0.65rem;padding:0.05rem 0.35rem;border-radius:8px;">
                                {{ $comp->pivot->niveau ?? '—' }}
                            </span>
                        </span>
                    @empty
                        <p style="color:var(--ent-muted);font-size:0.88rem;">Aucune compétence renseignée.</p>
                    @endforelse
                </div>
            </div>

            <!-- CV -->
            <div class="ent-section-card">
                <div class="ent-section-card-header">
                    <h3><i class="fas fa-file-pdf"></i> CV</h3>
                </div>
                @forelse($candidature->particulier->cv as $cv)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem 1rem;background:var(--ent-bg);border-radius:8px;margin-bottom:0.5rem;border:1px solid var(--ent-border);">
                        <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.88rem;font-weight:500;">
                            <i class="fas fa-file-pdf" style="color:#dc2626;"></i>
                            CV_{{ $cv->created_at->format('d-m-Y') }}.pdf
                        </div>
                        <div style="display:flex;gap:0.5rem;">
                            <a href="{{ route('entreprise.candidature.cv', $candidature->id) }}" target="_blank" class="ent-btn ent-btn-outline ent-btn-sm">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            <a href="{{ route('entreprise.candidature.cv', $candidature->id) }}" class="ent-btn ent-btn-secondary ent-btn-sm">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <p style="color:var(--ent-muted);font-size:0.88rem;">Aucun CV disponible.</p>
                @endforelse
            </div>

            @if($candidature->commentaire)
            <div class="ent-section-card">
                <div class="ent-section-card-header">
                    <h3><i class="fas fa-comment"></i> Commentaire précédent</h3>
                </div>
                <p style="font-size:0.9rem;line-height:1.65;color:#444;font-style:italic;background:var(--ent-bg);padding:1rem;border-radius:8px;border-left:3px solid var(--ent-green);">
                    "{{ $candidature->commentaire }}"
                </p>
            </div>
            @endif
        </div>

        <!-- Sidebar sticky -->
        <div style="position:sticky;top:calc(var(--ent-topbar-h) + 1rem);">

            <!-- Changer statut -->
            <div class="ent-section-card">
                <div class="ent-section-card-header">
                    <h3><i class="fas fa-exchange-alt"></i> Changer le statut</h3>
                </div>
                <form method="POST" action="{{ route('entreprise.candidature.statut', $candidature->id) }}">
                    @csrf @method('PATCH')
                    <div class="ent-form-group">
                        <label>Statut</label>
                        <select name="statut" class="ent-form-control">
                            <option value="en_attente" {{ $candidature->statut == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                            <option value="en_cours"   {{ $candidature->statut == 'en_cours'   ? 'selected' : '' }}>🔄 En cours</option>
                            <option value="acceptee"   {{ $candidature->statut == 'acceptee'   ? 'selected' : '' }}>✅ Acceptée</option>
                            <option value="refusee"    {{ $candidature->statut == 'refusee'    ? 'selected' : '' }}>❌ Refusée</option>
                        </select>
                    </div>
                    <div class="ent-form-group">
                        <label>Commentaire (optionnel)</label>
                        <textarea name="commentaire" class="ent-form-control" rows="3"
                                  placeholder="Message pour le candidat...">{{ $candidature->commentaire }}</textarea>
                    </div>
                    <button type="submit" class="ent-btn ent-btn-primary" style="width:100%;justify-content:center;">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </form>
            </div>

            <!-- Offre concernée -->
            <div class="ent-section-card">
                <div class="ent-section-card-header">
                    <h3><i class="fas fa-briefcase"></i> Offre concernée</h3>
                </div>
                <h4 style="font-family:var(--ent-font-head);font-size:0.95rem;font-weight:700;margin-bottom:0.75rem;">{{ $candidature->offre->titre }}</h4>
                <div style="display:flex;flex-direction:column;gap:0.4rem;font-size:0.83rem;color:var(--ent-muted);margin-bottom:1rem;">
                    @if($candidature->offre->contrat)
                        <span><i class="fas fa-file-contract" style="width:14px;"></i> {{ $candidature->offre->contrat }}</span>
                    @endif
                    @if($candidature->offre->localisation)
                        <span><i class="fas fa-map-marker-alt" style="width:14px;"></i> {{ $candidature->offre->localisation }}</span>
                    @endif
                    @if($candidature->offre->salaire)
                        <span><i class="fas fa-money-bill-wave" style="width:14px;"></i> {{ $candidature->offre->salaire }}</span>
                    @endif
                    <span><i class="fas fa-calendar" style="width:14px;"></i> Postulé le {{ $candidature->date->format('d/m/Y') }}</span>
                </div>
                <a href="{{ route('offres.show', $candidature->offre->id) }}" class="ent-btn ent-btn-outline ent-btn-sm" style="width:100%;justify-content:center;">
                    Voir l'offre <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <a href="{{ route('entreprise.candidatures') }}" class="ent-btn ent-btn-outline" style="width:100%;justify-content:center;">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

    </div>

@endsection
