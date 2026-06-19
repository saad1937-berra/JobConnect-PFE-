<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public static function between(Utilisateur $first, Utilisateur $second): self
    {
        [$one, $two] = collect([$first->id, $second->id])->sort()->values()->all();

        return self::firstOrCreate(
            ['user_one_id' => $one, 'user_two_id' => $two],
            ['last_message_at' => now()]
        );
    }

    public function userOne()
    {
        return $this->belongsTo(Utilisateur::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(Utilisateur::class, 'user_two_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function otherParticipant(Utilisateur $user): ?Utilisateur
    {
        if ($this->user_one_id === $user->id) {
            return $this->userTwo;
        }

        if ($this->user_two_id === $user->id) {
            return $this->userOne;
        }

        return null;
    }

    public function hasParticipant(Utilisateur $user): bool
    {
        return in_array($user->id, [$this->user_one_id, $this->user_two_id], true);
    }
}
