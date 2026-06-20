<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Particulier extends Model
{
    protected $table = 'particuliers';

    protected $fillable = [
        'utilisateur_id',
        'bio',
        'tel',
        'adresse',
        'date_naissance',
        'niveau_etude',
        'photo',
        'cv_titre',
        'cv_experiences',
        'cv_formations',
        'cv_langues',
        'cv_loisirs',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'cv_experiences' => 'array',
        'cv_formations' => 'array',
        'cv_langues' => 'array',
        'cv_loisirs' => 'array',
    ];

    // Relations
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function cv()
    {
        return $this->hasMany(Cv::class, 'particulier_id');
    }

    public function competances()
    {
        return $this->belongsToMany(Competance::class, 'particulier_competance', 'particulier_id', 'competance_id')
                    ->withPivot('niveau');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'particulier_id');
    }

    public function offres()
    {
        return $this->belongsToMany(Offre::class, 'particulier_offre', 'particulier_id', 'offre_id')
                    ->withPivot('type')
                    ->withTimestamps();
    }
}
