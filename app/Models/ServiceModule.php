<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceModule extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'icon',
        'badge',
        'badge_class',
        'is_active',
        'order',
        'route',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];
}
