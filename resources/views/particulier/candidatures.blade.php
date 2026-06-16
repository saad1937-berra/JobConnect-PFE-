@extends('layouts.particulier')
@section('title', 'Mes Candidatures')

@section('part-content')

    <div class="part-page-header">
        <h1>Mes <span>Candidatures</span></h1>
        <p>{{ $candidatures->count() }} candidature(s) au total</p>
    </div>

    <!-- Filtres par statut -->
    <div class="part-tabs">
        <button class="part-tab-btn active" onclick="filterStatus('all', this)">Toutes</button>
        <button class="part-tab-btn" onclick="filterStatus('en_attente', this)">En attente</button>
        <button class="part-tab-btn" onclick="filterStatus('en_cours', this)">En cours</button>
        <button class="part-tab-btn" onclick="filterStatus('acceptee', this)">Acceptées</button>
        <button class="part-tab-btn" onclick="filterStatus('refusee', this)">Refusées</button>
    </div>

    <div id="cand-list" style="display:flex;flex-direction:column;gap:0.85rem;">
        @forelse($candidatures as $cand)
            @php
                $bc = match($cand->statut) { 'acceptee'=>'green','refusee'=>'red','en_cours'=>'blue', default=>'yellow' };
                $bl = match($cand->statut) { 'acceptee'=>'Acceptée','refusee'=>'Refusée','en_cours'=>'En cours', default=>'En attente' };
            @endphp

            <div class="part-card" data-status="{{ $cand->statut }}"
                 style="display:flex;align-items:center;gap:1.25rem;padding:1.25rem 1.5rem;">

                {{-- Logo entreprise --}}
                <div class="part-company-logo" style="width:48px;height:48px;">
                    @if($cand->offre->entreprise->logo)
                        <img src="{{ asset('storage/'.$cand->offre->entreprise->logo) }}" alt="">
                    @else
                        {{ strtoupper(substr($cand->offre->entreprise->nom, 0, 2)) }}
                    @endif
                </div>

                {{-- Infos --}}
                <div style="flex:1;">
                    <a href="{{ route('offres.show', $cand->offre->id) }}"
                       style="font-family:var(--part-font-head);font-size:1rem;font-weight:700;text-decoration:none;color:var(--part-black);">
                        {{ $cand->offre->titre }}
                    </a>
                    <div style="font-size:0.82rem;color:var(--part-muted);margin-top:0.15rem;margin-bottom:0.5rem;">
                        {{ $cand->offre->entreprise->nom }}
                    </div>
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                        @if($cand->offre->localisation)
                            <span class="part-chip"><i class="fas fa-map-marker-alt"></i> {{ $cand->offre->localisation }}</span>
                        @endif
                        @if($cand->offre->contrat)
                            <span class="part-chip"><i class="fas fa-briefcase"></i> {{ $cand->offre->contrat }}</span>
                        @endif
                    </div>
                    @if($cand->commentaire)
                        <p style="margin-top:0.5rem;font-size:0.82rem;color:var(--part-muted);font-style:italic;border-left:3px solid var(--part-yellow);padding-left:0.6rem;">
                            {{ $cand->commentaire }}
                        </p>
                    @endif
                </div>

                {{-- Statut + date --}}
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:0.75rem;color:var(--part-muted);margin-bottom:0.5rem;">
                        {{ $cand->date->format('d/m/Y') }}
                    </div>
                    <span class="part-badge part-badge-{{ $bc }}">{{ $bl }}</span>
                </div>
            </div>
        @empty
            <div class="part-empty">
                <i class="fas fa-paper-plane"></i>
                <strong>Aucune candidature</strong>
                <p>Parcourez les offres et postulez dès maintenant !</p>
                <a href="{{ route('offres.index') }}" class="part-btn part-btn-primary" style="margin-top:1.25rem;">
                    <i class="fas fa-search"></i> Voir les offres
                </a>
            </div>
        @endforelse
    </div>

@push('scripts')
<script>
    function filterStatus(status, btn) {
        document.querySelectorAll('.part-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('#cand-list [data-status]').forEach(card => {
            card.style.display = (status === 'all' || card.dataset.status === status) ? 'flex' : 'none';
        });
    }
</script>
@endpush

@endsection