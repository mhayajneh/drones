<?php

namespace App\Strategies;

use App\Models\Drone;

class HighSpeedStrategy implements DangerClassificationStrategy
{
    private float $threshold;

    public function __construct(?float $threshold = null)
    {
        $this->threshold = $threshold ?? config('drone.danger.speed_threshold', 10);
    }

    public function isDangerous(Drone $drone): bool
    {
        $speed = $drone->getCurrentSpeed();
        return $speed > $this->threshold;
    }

    public function getReason(): string
    {
        return 'high_speed';
    }

    public function getDetails(Drone $drone): ?string
    {
        if (!$this->isDangerous($drone)) {
            return null;
        }
        $speed = $drone->getCurrentSpeed();
        return sprintf(
            'Drone moving at %.2f m/s, exceeding safe speed limit of %.2f m/s (horizontal: %.2f m/s, vertical: %.2f m/s)',
            $speed,
            $this->threshold,
            $drone->horizontal_speed ?? 0,
            $drone->vertical_speed ?? 0
        );
    }
}
