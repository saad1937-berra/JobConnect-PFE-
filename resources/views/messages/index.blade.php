@php
    $user = auth()->user();
    $layout = 'layouts.app';
    $section = 'content';

    if ($user?->isParticulier()) {
        $layout = 'layouts.particulier';
        $section = 'part-content';
    } elseif ($user?->isEntreprise()) {
        $layout = 'layouts.entreprise';
        $section = 'ent-content';
    } elseif ($user?->isAdmin()) {
        $layout = 'layouts.admin';
        $section = 'admin-content';
    }
@endphp

@extends($layout)
@section('title', 'Messages')

@push('styles')
    <style>
        .msg-page { max-width: 980px; margin: 0 auto; }
        .msg-header { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1.5rem; }
        .msg-header h1 { font-family: var(--font-head, var(--ent-font-head, sans-serif)); font-size:1.8rem; font-weight:800; margin:0; }
        .msg-header p { color: var(--muted, var(--ent-muted, #6b7280)); margin-top:.25rem; }
        .msg-list { display:flex; flex-direction:column; gap:.75rem; }
        .msg-row { display:flex; align-items:center; gap:1rem; padding:1rem 1.25rem; background:#fff; border:1px solid var(--border, var(--ent-border, #e5e7eb)); border-radius:12px; text-decoration:none; color:inherit; }
        .msg-row:hover { border-color: var(--accent, var(--ent-green, #1a4d3a)); box-shadow:0 4px 16px rgba(0,0,0,.06); }
        .msg-avatar { width:44px; height:44px; border-radius:50%; background:var(--accent, var(--ent-green, #1a4d3a)); color:white; display:flex; align-items:center; justify-content:center; font-weight:800; flex-shrink:0; }
        .msg-main { flex:1; min-width:0; }
        .msg-name { font-weight:800; margin-bottom:.2rem; }
        .msg-preview { color:var(--muted, var(--ent-muted, #6b7280)); font-size:.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .msg-meta { text-align:right; color:var(--muted, var(--ent-muted, #6b7280)); font-size:.78rem; flex-shrink:0; }
        .msg-empty { background:#fff; border:1px solid var(--border, var(--ent-border, #e5e7eb)); border-radius:12px; padding:3rem 1.5rem; text-align:center; color:var(--muted, var(--ent-muted, #6b7280)); }
        .msg-unread { display:inline-flex; min-width:22px; height:22px; border-radius:20px; align-items:center; justify-content:center; background:#ef4444; color:white; font-size:.72rem; font-weight:800; margin-top:.35rem; }
    </style>
@endpush

@section($section)
    <div class="msg-page">
        <div class="msg-header">
            <div>
                <h1>Messages</h1>
                <p>Conversations avec candidats, entreprises et administration.</p>
            </div>
        </div>

        <div class="msg-list">
            @forelse($conversations as $conversation)
                @php
                    $other = $conversation->otherParticipant(auth()->user());
                    $last = $conversation->lastMessage;
                    $isParticipant = $conversation->hasParticipant(auth()->user());
                    $unread = $isParticipant
                        ? $conversation->messages()
                            ->where('sender_id', '!=', auth()->id())
                            ->whereNull('read_at')
                            ->count()
                        : 0;
                    $displayName = $other
                        ? trim($other->prenom . ' ' . $other->nom)
                        : trim($conversation->userOne?->prenom . ' ' . $conversation->userOne?->nom) . ' / ' . trim($conversation->userTwo?->prenom . ' ' . $conversation->userTwo?->nom);
                    $displayRole = $other
                        ? $other->role
                        : ($conversation->userOne?->role . ' - ' . $conversation->userTwo?->role);
                    $initials = strtoupper(substr($displayName, 0, 1));
                @endphp

                <a href="{{ route('messages.show', $conversation->id) }}" class="msg-row">
                    <div class="msg-avatar">{{ $initials }}</div>
                    <div class="msg-main">
                        <div class="msg-name">{{ $displayName }} <span style="font-weight:600;color:var(--muted, #6b7280);">({{ $displayRole }})</span></div>
                        <div class="msg-preview">{{ $last?->body ?? 'Aucun message.' }}</div>
                    </div>
                    <div class="msg-meta">
                        <div>{{ $last?->created_at?->diffForHumans() ?? $conversation->created_at->diffForHumans() }}</div>
                        @if($unread > 0)
                            <span class="msg-unread">{{ $unread }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="msg-empty">
                    <i class="fas fa-comments" style="font-size:2rem;margin-bottom:.75rem;display:block;"></i>
                    Aucune conversation pour le moment.
                </div>
            @endforelse
        </div>
    </div>
@endsection
