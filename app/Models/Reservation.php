<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'hotel_id',
        'room_id',
        'reservation_number',
        'check_in',
        'check_out',
        'guests',
        'total_nights',
        'price_per_night',
        'total_price',
        'status',
        'payment_status',
        'special_requests',
        'guest_name',
        'guest_email',
        'guest_phone',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'price_per_night' => 'decimal:2',
        'total_price' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            $reservation->reservation_number = 'RES-' . strtoupper(uniqid());
            $reservation->total_nights = $reservation->check_in->diffInDays($reservation->check_out);
            $reservation->total_price = $reservation->price_per_night * $reservation->total_nights;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('check_in', '>=', now()->toDateString())
            ->where('status', 'confirmed');
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }
}
