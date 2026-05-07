@extends('layouts.app')
@section('title', 'Détail candidature')

@push('styles')
<style>
    .show-page { padding: 2.5rem 0; }

    .show-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 2rem;
        align-items: start;
    }

    .section-card {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.75rem; margin-bottom: 1.25rem;
    }

    .section-card h3 {
        font-family: var(--font-head); font-size: 1.05rem; font-weight: 700;
        margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; gap: 0.5rem;
    }

    .candidate-header {
        display: flex; align-items: center; gap: 1.25rem; margin-bottom: 1.5rem;
    }

    .avatar-lg {
        width: 72px; height: 72px; border-radius: 50%;
        background: var(--ink); color: var(--paper);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-head); font-size: 1.6rem; font-weight: 800;
        flex-shrink: 0;
    }

    .candidate-header h2 {
        font-family: var(--font-head); font-size: 1.3rem; font-weight: 800; margin-bottom: 0.25rem;
    }
    .candidate-header .email { color: var(--muted); font-size: 0.88rem; margin-bottom: 0.5rem; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .info-item .label { font-size: 0.78rem; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 0.2rem; }
    .info-item .value { font-size: 0.92rem; font-weight: 500; }

    .competence-tag {
        display: inline-flex; align-items: center;
        background: var(--paper); border: 1px solid var(--border);
        padding: 0.25rem 0.7rem; border-radius: 20px;
        font-size: 0.8rem; font-weight: 500; margin: 0.2rem;
    }

    .cv-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.75rem 1rem; background: var(--paper); border-radius: 8px; margin-bottom: 0.5rem;
    }
    .cv-item .cv-name { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 500; }
    .cv-item .cv-name i { color: var(--accent); }

    /* Sidebar sticky */
    .show-sidebar { position: sticky; top: 84px; }

    .status-form { }
    .status-form select { width: 100%; margin-bottom: 0.75rem; }
    .status-form textarea { width: 100%; resize: vertical; min-height: 80px; margin-bottom: 0.75rem; }

    .offre-mini {
        display: flex; flex-direction: column; gap: 0.5rem;
    }
    .offre-mini h4 { font-family: var(--font-head); font-size: 1rem; font-weight: 700; margin-bottom: 0.35rem; }
    .offre-mini .meta-row { display: flex; align-items: center; gap: 0.4rem; font-size: 0.83rem; color: var(--muted); }

    .breadcrumb { font-size: 0.85rem; color: var(--muted); margin-bottom: 1.25rem; }
    .breadcrumb a { color: var(--accent2); text-decoration: none; }

    @media (max-width: 900px) {
        .show-layout { grid-template-columns: 1fr; }
        .show-sidebar { position: static; }
    }
</style>
@endpush

