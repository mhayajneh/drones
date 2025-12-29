<?php

namespace App\Repositories;

use App\Models\NoFlyZone;
use Illuminate\Database\Eloquent\Collection;

class NoFlyZoneRepository
{
    public function __construct(
        private NoFlyZone $model
    ) {}

    public function getActive(): Collection
    {
        return $this->model->active()->get();
    }

    public function findZonesContainingPoint(float $latitude, float $longitude): Collection
    {
        $zones = $this->getActive();

        return $zones->filter(function ($zone) use ($latitude, $longitude) {
            return $zone->isPointInside($latitude, $longitude);
        });
    }

    public function create(array $data): NoFlyZone
    {
        return $this->model->create($data);
    }
}
