<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'utilisateur_id',
        'type',
        'message',
        'date_lecture',
    ];

    protected $casts = [
        'date_lecture' => 'datetime',
    ];

    // Relations
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    // Helpers
    public function isLue()
    {
        return $this->date_lecture !== null;
    }

    public function marquerLu()
    {
        $this->update(['date_lecture' => now()]);
    }
}
