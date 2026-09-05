<?php

namespace App\Mail;

use App\Models\Utilisateur;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Utilisateur $utilisateur,
        public string $verificationUrl
    ) {
    }

    public function build()
    {
        return $this->subject('JobConnect - Confirmez votre adresse email')
            ->view('emails.verify-email');
    }
}
