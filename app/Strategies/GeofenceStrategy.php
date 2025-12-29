<?php

namespace App\Strategies;

use App\Models\Drone;
use App\Repositories\NoFlyZoneRepository;

class GeofenceStrategy implements DangerClassificationStrategy
{
    public function __construct(
        private NoFlyZoneRepository $noFlyZoneRepository
    ) {}

    public function isDangerous(Drone $drone): bool
    {
        if (!$drone->latitude || !$drone->longitude) {
            return false;
        }
        $zones = $this->noFlyZoneRepository->findZonesContainingPoint(
            $drone->latitude,
            $drone->longitude
        );
        return $zones->isNotEmpty();
    }

    public function getReason(): string
    {
        return 'geofence_violation';
    }

    public function getDetails(Drone $drone): ?string
    {
        if (!$this->isDangerous($drone)) {
            return null;
        }
        $zones = $this->noFlyZoneRepository->findZonesContainingPoint(
            $drone->latitude,
            $drone->longitude
        );
        $zoneNames = $zones->pluck('name')->implode(', ');
        return sprintf(
            'Drone entered restricted no-fly zone(s): %s at coordinates (%.6f, %.6f)',
            $zoneNames,
            $drone->latitude,
            $drone->longitude
        );
    }
}
