@extends('layouts.app')
@section('title', 'Mes Candidatures')

@push('styles')
<style>
    .cand-page { padding: 2.5rem 0; }

    .page-header { margin-bottom: 2rem; }
    .page-header h1 { font-family: var(--font-head); font-size: 2rem; font-weight: 800; letter-spacing:-0.5px; }
    .page-header p { color: var(--muted); margin-top: 0.25rem; }

    .tabs {
        display: flex; gap: 0.25rem; margin-bottom: 1.5rem;
        background: white; border: 1px solid var(--border);
        border-radius: 10px; padding: 0.35rem; width: fit-content;
    }

    .tab-btn {
        padding: 0.45rem 1.1rem; border-radius: 7px; border: none;
        cursor: pointer; font-family: var(--font-body); font-size: 0.88rem;
        font-weight: 500; color: var(--muted); background: transparent;
        transition: all 0.2s;
    }
    .tab-btn.active { background: var(--ink); color: white; }

    .cand-list { display: flex; flex-direction: column; gap: 1rem; }

    .cand-card {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.5rem;
        display: flex; align-items: center; gap: 1.25rem;
        transition: box-shadow 0.2s;
    }
    .cand-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.06); }

    .company-logo {
        width: 52px; height: 52px; border-radius: 10px;
        background: var(--paper); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-head); font-weight: 800; color: var(--accent);
        flex-shrink: 0; overflow: hidden; font-size: 1.1rem;
    }
    .company-logo img { width: 100%; height: 100%; object-fit: cover; }

    .cand-info { flex: 1; }
    .cand-info h3 { font-family: var(--font-head); font-size: 1rem; font-weight: 700; margin-bottom: 0.2rem; }
    .cand-info .company { font-size: 0.85rem; color: var(--muted); margin-bottom: 0.5rem; }
    .cand-info .meta { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .meta-chip { font-size: 0.78rem; color: var(--muted); display: flex; align-items: center; gap: 0.3rem; }

    .cand-right { text-align: right; flex-shrink: 0; }
    .cand-right .date { font-size: 0.78rem; color: var(--muted); margin-bottom: 0.5rem; }

    .status-timeline {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.5rem; margin-top: 0.5rem;
        display: none;
    }

    .timeline { position: relative; padding-left: 1.5rem; }
    .timeline::before { content: ''; position: absolute; left: 6px; top: 8px; bottom: 8px; width: 2px; background: var(--border); }

    .timeline-item { position: relative; margin-bottom: 1rem; }
    .timeline-item::before {
        content: ''; position: absolute; left: -1.5rem; top: 5px;
        width: 12px; height: 12px; border-radius: 50%;
        background: var(--border); border: 2px solid white;
        box-shadow: 0 0 0 2px var(--border);
    }
    .timeline-item.done::before { background: #28a745; box-shadow: 0 0 0 2px #28a745; }
    .timeline-item.current::before { background: var(--accent); box-shadow: 0 0 0 2px var(--accent); }

    .timeline-item .tl-label { font-size: 0.85rem; font-weight: 600; }
    .timeline-item .tl-date { font-size: 0.78rem; color: var(--muted); }

    .empty-state {
        text-align: center; padding: 4rem 2rem; color: var(--muted);
        background: white; border: 1px solid var(--border); border-radius: var(--radius);
    }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; }
</style>
@endpush

@section('content')
<div class="container cand-page">
    <div class="page-header">
        <h1>Mes Candidatures</h1>
        <p>{{ $candidatures->count() }} candidature(s) au total</p>
    </div>

    <!-- Filtres par statut -->
    <div class="tabs">
        <button class="tab-btn active" onclick="filterStatus('all', this)">Toutes</button>
        <button class="tab-btn" onclick="filterStatus('en_attente', this)">En attente</button>
        <button class="tab-btn" onclick="filterStatus('en_cours', this)">En cours</button>
        <button class="tab-btn" onclick="filterStatus('acceptee', this)">Acceptées</button>
        <button class="tab-btn" onclick="filterStatus('refusee', this)">Refusées</button>
    </div>

    <div class="cand-list" id="cand-list">
        @forelse($candidatures as $cand)
            @php
                $badgeClass = match($cand->statut) {
                    'acceptee'   => 'badge-green',
                    'refusee'    => 'badge-red',
                    'en_cours'   => 'badge-blue',
                    default      => 'badge-yellow',
                };
                $badgeLabel = match($cand->statut) {
                    'acceptee'   => 'Acceptée',
                    'refusee'    => 'Refusée',
                    'en_cours'   => 'En cours',
                    default      => 'En attente',
                };
            @endphp

            <div class="cand-card" data-status="{{ $cand->statut }}">
                <div class="company-logo">
                    @if($cand->offre->entreprise->logo)
                        <img src="{{ asset('storage/'.$cand->offre->entreprise->logo) }}" alt="">
                    @else
                        {{ strtoupper(substr($cand->offre->entreprise->nom, 0, 2)) }}
                    @endif
                </div>
                <div class="cand-info">
                    <h3>
                        <a href="{{ route('offres.show', $cand->offre->id) }}" style="text-decoration:none;color:inherit;">
                            {{ $cand->offre->titre }}
                        </a>
                    </h3>
                    <div class="company">{{ $cand->offre->entreprise->nom }}</div>
                    <div class="meta">
                        @if($cand->offre->localisation)
                            <span class="meta-chip"><i class="fas fa-map-marker-alt"></i> {{ $cand->offre->localisation }}</span>
                        @endif
                        @if($cand->offre->contrat)
                            <span class="meta-chip"><i class="fas fa-briefcase"></i> {{ $cand->offre->contrat }}</span>
                        @endif
                    </div>
                    @if($cand->commentaire)
                        <p style="margin-top:0.5rem;font-size:0.83rem;color:var(--muted);font-style:italic;">
                            💬 {{ $cand->commentaire }}
                        </p>
                    @endif
                </div>
                <div class="cand-right">
                    <div class="date">{{ $cand->date->format('d/m/Y') }}</div>
                    <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-paper-plane"></i>
                <p style="font-size:1.05rem;font-weight:500;margin-bottom:0.5rem;">Aucune candidature</p>
                <p style="font-size:0.9rem;">Parcourez les offres et postulez dès maintenant !</p>
                <a href="{{ route('offres.index') }}" class="btn btn-primary" style="margin-top:1.5rem;">
                    <i class="fas fa-search"></i> Voir les offres
                </a>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    function filterStatus(status, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.cand-card').forEach(card => {
            card.style.display = (status === 'all' || card.dataset.status === status) ? 'flex' : 'none';
        });
    }
</script>
@endpush
@endsection
