<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    protected $table = 'offres';

    protected $fillable = [
        'entreprise_id',
        'categorie_id',
        'titre',
        'description',
        'date_publication',
        'date_expiration',
        'contrat',
        'duree',
        'localisation',
        'niveau_etude',
        'statut',
        'salaire',
    ];

    protected $casts = [
        'date_publication' => 'datetime',
        'date_expiration'  => 'datetime',
    ];

    // Relations
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'offre_id');
    }

    public function particuliers()
    {
        return $this->belongsToMany(Particulier::class, 'particulier_offre', 'offre_id', 'particulier_id')
                    ->withPivot('type')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('statut', 'active');
    }

    public function scopeByCategorie($query, $categorieId)
    {
        return $query->where('categorie_id', $categorieId);
    }
}
