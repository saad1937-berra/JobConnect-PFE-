<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public const STATUSES = [
        'nouveau' => 'Nouveau',
        'en_cours' => 'En cours',
        'traite' => 'Traité',
        'rejete' => 'Rejeté',
    ];

    public function followUpMessage(): string
    {
        $progress = match ($this->status) {
            'en_cours' => 'Votre signalement est en cours d’examen.',
            'traite' => 'Le traitement de votre signalement est terminé.',
            'rejete' => 'Votre signalement a été rejeté.',
            default => 'Votre signalement a été reçu et attend son examen.',
        };

        return "Signalement #{$this->id}\nMotif : {$this->reason}\nStatut : "
            . (self::STATUSES[$this->status] ?? 'Nouveau')
            . "\n{$progress}\nTraitement : "
            . ($this->admin_note ?: 'Aucun traitement renseigné pour le moment.');
    }

    protected $fillable = [
        'conversation_id',
        'reporter_id',
        'reported_id',
        'reason',
        'status',
        'admin_note',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function reporter()
    {
        return $this->belongsTo(Utilisateur::class, 'reporter_id');
    }

    public function reported()
    {
        return $this->belongsTo(Utilisateur::class, 'reported_id');
    }
}
