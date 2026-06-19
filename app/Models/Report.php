<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
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
