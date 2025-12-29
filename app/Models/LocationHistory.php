<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'drone_id',
        'latitude',
        'longitude',
        'height',
        'horizontal_speed',
        'vertical_speed',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'height' => 'decimal:2',
        'horizontal_speed' => 'decimal:2',
        'vertical_speed' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function drone(): BelongsTo
    {
        return $this->belongsTo(Drone::class);
    }
}
