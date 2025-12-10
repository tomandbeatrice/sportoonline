<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'driver_id',
        'vehicle_id',
        'route_id',
        'booking_number',
        'pickup_location',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'dropoff_location',
        'dropoff_address',
        'dropoff_latitude',
        'dropoff_longitude',
        'pickup_datetime',
        'estimated_arrival',
        'actual_pickup_time',
        'actual_dropoff_time',
        'passengers',
        'luggage_count',
        'distance',
        'duration',
        'base_price',
        'extras_price',
        'total_price',
        'payment_method',
        'payment_status',
        'status',
        'notes',
        'passenger_name',
        'passenger_phone',
        'passenger_email',
        'flight_number',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'estimated_arrival' => 'datetime',
        'actual_pickup_time' => 'datetime',
        'actual_dropoff_time' => 'datetime',
        'cancelled_at' => 'datetime',
        'base_price' => 'decimal:2',
        'extras_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'pickup_latitude' => 'decimal:8',
        'pickup_longitude' => 'decimal:8',
        'dropoff_latitude' => 'decimal:8',
        'dropoff_longitude' => 'decimal:8',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            $transfer->booking_number = 'TRF-' . strtoupper(uniqid());
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function review()
    {
        return $this->morphOne(Review::class, 'reviewable');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('pickup_datetime', '>=', now())
            ->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('pickup_datetime', today());
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    public function startTrip()
    {
        $this->update([
            'status' => 'in_progress',
            'actual_pickup_time' => now(),
        ]);

        if ($this->driver) {
            $this->driver->update(['is_available' => false]);
        }
    }

    public function completeTrip()
    {
        $this->update([
            'status' => 'completed',
            'actual_dropoff_time' => now(),
        ]);

        if ($this->driver) {
            $this->driver->update(['is_available' => true]);
            $this->driver->incrementTrips();
        }
    }
}
