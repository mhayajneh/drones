<?php

namespace App\Repositories;

use App\Models\Drone;
use App\Models\DangerClassification;
use Illuminate\Database\Eloquent\Collection;

class DangerClassificationRepository
{
    public function __construct(
        private DangerClassification $model
    ) {}

    public function create(Drone $drone, string $reason, ?string $details = null): DangerClassification
    {
        return $this->model->create([
            'drone_id' => $drone->id,
            'reason' => $reason,
            'details' => $details,
            'is_resolved' => false,
            'detected_at' => now(),
        ]);
    }

    public function getUnresolvedForDrone(Drone $drone): Collection
    {
        return $this->model->where('drone_id', $drone->id)
            ->unresolved()
            ->get();
    }

    public function resolveAll(Drone $drone): int
    {
        return $this->model->where('drone_id', $drone->id)
            ->unresolved()
            ->update([
                'is_resolved' => true,
                'resolved_at' => now(),
            ]);
    }

    public function hasUnresolvedReason(Drone $drone, string $reason): bool
    {
        return $this->model->where('drone_id', $drone->id)
            ->where('reason', $reason)
            ->unresolved()
            ->exists();
    }
}
