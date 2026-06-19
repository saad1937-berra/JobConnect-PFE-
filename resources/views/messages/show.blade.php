@php
    $user = auth()->user();
    $layout = 'layouts.app';
    $section = 'content';
    $other = $conversation->otherParticipant($user);
    $isParticipant = $conversation->hasParticipant($user);
    $title = $other
        ? trim($other->prenom . ' ' . $other->nom)
        : 'Conversation surveillee';
    $subtitle = $other
        ? ($other->email . ' - ' . $other->role)
        : trim($conversation->userOne?->prenom . ' ' . $conversation->userOne?->nom) . ' (' . $conversation->userOne?->role . ') / ' . trim($conversation->userTwo?->prenom . ' ' . $conversation->userTwo?->nom) . ' (' . $conversation->userTwo?->role . ')';

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
@section('title', 'Conversation')

@push('styles')
    <style>
        .chat-page { max-width: 920px; margin:0 auto; }
        .chat-top { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1rem; }
        .chat-title h1 { font-family:var(--font-head, var(--ent-font-head, sans-serif)); font-size:1.45rem; font-weight:800; margin:0; }
        .chat-title p { color:var(--muted, var(--ent-muted, #6b7280)); margin-top:.2rem; }
        .chat-box { background:#fff; border:1px solid var(--border, var(--ent-border, #e5e7eb)); border-radius:12px; overflow:hidden; }
        .chat-messages { padding:1.25rem; display:flex; flex-direction:column; gap:.8rem; max-height:58vh; overflow-y:auto; background:#fafafa; }
        .chat-message { max-width:72%; padding:.8rem 1rem; border-radius:14px; border:1px solid var(--border, var(--ent-border, #e5e7eb)); background:white; }
        .chat-message.mine { align-self:flex-end; background:var(--accent, var(--ent-green, #1a4d3a)); color:white; border-color:transparent; }
        .chat-message .body { line-height:1.5; white-space:pre-wrap; }
        .chat-message .date { margin-top:.35rem; font-size:.72rem; opacity:.7; }
        .chat-form { padding:1rem; border-top:1px solid var(--border, var(--ent-border, #e5e7eb)); display:flex; gap:.75rem; background:white; }
        .chat-form textarea { flex:1; min-height:54px; max-height:140px; resize:vertical; border:1px solid var(--border, var(--ent-border, #e5e7eb)); border-radius:10px; padding:.75rem; font:inherit; }
        .chat-btn { border:0; border-radius:10px; padding:.75rem 1.15rem; background:var(--accent, var(--ent-green, #1a4d3a)); color:white; font-weight:800; cursor:pointer; }
        .chat-back { color:inherit; text-decoration:none; font-weight:700; }
        .chat-advice { margin:1rem; padding:.85rem 1rem; border-radius:10px; background:#fff7ed; border:1px solid #fed7aa; color:#7c2d12; font-size:.88rem; line-height:1.55; }
        .chat-readonly { padding:1rem; border-top:1px solid var(--border, var(--ent-border, #e5e7eb)); color:var(--muted, #6b7280); background:white; text-align:center; }
        .chat-report { margin-top:.75rem; }
        .chat-report summary { cursor:pointer; color:#b91c1c; font-weight:800; }
        .chat-report form { margin-top:.6rem; display:flex; gap:.6rem; align-items:flex-start; }
        .chat-report textarea { min-height:42px; resize:vertical; border:1px solid #fecaca; border-radius:8px; padding:.6rem; font:inherit; flex:1; }
        .chat-report button { border:0; border-radius:8px; padding:.65rem .9rem; background:#dc2626; color:#fff; font-weight:800; cursor:pointer; }
    </style>
@endpush

@section($section)
    <div class="chat-page">
        <div class="chat-top">
            <div class="chat-title">
                <a href="{{ route('messages.index') }}" class="chat-back"><i class="fas fa-arrow-left"></i> Messages</a>
                <h1>{{ $title }}</h1>
                <p>{{ $subtitle }}</p>
                @if($canReport)
                    <details class="chat-report">
                        <summary><i class="fas fa-flag"></i> Signaler cette conversation a l'admin</summary>
                        <form method="POST" action="{{ route('messages.report', $conversation->id) }}">
                            @csrf
                            <textarea name="reason" placeholder="Expliquez rapidement le probleme...">{{ old('reason') }}</textarea>
                            <button type="submit">Signaler</button>
                        </form>
                    </details>
                @endif
            </div>
        </div>

        <div class="chat-box">
            <div class="chat-messages" id="chatMessages">
                @forelse($conversation->messages as $message)
                    <div class="chat-message {{ $message->sender_id === auth()->id() ? 'mine' : '' }}">
                        <div class="body">{{ $message->body }}</div>
                        <div class="date">{{ $message->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                @empty
                    <div style="text-align:center;color:var(--muted, #6b7280);padding:2rem;">Aucun message.</div>
                @endforelse
            </div>

            @if($canReply)
                <div class="chat-advice">
                    Conseils pro : soyez clair et respectueux, restez dans le cadre du recrutement, evitez les donnees sensibles et signalez tout abus.
                </div>
                <form method="POST" action="{{ route('messages.store', $conversation->id) }}" class="chat-form">
                    @csrf
                    <textarea name="body" placeholder="Ecrire un message professionnel..." required>{{ old('body') }}</textarea>
                    <button type="submit" class="chat-btn"><i class="fas fa-paper-plane"></i></button>
                </form>
            @else
                <div class="chat-readonly">
                    Lecture seule. Vous pouvez consulter cette conversation, mais pas y envoyer de message.
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
    </script>
@endpush
