<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationWebController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function marquerLu($id)
    {
        Notification::where('utilisateur_id', auth()->id())
            ->findOrFail($id)
            ->marquerLu();

        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function marquerToutLu()
    {
        auth()->user()
            ->notifications()
            ->whereNull('date_lecture')
            ->update(['date_lecture' => now()]);

        return back()->with('success', 'Toutes les notifications marquées comme lues.');
    }
}