@section('content')
<div class="container show-page">

    <div class="breadcrumb">
        <a href="{{ route('entreprise.dashboard') }}">Dashboard</a> /
        <a href="{{ route('entreprise.candidatures') }}">Candidatures</a> /
        {{ $candidature->particulier->utilisateur->prenom }} {{ $candidature->particulier->utilisateur->nom }}
    </div>

    <div class="show-layout">

        <!-- Contenu principal -->
        <div>
            <!-- Profil candidat -->
            <div class="section-card">
                <h3><i class="fas fa-user" style="color:var(--accent);"></i> Profil du candidat</h3>

                <div class="candidate-header">
                    <div class="avatar-lg">
                        {{ strtoupper(substr($candidature->particulier->utilisateur->prenom, 0, 1) . substr($candidature->particulier->utilisateur->nom, 0, 1)) }}
                    </div>
                    <div>
                        <h2>
                            {{ $candidature->particulier->utilisateur->prenom }}
                            {{ $candidature->particulier->utilisateur->nom }}
                        </h2>
                        <div class="email">{{ $candidature->particulier->utilisateur->email }}</div>
                        @php
                            $bc = match($candidature->statut) { 'acceptee'=>'badge-green','refusee'=>'badge-red','en_cours'=>'badge-blue', default=>'badge-yellow' };
                            $bl = match($candidature->statut) { 'acceptee'=>'Acceptée','refusee'=>'Refusée','en_cours'=>'En cours', default=>'En attente' };
                        @endphp
                        <span class="badge {{ $bc }}">{{ $bl }}</span>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Téléphone</div>
                        <div class="value">{{ $candidature->particulier->tel ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Date de naissance</div>
                        <div class="value">{{ $candidature->particulier->date_naissance?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div class="info-item" style="grid-column:1/-1;">
                        <div class="label">Adresse</div>
                        <div class="value">{{ $candidature->particulier->adresse ?? '—' }}</div>
                    </div>
                    @if($candidature->particulier->bio)
                    <div class="info-item" style="grid-column:1/-1;">
                        <div class="label">Bio</div>
                        <div class="value" style="line-height:1.6;">{{ $candidature->particulier->bio }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Compétences -->
            <div class="section-card">
                <h3><i class="fas fa-star" style="color:var(--accent);"></i> Compétences</h3>
                @forelse($candidature->particulier->competances as $comp)
                    <span class="competence-tag">{{ $comp->nom }}</span>
                @empty
                    <p style="color:var(--muted);font-size:0.9rem;">Aucune compétence renseignée.</p>
                @endforelse
            </div>

            <!-- CV -->
            <div class="section-card">
                <h3><i class="fas fa-file-pdf" style="color:var(--accent);"></i> CV</h3>
                @forelse($candidature->particulier->cv as $cv)
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
                                <i class="fas fa-download"></i> Télécharger
                            </a>
                        </div>
                    </div>
                @empty
                    <p style="color:var(--muted);font-size:0.9rem;">Aucun CV disponible.</p>
                @endforelse
            </div>

            @if($candidature->commentaire)
            <div class="section-card">
                <h3><i class="fas fa-comment" style="color:var(--accent);"></i> Commentaire précédent</h3>
                <p style="font-size:0.92rem;line-height:1.65;color:#444;font-style:italic;">
                    "{{ $candidature->commentaire }}"
                </p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="show-sidebar">

            <!-- Changer statut -->
            <div class="section-card">
                <h3><i class="fas fa-exchange-alt" style="color:var(--accent);"></i> Changer le statut</h3>

                <form method="POST" action="{{ route('entreprise.candidature.statut', $candidature->id) }}" class="status-form">
                    @csrf @method('PATCH')

                    <div class="form-group">
                        <label style="font-size:0.82rem;font-weight:600;margin-bottom:0.4rem;display:block;">Statut</label>
                        <select name="statut" class="form-control">
                            <option value="en_attente" {{ $candidature->statut == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                            <option value="en_cours"   {{ $candidature->statut == 'en_cours'   ? 'selected' : '' }}>🔄 En cours</option>
                            <option value="acceptee"   {{ $candidature->statut == 'acceptee'   ? 'selected' : '' }}>✅ Acceptée</option>
                            <option value="refusee"    {{ $candidature->statut == 'refusee'    ? 'selected' : '' }}>❌ Refusée</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="font-size:0.82rem;font-weight:600;margin-bottom:0.4rem;display:block;">Commentaire (optionnel)</label>
                        <textarea name="commentaire" class="form-control"
                                  placeholder="Ajouter un message pour le candidat...">{{ $candidature->commentaire }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </form>
            </div>

            <!-- Offre concernée -->
            <div class="section-card">
                <h3><i class="fas fa-briefcase" style="color:var(--accent);"></i> Offre concernée</h3>
                <div class="offre-mini">
                    <h4>{{ $candidature->offre->titre }}</h4>
                    @if($candidature->offre->contrat)
                        <div class="meta-row"><i class="fas fa-file-contract"></i> {{ $candidature->offre->contrat }}</div>
                    @endif
                    @if($candidature->offre->localisation)
                        <div class="meta-row"><i class="fas fa-map-marker-alt"></i> {{ $candidature->offre->localisation }}</div>
                    @endif
                    @if($candidature->offre->salaire)
                        <div class="meta-row"><i class="fas fa-money-bill-wave"></i> {{ $candidature->offre->salaire }}</div>
                    @endif
                    <div class="meta-row"><i class="fas fa-calendar"></i> Candidature le {{ $candidature->date->format('d/m/Y') }}</div>
                    <a href="{{ route('offres.show', $candidature->offre->id) }}" class="btn btn-outline btn-sm" style="margin-top:0.5rem;">
                        Voir l'offre <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <a href="{{ route('entreprise.candidatures') }}" class="btn btn-outline" style="width:100%;justify-content:center;">
                <i class="fas fa-arrow-left"></i> Retour aux candidatures
            </a>
        </div>

    </div>
</div>
@endsection
