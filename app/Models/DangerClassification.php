<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DangerClassification extends Model
{
    use HasFactory;

    protected $fillable = [
        'drone_id',
        'reason',
        'details',
        'is_resolved',
        'detected_at',
        'resolved_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function drone(): BelongsTo
    {
        return $this->belongsTo(Drone::class);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }
}
