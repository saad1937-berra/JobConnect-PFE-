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
        transition: box-shadow 0.2s;
    }
    .notif-item.unread { border-left: 3px solid var(--accent); }
    .notif-item:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.06); }

    .notif-icon {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }

    .notif-body { flex: 1; }
    .notif-body p { font-size: 0.9rem; line-height: 1.5; margin-bottom: 0.25rem; }
    .notif-body .notif-time { font-size: 0.78rem; color: var(--muted); }

    .notif-action form { display: inline; }

    .empty-state { text-align: center; padding: 4rem 2rem; color: var(--muted); background: white; border: 1px solid var(--border); border-radius: var(--radius); }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; }
</style>
@endpush

@section('content')
<div class="container notif-page">
    <div class="page-header">
        <div>
            <h1>Notifications</h1>
            <p style="color:var(--muted);font-size:0.9rem;">{{ $notifications->where('date_lecture', null)->count() }} non lues</p>
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
            $iconConfig = match($notif->type) {
                'candidature' => ['bg' => '#d1ecf1', 'color' => '#0c5460', 'icon' => 'fa-paper-plane'],
                'acceptee'    => ['bg' => '#d4edda', 'color' => '#155724', 'icon' => 'fa-check-circle'],
                'refusee'     => ['bg' => '#f8d7da', 'color' => '#721c24', 'icon' => 'fa-times-circle'],
                default       => ['bg' => '#fff3cd', 'color' => '#856404', 'icon' => 'fa-bell'],
            };
        @endphp

        <div class="notif-item {{ is_null($notif->date_lecture) ? 'unread' : '' }}">
            <div class="notif-icon" style="background:{{ $iconConfig['bg'] }};color:{{ $iconConfig['color'] }};">
                <i class="fas {{ $iconConfig['icon'] }}"></i>
            </div>
            <div class="notif-body">
                <p>{{ $notif->message }}</p>
                <span class="notif-time">{{ $notif->created_at->diffForHumans() }}</span>
            </div>
            @if(is_null($notif->date_lecture))
                <div class="notif-action">
                    <form method="POST" action="{{ route('notifications.lire', $notif->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-outline btn-sm" title="Marquer comme lu">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                </div>
            @else
                <span style="color:var(--muted);font-size:0.78rem;white-space:nowrap;">Lu</span>
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
@endsection
