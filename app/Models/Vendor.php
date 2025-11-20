<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'branding_color',
        'branding_font',
        'branding_logo',
        'user_id',
        'status',
    ];

    protected $appends = ['status_tr'];

    /**
     * Get the translated status attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function statusTr(): Attribute
    {
        return new Attribute(
            get: fn () => match ($this->status) {
                'pending' => 'Onay Bekliyor',
                'approved' => 'Onaylandı',
                'rejected' => 'Reddedildi',
                default => 'Bilinmiyor',
            },
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}