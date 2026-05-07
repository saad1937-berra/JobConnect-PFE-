<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $table = 'entreprises';

    protected $fillable = [
        'utilisateur_id',
        'nom',
        'secteur',
        'description',
        'adresse',
        'site_web',
        'logo',
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
}
