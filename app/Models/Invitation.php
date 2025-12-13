<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'code',
        'status',
        'expires_at',
        'accepted_at',
        'invited_user_id'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime'
    ];

    /**
     * Get the user who sent the invitation
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who accepted the invitation
     */
    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }
}
