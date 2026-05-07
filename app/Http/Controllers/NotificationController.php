<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Lister les notifications de l'utilisateur connecté
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    // Marquer une notification comme lue
    public function marquerLu(Request $request, $id)
    {
        $notification = Notification::where('utilisateur_id', $request->user()->id)
                                    ->findOrFail($id);
        $notification->marquerLu();

        return response()->json(['message' => 'Notification marquée comme lue.']);
    }

    // Marquer toutes comme lues
    public function marquerToutLu(Request $request)
    {
        $request->user()->notifications()
            ->whereNull('date_lecture')
            ->update(['date_lecture' => now()]);

        return response()->json(['message' => 'Toutes les notifications marquées comme lues.']);
    }

    // Envoyer une notification (usage interne / admin)
    public function envoyer(Request $request)
    {
        $request->validate([
            'utilisateur_id' => 'required|exists:utilisateurs,id',
            'type'           => 'required|string',
            'message'        => 'required|string',
        ]);

        $notification = Notification::create($request->only(['utilisateur_id', 'type', 'message']));

        return response()->json(['message' => 'Notification envoyée.', 'notification' => $notification], 201);
    }
}
