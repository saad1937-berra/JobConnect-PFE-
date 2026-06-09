@extends('layouts.app')
@section('title', 'Notifications')

@push('styles')
<style>
    .notif-page { padding: 2.5rem 0; max-width: 760px; margin: 0 auto; }
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.75rem; }
    .page-header h1 { font-family: var(--font-head); font-size: 2rem; font-weight: 800; letter-spacing:-0.5px; }

    .notif-item {
        background: white; border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.25rem 1.5rem;
        margin-bottom: 0.75rem; display: flex; gap: 1rem; align-items: flex-start;
        transition: box-shadow 0.2s, border-color 0.2s;
        text-decoration: none; color: inherit;
    }
    .notif-item.unread { border-left: 3px solid var(--accent); }
    .notif-item:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.06); border-color: var(--accent2); }

    .notif-icon {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }

    .notif-body { flex: 1; }
    .notif-body p { font-size: 0.9rem; line-height: 1.5; margin-bottom: 0.25rem; }
    .notif-body .notif-time { font-size: 0.78rem; color: var(--muted); }

    .notif-action { flex-shrink: 0; }
    .notif-action form { display: inline; }

    .empty-state {
        text-align: center; padding: 4rem 2rem; color: var(--muted);
        background: white; border: 1px solid var(--border); border-radius: var(--radius);
    }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; }
</style>
@endpush

@section('content')
<div class="container notif-page">
    <div class="page-header">
        <div>
            <h1>Notifications</h1>
            <p style="color:var(--muted);font-size:0.9rem;">
                {{ $notifications->where('date_lecture', null)->count() }} non lue(s)
            </p>
        </div>
        @if($notifications->where('date_lecture', null)->count() > 0)
            <form method="POST" action="{{ route('notifications.lire.tout') }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-outline btn-sm">
                    <i class="fas fa-check-double"></i> Tout marquer lu
                </button>
            </form>
        @endif
    </div>

    @forelse($notifications as $notif)
        @php
            // Icône selon le type
            $iconConfig = match($notif->type) {
                'candidature' => ['bg' => '#d1ecf1', 'color' => '#0c5460', 'icon' => 'fa-paper-plane'],
                'acceptee'    => ['bg' => '#d4edda', 'color' => '#155724', 'icon' => 'fa-check-circle'],
                'refusee'     => ['bg' => '#f8d7da', 'color' => '#721c24', 'icon' => 'fa-times-circle'],
                'en_cours'    => ['bg' => '#d1ecf1', 'color' => '#0c5460', 'icon' => 'fa-sync-alt'],
                'entreprise'  => ['bg' => '#e2d9f3', 'color' => '#6f42c1', 'icon' => 'fa-building'],
                'offre'       => ['bg' => '#fff3cd', 'color' => '#856404', 'icon' => 'fa-briefcase'],
                default       => ['bg' => '#fff3cd', 'color' => '#856404', 'icon' => 'fa-bell'],
            };

            // Lien de redirection selon le type et le rôle
            $lien = match($notif->type) {
                'candidature' => auth()->user()->isEntreprise()
                                    ? route('entreprise.candidatures')
                                    : route('particulier.candidatures'),
                'acceptee',
                'refusee',
                'en_cours'    => route('particulier.candidatures'),
                'entreprise'  => route('admin.entreprises'),
                'offre'       => route('offres.index'),
                default       => '#',
            };
        @endphp

        <div style="display:flex; gap:0.75rem; align-items:flex-start; margin-bottom:0.75rem;">

            {{-- Lien cliquable sur toute la notif --}}
            <a href="{{ $lien }}"
               onclick="marquerLu({{ $notif->id }}, this)"
               class="notif-item {{ is_null($notif->date_lecture) ? 'unread' : '' }}"
               style="flex:1; margin-bottom:0;">

                <div class="notif-icon"
                     style="background:{{ $iconConfig['bg'] }};color:{{ $iconConfig['color'] }};">
                    <i class="fas {{ $iconConfig['icon'] }}"></i>
                </div>

                <div class="notif-body">
                    <p>{{ $notif->message }}</p>
                    <span class="notif-time">
                        <i class="fas fa-clock" style="font-size:0.7rem;"></i>
                        {{ $notif->created_at->diffForHumans() }}
                    </span>
                </div>

                @if(is_null($notif->date_lecture))
                    <span style="width:8px;height:8px;background:var(--accent);border-radius:50%;flex-shrink:0;margin-top:0.4rem;"></span>
                @endif
            </a>

            {{-- Bouton marquer lu séparé --}}
            @if(is_null($notif->date_lecture))
                <form method="POST" action="{{ route('notifications.lire', $notif->id) }}"
                      style="flex-shrink:0;margin-top:0.25rem;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline btn-sm" title="Marquer comme lu">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
            @endif
        </div>

    @empty
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <p style="font-size:1.05rem;font-weight:500;">Aucune notification</p>
            <p style="font-size:0.9rem;margin-top:0.35rem;">Vous êtes à jour !</p>
        </div>
    @endforelse

    <div style="margin-top:1.5rem;display:flex;justify-content:center;">
        {{ $notifications->links() }}
    </div>
</div>

@push('scripts')
<script>
    // Marquer comme lu via AJAX au clic, puis rediriger
    function marquerLu(id, linkEl) {
        event.preventDefault();
        const url    = linkEl.href;
        const token  = document.querySelector('meta[name="csrf-token"]').content;

        fetch(`/notifications/${id}/lire`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'X-HTTP-Method-Override': 'PATCH',
            },
        }).finally(() => {
            window.location.href = url;
        });
    }
</script>
@endpush
@endsection