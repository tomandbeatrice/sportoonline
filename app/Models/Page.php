<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'type',
        'status',
        'meta_title',
        'meta_description',
        'show_in_footer',
        'show_in_menu',
        'views',
    ];

    protected $casts = [
        'show_in_footer' => 'boolean',
        'show_in_menu' => 'boolean',
        'views' => 'integer',
    ];

    protected $attributes = [
        'type' => 'custom',
        'status' => 'published',
        'show_in_footer' => false,
        'show_in_menu' => false,
        'views' => 0,
    ];
}
