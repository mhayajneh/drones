<?php

namespace App\Strategies;

use App\Models\Drone;

interface DangerClassificationStrategy
{
    public function isDangerous(Drone $drone): bool;
    public function getReason(): string;
    public function getDetails(Drone $drone): ?string;
}
