<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'image_url',
        'link_url',
        'open_new_tab',
        'position',
        'order',
        'start_date',
        'end_date',
        'description',
        'is_active',
        'views',
        'clicks',
    ];

    protected $casts = [
        'open_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'views' => 'integer',
        'clicks' => 'integer',
    ];

    protected $attributes = [
        'open_new_tab' => true,
        'is_active' => true,
        'views' => 0,
        'clicks' => 0,
        'order' => 0,
    ];
}
