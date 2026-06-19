<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'utilisateurs';

    protected $fillable = [
        'email',
        'pass',
        'nom',
        'prenom',
        'role',
        'date_inscription',
    ];

    protected $hidden = [
        'pass',
    ];

    protected $casts = [
        'date_inscription' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->pass;
    }

    // Relations
    public function particulier()
    {
        return $this->hasOne(Particulier::class, 'utilisateur_id');
    }

    public function entreprise()
    {
        return $this->hasOne(Entreprise::class, 'utilisateur_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'utilisateur_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversations()
    {
        return Conversation::where('user_one_id', $this->id)
            ->orWhere('user_two_id', $this->id);
    }

    public function unreadMessagesCount(): int
    {
        return Message::whereNull('read_at')
            ->where('sender_id', '!=', $this->id)
            ->whereHas('conversation', fn($q) => $q
                ->where('user_one_id', $this->id)
                ->orWhere('user_two_id', $this->id))
            ->count();
    }

    // Helpers
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isParticulier()
    {
        return $this->role === 'particulier';
    }

    public function isEntreprise()
    {
        return $this->role === 'entreprise';
    }
}
