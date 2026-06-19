<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = Conversation::with(['userOne', 'userTwo', 'lastMessage.sender'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at');

        if (!$user->isAdmin()) {
            $query->where(fn($q) => $q
                ->where('user_one_id', $user->id)
                ->orWhere('user_two_id', $user->id));
        }

        $conversations = $query->get()
            ->filter(fn(Conversation $conversation) => $this->canViewConversation($user, $conversation))
            ->values();

        return view('messages.index', compact('conversations'));
    }

    public function show($id)
    {
        $conversation = Conversation::with(['userOne', 'userTwo', 'messages.sender'])->findOrFail($id);
        $user = auth()->user();

        abort_unless($this->canViewConversation($user, $conversation), 403, 'Conversation non autorisee.');

        if ($conversation->hasParticipant($user)) {
            Message::where('conversation_id', $conversation->id)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $canReply = $this->canSendInConversation($user, $conversation);
        $canReport = $this->canReportConversation($user, $conversation);

        return view('messages.show', compact('conversation', 'canReply', 'canReport'));
    }

    public function store(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);
        $user = auth()->user();

        abort_unless($this->canSendInConversation($user, $conversation), 403, 'Conversation non autorisee.');

        $request->validate([
            'body' => 'required|string|max:3000',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'body'            => $request->body,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back()->with('success', 'Message envoye.');
    }

    public function start(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:utilisateurs,id',
            'body'    => 'nullable|string|max:3000',
        ]);

        $current = auth()->user();
        $other = Utilisateur::findOrFail($request->user_id);

        abort_unless($this->canStartConversation($current, $other), 403, 'Conversation non autorisee.');

        $conversation = Conversation::between($current, $other);

        $body = $request->body;

        if (!filled($body) && $current->isEntreprise() && $other->isParticulier()) {
            $body = 'Bonjour, votre profil nous interesse. Nous souhaitons echanger avec vous.';
        }

        if (filled($body)) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $current->id,
                'body'            => $body,
            ]);

            $conversation->update(['last_message_at' => now()]);
        }

        return redirect()->route('messages.show', $conversation->id);
    }

    public function report(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:2000',
        ]);

        $user = auth()->user();
        $conversation = Conversation::with(['userOne', 'userTwo'])->findOrFail($id);

        abort_unless($this->canReportConversation($user, $conversation), 403, 'Signalement non autorise.');

        $admin = Utilisateur::where('role', 'admin')->orderBy('id')->first();

        if (!$admin) {
            return back()->with('error', 'Aucun administrateur disponible pour recevoir le signalement.');
        }

        $other = $conversation->otherParticipant($user);
        $adminConversation = Conversation::between($user, $admin);
        $reason = filled($request->reason) ? $request->reason : 'Aucun motif detaille.';

        Message::create([
            'conversation_id' => $adminConversation->id,
            'sender_id' => $user->id,
            'body' => "Signalement de conversation #{$conversation->id}\nUtilisateur signale : {$other?->prenom} {$other?->nom} ({$other?->email})\nMotif : {$reason}",
        ]);

        $adminConversation->update(['last_message_at' => now()]);

        return redirect()
            ->route('messages.show', $adminConversation->id)
            ->with('success', "Signalement envoye a l'administrateur.");
    }

    private function canStartConversation(Utilisateur $current, Utilisateur $other): bool
    {
        if ($current->id === $other->id) {
            return false;
        }

        if ($current->isAdmin()) {
            return $other->isEntreprise() || $other->isParticulier();
        }

        if ($current->isEntreprise()) {
            return $other->isParticulier() || $other->isAdmin();
        }

        if ($current->isParticulier() && $other->isEntreprise()) {
            return $this->hasAcceptedCandidatureWithEntreprise($current, $other)
                || $this->enterpriseAlreadyContacted($other, $current);
        }

        return false;
    }

    private function canViewConversation(Utilisateur $user, Conversation $conversation): bool
    {
        $conversation->loadMissing(['userOne', 'userTwo']);

        if ($user->isAdmin()) {
            return true;
        }

        return $this->canSendInConversation($user, $conversation)
            || ($conversation->hasParticipant($user) && $this->conversationHasAdmin($conversation));
    }

    private function canSendInConversation(Utilisateur $user, Conversation $conversation): bool
    {
        $conversation->loadMissing(['userOne', 'userTwo']);

        if (!$conversation->hasParticipant($user)) {
            return false;
        }

        $other = $conversation->otherParticipant($user);

        if (!$other || $user->id === $other->id) {
            return false;
        }

        if ($user->isAdmin()) {
            return $other->isEntreprise() || $other->isParticulier();
        }

        if ($this->conversationHasAdmin($conversation)) {
            return $user->isEntreprise() || $user->isParticulier();
        }

        if ($user->isEntreprise() && $other->isParticulier()) {
            return true;
        }

        if ($user->isParticulier() && $other->isEntreprise()) {
            return $this->hasAcceptedCandidatureWithEntreprise($user, $other)
                || $this->enterpriseAlreadyContacted($other, $user, $conversation);
        }

        return false;
    }

    private function canReportConversation(Utilisateur $user, Conversation $conversation): bool
    {
        if (!$user->isEntreprise() || !$conversation->hasParticipant($user)) {
            return false;
        }

        $other = $conversation->otherParticipant($user);

        return $other?->isParticulier() === true;
    }

    private function conversationHasAdmin(Conversation $conversation): bool
    {
        $conversation->loadMissing(['userOne', 'userTwo']);

        return $conversation->userOne?->isAdmin() || $conversation->userTwo?->isAdmin();
    }

    private function enterpriseAlreadyContacted(Utilisateur $entrepriseUser, Utilisateur $particulierUser, ?Conversation $conversation = null): bool
    {
        if (!$conversation) {
            [$one, $two] = collect([$entrepriseUser->id, $particulierUser->id])->sort()->values()->all();
            $conversation = Conversation::where('user_one_id', $one)
                ->where('user_two_id', $two)
                ->first();
        }

        if (!$conversation) {
            return false;
        }

        return Message::where('conversation_id', $conversation->id)
            ->where('sender_id', $entrepriseUser->id)
            ->exists();
    }

    private function hasAcceptedCandidatureWithEntreprise(Utilisateur $particulierUser, Utilisateur $entrepriseUser): bool
    {
        $particulier = $particulierUser->particulier;
        $entreprise = $entrepriseUser->entreprise;

        if (!$particulier || !$entreprise) {
            return false;
        }

        return Candidature::where('particulier_id', $particulier->id)
            ->where('statut', 'acceptee')
            ->whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
            ->exists();
    }
}
