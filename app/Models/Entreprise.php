<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $table = 'entreprises';

    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_VALIDEE = 'validee';
    public const STATUT_REFUSEE = 'refusee';

    protected $fillable = [
        'utilisateur_id',
        'nom',
        'secteur',
        'description',
        'adresse',
        'site_web',
        'logo',
        'statut_validation',
    ];

    // Relations
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function offres()
    {
        return $this->hasMany(Offre::class, 'entreprise_id');
    }

    public function scopeActiveAccount($query)
    {
        return $query
            ->where('statut_validation', self::STATUT_VALIDEE)
            ->whereHas('utilisateur', fn($q) => $q
                ->where('role', 'entreprise')
                ->whereNotNull('email_verified_at'));
    }

    public function isValidee(): bool
    {
        return $this->statut_validation === self::STATUT_VALIDEE;
    }

    public function isEnAttente(): bool
    {
        return $this->statut_validation === self::STATUT_EN_ATTENTE;
    }

    public function isRefusee(): bool
    {
        return $this->statut_validation === self::STATUT_REFUSEE;
    }

    public function emailVerifie(): bool
    {
        return (bool) $this->utilisateur?->hasVerifiedEmail();
    }

    public function peutPublier(): bool
    {
        return $this->isValidee() && $this->emailVerifie();
    }
}
