<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    protected $table = 'candidatures';

    protected $fillable = [
        'particulier_id',
        'offre_id',
        'date',
        'statut',
        'commentaire',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    // Relations
    public function particulier()
    {
        return $this->belongsTo(Particulier::class, 'particulier_id');
    }

    public function offre()
    {
        return $this->belongsTo(Offre::class, 'offre_id');
    }
}
