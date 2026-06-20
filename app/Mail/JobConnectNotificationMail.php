<?php

namespace App\Mail;

use App\Models\Utilisateur;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobConnectNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Utilisateur $utilisateur,
        public string $type,
        public string $notificationMessage
    ) {
    }

    public function build()
    {
        return $this->subject('JobConnect - Nouvelle notification')
            ->view('emails.notification');
    }
}
