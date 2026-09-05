<?php

namespace App\Services;

use App\Mail\VerifyEmailMail;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class EmailVerificationService
{
    public static function send(Utilisateur $utilisateur): void
    {
        if ($utilisateur->hasVerifiedEmail()) {
            return;
        }

        Mail::to($utilisateur->email)->send(new VerifyEmailMail(
            $utilisateur,
            self::verificationUrl($utilisateur)
        ));
    }

    public static function verificationUrl(Utilisateur $utilisateur): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $utilisateur->id,
                'hash' => sha1($utilisateur->email),
            ]
        );
    }
}
