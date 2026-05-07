<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'nom',
        'description',
    ];

    public function offres()
    {
        return $this->hasMany(Offre::class, 'categorie_id');
    }
}
