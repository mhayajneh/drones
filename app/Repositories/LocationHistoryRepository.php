<?php

namespace App\Repositories;

use App\Models\Drone;
use App\Models\LocationHistory;
use Illuminate\Database\Eloquent\Collection;

class LocationHistoryRepository
{
    public function __construct(
        private LocationHistory $model
    ) {}

    public function create(Drone $drone, array $data): LocationHistory
    {
        return $this->model->create([
            'drone_id' => $drone->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'height' => $data['height'],
            'horizontal_speed' => $data['horizontal_speed'] ?? null,
            'vertical_speed' => $data['vertical_speed'] ?? null,
            'recorded_at' => now(),
        ]);
    }

    public function getFlightPath(Drone $drone, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = $this->model->where('drone_id', $drone->id)
            ->orderBy('recorded_at', 'asc');

        if ($startDate) {
            $query->where('recorded_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('recorded_at', '<=', $endDate);
        }

        return $query->get();
    }

    public function getRecentLocations(Drone $drone, int $limit = 100): Collection
    {
        return $this->model->where('drone_id', $drone->id)
            ->orderBy('recorded_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function cleanup(int $daysOld = 30): int
    {
        return $this->model->where('recorded_at', '<', now()->subDays($daysOld))
            ->delete();
    }
}
