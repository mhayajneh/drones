<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drone extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial',
        'latitude',
        'longitude',
        'height',
        'horizontal_speed',
        'vertical_speed',
        'elevation',
        'gear',
        'height_limit',
        'home_distance',
        'is_near_area_limit',
        'is_near_height_limit',
        'rc_lost_action',
        'rid_state',
        'rth_altitude',
        'storage',
        'total_flight_distance',
        'total_flight_sorties',
        'total_flight_time',
        'track_id',
        'wind_direction',
        'wind_speed',
        'is_online',
        'is_dangerous',
        'is_marked_safe',
        'last_seen_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'height' => 'decimal:2',
        'horizontal_speed' => 'decimal:2',
        'vertical_speed' => 'decimal:2',
        'storage' => 'array',
        'is_near_area_limit' => 'boolean',
        'is_near_height_limit' => 'boolean',
        'rid_state' => 'boolean',
        'is_online' => 'boolean',
        'is_dangerous' => 'boolean',
        'is_marked_safe' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function locationHistories(): HasMany
    {
        return $this->hasMany(LocationHistory::class);
    }

    public function dangerClassifications(): HasMany
    {
        return $this->hasMany(DangerClassification::class);
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeDangerous($query)
    {
        return $query->where('is_dangerous', true)
            ->where('is_marked_safe', false);
    }

    public function scopeSerialLike($query, string $serial)
    {
        return $query->where('serial', 'like', "%{$serial}%");
    }

    public function getCurrentSpeed(): float
    {
        return sqrt(
            pow($this->horizontal_speed ?? 0, 2) +
            pow($this->vertical_speed ?? 0, 2)
        );
    }
}
