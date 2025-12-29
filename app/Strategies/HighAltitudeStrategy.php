<?php

namespace App\Strategies;

use App\Models\Drone;

class HighAltitudeStrategy implements DangerClassificationStrategy
{
    private float $threshold;

    public function __construct(?float $threshold = null)
    {
        $this->threshold = $threshold ?? config('drone.danger.height_threshold', 500);
    }

    public function isDangerous(Drone $drone): bool
    {
        if (!$drone->height) {
            return false;
        }
        return $drone->height > $this->threshold;
    }

    public function getReason(): string
    {
        return 'high_altitude';
    }

    public function getDetails(Drone $drone): ?string
    {
        if (!$this->isDangerous($drone)) {
            return null;
        }
        return sprintf(
            'Drone flying at %.2f meters, exceeding safe altitude limit of %.2f meters',
            $drone->height,
            $this->threshold
        );
    }
}
