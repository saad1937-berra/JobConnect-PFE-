<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Envoyer une notification à un utilisateur
     */
    public static function envoyer(int $utilisateurId, string $type, string $message): void
    {
        Notification::create([
            'utilisateur_id' => $utilisateurId,
            'type'           => $type,
            'message'        => $message,
        ]);
    }

    /**
     * Notifier l'entreprise qu'elle a reçu une nouvelle candidature
     */
    public static function nouvelleCandidature($candidature): void
    {
        $candidat  = $candidature->particulier->utilisateur;
        $offre     = $candidature->offre;
        $entreprise = $offre->entreprise->utilisateur;

        self::envoyer(
            $entreprise->id,
            'candidature',
            "{$candidat->prenom} {$candidat->nom} a postulé à votre offre « {$offre->titre} »."
        );
    }

    /**
     * Notifier le candidat que son statut a changé
     */
    public static function statutCandidature($candidature): void
    {
        $candidat = $candidature->particulier->utilisateur;
        $offre    = $candidature->offre;

        $messages = [
            'acceptee'   => "🎉 Félicitations ! Votre candidature pour « {$offre->titre} » a été acceptée.",
            'refusee'    => "Votre candidature pour « {$offre->titre} » n'a pas été retenue.",
            'en_cours'   => "Votre candidature pour « {$offre->titre} » est en cours d'examen.",
            'en_attente' => "Votre candidature pour « {$offre->titre} » est en attente de traitement.",
        ];

        $types = [
            'acceptee'   => 'acceptee',
            'refusee'    => 'refusee',
            'en_cours'   => 'en_cours',
            'en_attente' => 'info',
        ];

        $message = $messages[$candidature->statut] ?? "Votre candidature pour « {$offre->titre} » a été mise à jour.";
        $type    = $types[$candidature->statut]    ?? 'info';

        self::envoyer($candidat->id, $type, $message);
    }

    /**
     * Notifier le candidat qu'une nouvelle offre correspond à son profil
     */
    public static function nouvelleOffre($offre, int $particulierUserId): void
    {
        self::envoyer(
            $particulierUserId,
            'offre',
            "Nouvelle offre disponible : « {$offre->titre} » chez {$offre->entreprise->nom}."
        );
    }

    /**
     * Notifier l'admin qu'une nouvelle entreprise s'est inscrite
     */
    public static function nouvelleEntreprise($entreprise, $admins): void
    {
        foreach ($admins as $admin) {
            self::envoyer(
                $admin->id,
                'entreprise',
                "Nouvelle entreprise inscrite : {$entreprise->nom}. En attente de validation."
            );
        }
    }
}