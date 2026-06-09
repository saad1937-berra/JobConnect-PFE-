<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competance extends Model
{
    protected $table = 'competances';

    protected $fillable = [
        'nom',
        'description',
    ];

    public function particuliers()
    {
        return $this->belongsToMany(Particulier::class, 'particulier_competance', 'competance_id', 'particulier_id');
    }
    
    public function offres()
    {
        return $this->belongsToMany(Offre::class, 'offre_competance', 'competance_id', 'offre_id');
    }
}
